<?php

namespace eTextBook\LoungeBundle\Lib;

use Gedmo\Sluggable\Util as Sluggable;
use eTextBook\LoungeBundle\Lib\Transliterate;
use eTextBook\LoungeBundle\Lib\FileManager;

class BookPacker {

    private $currentBookSlug;

    public function createBookPack($bookData) {
        global $kernel;
        $transliterate = new Transliterate();
        $this->currentBookSlug = Sluggable\Urlizer::urlize($transliterate->transliterate($bookData['title'], 'ru'), '-');

        $this->createDumpDirs();
        $this->copyTemplateFiles();

        $bookData['modules'] = array();
        file_put_contents($this->getDirPath('') . 'book.info', json_encode($bookData));

        return $this->packDump();
    }

    public function repackBook($bookSlug) {
        $this->setCurrentBookSlug($bookSlug);
        $this->createDumpDirs();
        $this->copyTemplateFiles();
        $this->copyTmpFiles();
        $this->packDump();
    }

    public function packDump() {
        global $kernel;
        $booksDir = $kernel->getContainer()->getParameter('books_dir');
        $fileManager = $kernel->getContainer()->get('fileManager');
        $archivePath = $booksDir . $this->currentBookSlug . ".etb";
        if(is_file($archivePath)) { unlink($archivePath); }
        $fileManager->zip($this->getDirPath(''), $archivePath);
        $fileManager->removeDir($booksDir . $this->currentBookSlug);
        return $archivePath;
    }

    public function copyTmpFiles() {
        global $kernel;
        $fileManager = $kernel->getContainer()->get('fileManager');
        $tmpDir = $kernel->getContainer()->getParameter('book_tmp_dir') . $this->currentBookSlug;
        $fileManager->copyFilesFromDirectory($tmpDir . '/content/img', $this->getContentPath('img'));
        $fileManager->copyFilesFromDirectory($tmpDir . '/content/audio', $this->getContentPath('audio'));
        $fileManager->copyFilesFromDirectory($tmpDir . '/content/video', $this->getContentPath('video'));
        $fileManager->copyFilesFromDirectory($tmpDir . '/modules', $this->getDirPath('modules'));
        copy($tmpDir . '/book.info', $this->getDirPath('') . 'book.info');
    }

    public function getDirPath($dirName) {
        global $kernel;
        $booksDir = $kernel->getContainer()->getParameter('books_dir');
        $bookDir = $booksDir . $this->currentBookSlug;
        if(!is_dir($bookDir)) { mkdir($bookDir); }
        return $bookDir . '/' . $dirName;
    }

    public function getContentPath($dirName) {
        global $kernel;
        $booksDir = $kernel->getContainer()->getParameter('books_dir');
        $bookDir = $booksDir . $this->currentBookSlug;
        if(!is_dir($bookDir)) { mkdir($bookDir); }
        $contentDir = $bookDir . '/content';
        if(!is_dir($contentDir)) { mkdir($contentDir); }
        return $contentDir . '/' . $dirName;
    }

    public function createDumpDirs() {
        mkdir($this->getDirPath('css'));
        mkdir($this->getDirPath('js'));
        mkdir($this->getDirPath('img'));
        mkdir($this->getDirPath('fonts'));
        mkdir($this->getDirPath('modules'));
        mkdir($this->getContentPath('video'));
        mkdir($this->getContentPath('audio'));
        mkdir($this->getContentPath('img'));
    }

    public function copyTemplateFiles() {
        global $kernel;
        $templateDir = $kernel->getContainer()->getParameter('book_template_dir');
        $fileManager = $kernel->getContainer()->get('fileManager');

        $fileManager->copyFilesFromDirectory($templateDir . "/css", $this->getDirPath('css'));
        $fileManager->copyFilesFromDirectory($templateDir . "/js", $this->getDirPath('js'));
        $fileManager->copyFilesFromDirectory($templateDir . "/fonts", $this->getDirPath('fonts'));
        $fileManager->copyFilesFromDirectory($templateDir . "/img", $this->getDirPath('img'));
    }

    public function setCurrentBookSlug($currentBookSlug) {
        $this->currentBookSlug = $currentBookSlug;
    }

}