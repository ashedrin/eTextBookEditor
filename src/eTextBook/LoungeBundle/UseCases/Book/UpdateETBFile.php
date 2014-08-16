<?php

namespace eTextBook\LoungeBundle\UseCases\Book;

use eTextBook\LoungeBundle\Entity\Book;
use eTextBook\LoungeBundle\Lib\SummaryDom;

class UpdateETBFile
{
    private $book;
    private $tmpDir;
    private $bookTmpDir;

    public function __construct()
    {
        global $kernel;
        $this->tmpDir = $kernel->getContainer()->getParameter('book_tmp_dir');
        $this->templateDir = $kernel->getContainer()->getParameter('book_template_dir');
    }

    public function setBook(Book $book)
    {
        $this->book = $book;
        $this->bookTmpDir = $this->tmpDir . $this->book->getSlug() . '/';
    }

    public function updateModuleContent($bookName, $moduleSlug, $content)
    {
        $moduleFilePath = $this->tmpDir . $bookName . '/modules/' . $moduleSlug . '.html';
        $moduleContent = new SummaryDom();
        $moduleContent->loadWithBreaks($moduleFilePath);
        $moduleContent->find('.e-text-book-viewer', 0)->innertext = $content;
//        $content = $moduleContent->find('.e-text-book-viewer', 0)->innertext;
        $exercisesIDList = $moduleContent->getExercisesList();
        $moduleContent->save($moduleFilePath);

        $indexFilePath = $this->tmpDir . $bookName . '/index.html';
        $indexContent = new SummaryDom();
        $indexContent->loadWithBreaks($indexFilePath);
        $indexContent->getChapter($moduleSlug)->insertExercisesIntoChapter($exercisesIDList);
        $indexContent->tidySave($indexFilePath);
        $indexContent->destroy();

        $moduleContent->destroy();
//        $this->addExercisesToSummary($bookName, $moduleSlug, $exercisesIDList);

    }

    public function addExercisesToSummary($bookName, $moduleSlug, $exercisesIDList)
    {
    }

    public function addModule($moduleTitle)
    {
        $moduleSlug = date('d-m-y-H-i-s');
        $bookInfo = $this->getBookInfo();
        $bookInfo->modules[] = array('title' => $moduleTitle, 'slug' => $moduleSlug);
        $this->setBookInfo($bookInfo);

        $this->createModuleFile($moduleTitle, $moduleSlug);
        $this->updateBookSummary($moduleTitle, $moduleSlug);
        $this->pack();

        return $moduleSlug;
    }

    public function getBookInfo()
    {
        return json_decode(file_get_contents($this->tmpDir . $this->book->getSlug() . '/book.info'));
    }

    public function setBookInfo($data)
    {
        file_put_contents($this->tmpDir . $this->book->getSlug() . '/book.info', json_encode($data));
    }

    private function createModuleFile($moduleTitle, $moduleSlug)
    {
        $moduleFilePath = $this->tmpDir . $this->book->getSlug() . '/modules/' . $moduleSlug . '.html';
        $moduleContent = new SummaryDom();
        $moduleContent->loadWithBreaks($this->templateDir . "/moduleTemplate.html");
        $moduleContent->setTitle($moduleTitle);
        $moduleContent->setModuleTitle($moduleTitle);
        $moduleContent->save($moduleFilePath);
        $moduleContent->destroy();
    }

    private function updateBookSummary($moduleTitle, $moduleSlug)
    {
        $indexFilePath = $this->tmpDir . $this->book->getSlug() . '/index.html';
        $summaryTemplate = file_get_contents($this->templateDir . "/summaryLinkTemplate.html");

        if (file_exists($indexFilePath)) {
            $summaryContent = $this->createSummary($moduleTitle, $moduleSlug, $summaryTemplate);
            $indexTemplate = file_get_contents($this->tmpDir . $this->book->getSlug() . '/index.html');
        } else {
            $indexTemplate = file_get_contents($this->templateDir . "/index.html");
            $summaryContent = $this->getBookSummaryFromInfo($summaryTemplate);
        }

        $this->fillIndexFile($indexTemplate, $summaryContent, $indexFilePath);
    }

    private function getBookSummaryFromInfo($summaryTemplate)
    {
        $info = json_decode(file_get_contents($this->bookTmpDir . 'book.info'), true);
        $summaryContent = $summaryTemplate;
        foreach ($info['modules'] as $module) {
            $summaryContent = $this->createSummary($module['title'], $module['slug'], $summaryContent);
            if ($module != end($info['modules'])) {
                $summaryContent .= $summaryTemplate;
            }
        }

        return $summaryContent;
    }

    private function createSummary($moduleTitle, $moduleSlug, $summaryTemplate)
    {
        $summaryContent = new SummaryDom();
        $summaryContent->loadWithBreaks($summaryTemplate);
        $summaryContent->setChapterAttributes($moduleSlug, $moduleTitle);
        $result = $summaryContent->outertext;
        $summaryContent->destroy();

        return $result;
    }

    private function fillIndexFile($indexTemplate, $summaryContent, $indexPath)
    {
        $indexContent = new SummaryDom();
        $indexContent->loadWithBreaks($indexTemplate);
        $indexContent->setBookAttributes($this->book->getTitle());
        $indexContent->setSummaryList($summaryContent);
        $indexContent->save($indexPath);
        $indexContent->destroy();
    }

    public function execute()
    {
        $this->updateInfoFile();
        $this->pack();
    }

    public function updateInfoFile()
    {
        $info = json_decode(file_get_contents($this->bookTmpDir . 'book.info'));
        $info->title = $this->book->getTitle();
        $info->authors = $this->book->getAuthors();
        $info->editor = $this->book->getEditor();
        $info->isbn = $this->book->getIsbn();
        file_put_contents($this->bookTmpDir . 'book.info', json_encode($info));
    }

    public function pack()
    {
        global $kernel;
        $booksDir = $kernel->getContainer()->getParameter('books_dir');
        $fileManager = $kernel->getContainer()->get('fileManager');
        $bookFile = $booksDir . $this->book->getSlug() . '.etb';
        if (is_file($bookFile)) {
            unlink($bookFile);
        }
        $fileManager->zip($this->bookTmpDir, $bookFile);
    }

}