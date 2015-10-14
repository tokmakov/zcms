<?php
/**
 * Класс Admin_Backend_Model для авторизации администратора сайта,
 * административная часть сайта
 */
class Admin_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Авторизация администратора сайта
     */
    public function loginAdmin($name, $password) {
        if ($name == $this->config->admin->name && $password == $this->config->admin->password) {
            $_SESSION['zcmsAuthAdmin'] = true;
            return true;
        }
        return false;
    }

    /**
     * Выход администратора сайта
     */
    public function logoutAdmin() {
        if (isset($_SESSION['zcmsAuthAdmin'])) {
            unset($_SESSION['zcmsAuthAdmin']);
        }
    }

    /**
     * Администратор сайта авторизован?
     */
    public function isAuthAdmin() {
        return isset($_SESSION['zcmsAuthAdmin']);
    }

}