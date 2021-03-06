<?php

use Features\Bootstrap\eTextBookContext;

require_once dirname(__FILE__) . '/../../vendor/phpunit/phpunit/PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';


/**
 * Features context.
 */
class FileManagerContext extends eTextBookContext
{

    private $manager;

    private function getManager()
    {
        if (!$this->manager) {
            $this->manager = $this->findCss('.file-manager');
        }
        return $this->manager;
    }

    /**
     * @Given /^Загружаем картинку "([^"]*)" в менеджер файлов из фикстур$/
     */
    public function uploadImage($imageFileName)
    {
        $uploadButton = $this->getManager()->find('css', '.upload-container input');
        $uploadButton->attachFile(dirname(__FILE__) . '/../../fixtures/img/' . $imageFileName);
    }

    /**
     * @Given /^Загружаем видео "([^"]*)" в менеджер файлов из фикстур$/
     */
    public function uploadVideo($videoFileName)
    {
        $uploadButton = $this->getManager()->find('css', '.upload-container input');
        $uploadButton->attachFile(dirname(__FILE__) . '/../../fixtures/video/' . $videoFileName);
    }

    /**
     * @Given /^Загружаем аудио "([^"]*)" в менеджер файлов из фикстур$/
     */
    public function uploadAudio($audioFileName)
    {
        $uploadButton = $this->getManager()->find('css', '.upload-container input');
        $uploadButton->attachFile(dirname(__FILE__) . '/../../fixtures/audio/' . $audioFileName);
    }

    /**
     * @Given /^Выбираем последнию картинку из списка в менеджере файлов$/
     */
    public function selectFirstImageFromFileManager()
    {
        $lastImage = $this->getManager()->findAll('css', '#images .list .item');
        $lastImage = end($lastImage);
        $lastImage->click();
        $this->getManager()->find('css', '#images .player .buttons .select')->click();
    }

    /**
     * @Given /^Выбираем последнее видео из списка в менеджере файлов$/
     */
    public function selectFirstVideoFromFileManager()
    {
        $lastVideo = $this->getManager()->findAll('css', '#videos .list .item');
        $lastVideo = end($lastVideo);
        $lastVideo->click();
        $this->getManager()->find('css', '#videos .player .buttons .select')->click();
    }

    /**
     * @Given /^Выбираем последнее аудио из списка в менеджере файлов$/
     */
    public function selectFirstAudioFromFileManager()
    {
        $lastAudio = $this->getManager()->findAll('css', '#audios .list .item');
        $lastAudio = end($lastAudio);
        $lastAudio->click();
        $this->getManager()->find('css', '#audios .player .buttons .select')->click();
    }


    /**
     * @Given /^Переходим во вкладку с ссылкой "([^"]*)"$/
     */
    public function setVideoTab($tabHref)
    {
        $this->getManager()->find('css', '.nav a[href="' . $tabHref . '"]')->click();
    }
}