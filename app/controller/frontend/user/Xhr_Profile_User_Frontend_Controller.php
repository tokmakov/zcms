<?php
/**
 * Класс Xhr_Profile_User_Frontend_Controller формирует ответ на запрос XmlHttpRequest в
 * формате JSON, получает данные от модели User_Frontend_Model, общедоступная часть сайта.
 * Ответ содержит профиль зарегистрированного и авторизованного пользователя
 */
class Xhr_Profile_User_Frontend_Controller extends User_Frontend_Controller {

    /**
     * один из профилей пользователя
     */
    private $profile = array();

    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // если пользователь не авторизован
        if ( ! $this->authUser) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        // если не передан id профиля или id профиля не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        $profile = $this->userFrontendModel->getProfile($this->params['id']);
        if ( ! empty($profile)) {
            $this->profile = $profile;
        }

    }

    public function sendHeaders() {
        header('Content-type: application/json; charset=utf-8');
    }

    public function getPageContent() {
        return json_encode($this->profile);
    }

}