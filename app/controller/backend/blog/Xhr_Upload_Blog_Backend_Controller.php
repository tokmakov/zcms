<?php
/**
 * Класс Xhr_Upload_Blog_Backend_Controller принимает запрос XmlHttpRequest, загружает
 * файлы, работает с моделью Blog_Backend_Model, ответ содержит спискок всех файлов блога
 * за последний месяц в формате html, административная часть сайта
 */
class Xhr_Upload_Blog_Backend_Controller extends Blog_Backend_Controller {

    public function __construct($params = null) {
        if ( ! $this->isPostMethod()) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }
        parent::__construct($params);
    }

    public function request() {

        // загружаем файл(ы)
        $this->blogBackendModel->uploadFiles();

        // получаем от модели массив массив директорий и файлов
        $folders = $this->blogBackendModel->getFoldersAndFiles(1);
        
        $files = array_shift($folders);

        // получаем html-код списка директорий и файлов
        $this->pageContent = $this->render(
            $this->config->site->theme . '/backend/template/blog/xhr/upload.php',
            array(
                'files' => $files,
            )
        );
    }

}