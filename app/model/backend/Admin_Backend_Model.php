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
        $denied = 'files/admin/denied.txt';
        // через час разблокируем вход в панель управления
        if (is_file($denied) && (time() - filemtime($denied) > 3600)) {
            unlink($denied);
        }
        // если существует файл denied.txt, вход в панель управления заблокирован
        if (is_file($denied)) {
            return false;
        }
        if ($name == $this->config->admin->name && $password == $this->config->admin->password) {
            $_SESSION['zcmsAuthAdmin'] = true;
            return true;
        }
        // после пяти неудачных попыток войти, вход в панель управления блокируется
        $access = 'files/admin/access.txt';
        if ( ! is_file($access)) { // на всякий случай, вдруг файл access.txt пропадет
            file_put_contents($access, '1;'.time(), LOCK_EX);
            return false;
        }
        list($count, $time) = explode(';', file_get_contents($access));
        // первая неудачная попытка
        if (time() - $time > 60) {
            file_put_contents($access, '1;'.time(), LOCK_EX);
            return false;
        }
        $count++;
        if ($count < 5) { // вторая, третья, четвертая попытка
            file_put_contents($access, $count.';'.time(), LOCK_EX);
        } else { // пятая неудачная попытка, блокируем вход в панель управления
            file_put_contents($denied, '');
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