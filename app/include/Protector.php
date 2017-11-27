<?php
/**
 * Класс Protector защищает сайт от чрезмерно активных пользователей,
 * программных роботов, некоторых категорий DDoS-атак
 */
class Protector extends Base {

    /*
     * ip-адрес пользователя, который проверяем
     */
    private $ip;

    /*
     * время блокировки ip-адреса в секундах
     */
    private $time;


    public function __construct() {

        // не учитываем запросы с использованием XmlHttpRequest
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return;
        }

        // получаем ip-адрес
        if (!empty($_SERVER['REMOTE_ADDR']) && preg_match('~^\d{1,3}(\.\d{1,3}){3}$~', $_SERVER['REMOTE_ADDR'])) {
            $this->ip = $_SERVER['REMOTE_ADDR'];
        } else {
            return;
        }

        /*
         * вызываем конструктор родительского класса, чтобы иметь доступ
         * к настройкам сайта и выполнять запросы к базе данных
         */
        parent::__construct();

        // не блокируем ip-адреса из «белого» списка
        if (in_array($this->ip, $this->config->protector->getValue('white'))) {
            return;
        }

        // время блокировки ip-адреса в секундах
        $this->time = $this->config->protector->time;

        /*
         * Мы должны сделать:
         * 1. Проверить, не заблокирован ли ip-адрес этого посетителя
         * 2. Если заблокирован, сообщаем пользователю, что его ip-адрес заблокирован на … секунд
         * 3. Если не заблокирован,
         *    3.1. Записать ip-адрес в таблицу БД, чтобы накапливать статистику
         *    3.2. Заблокировать те ip-адреса, с которых идет слишком много запросов
         *    3.3. Удалить старые записи из таблиц `protect_ban` и `protect_ips`
         */

        /*
         * 1. проверяем, не заблокирован ли ip-адрес этого посетителя
         */
        $ban = $this->checkIpAddress();
        if ( ! $ban) { // не заблокирован
            /*
             * 3.1. записываем ip-адрес в таблицу БД, чтобы накапливать статистику
             */
            $this->addRecord();
            /*
             * 3.2. блокируем те ip-адреса, с которых идет слишком много запросов
             */
            $this->lockIpAddresses();
            /*
             * 3.3. удаляем старые записи из таблиц `protect_ban` и `protect_ips`
             */
            if (rand(1,100) == 50) {
                $this->removeOldRecords();
            }
            return;
        }

        /*
         * 2. сообщаем пользователю, что его ip-адрес заблокирован на … секунд
         */
        $this->showMessage();

    }

    /**
     * Функция проверяет, не заблокирован ли ip-адрес этого посетителя
     */
    private function checkIpAddress() {

        $query = "SELECT
                      true
                  FROM
                      `protect_ban`
                  WHERE
                      `ip` = :ip AND `added` > NOW() - INTERVAL :time SECOND";
        return $this->database->fetchOne($query, array('ip' => $this->ip, 'time' => $this->time));

    }

    /**
     * Функция сообщает пользователю, что его ip-адрес заблокирован на … секунд
     */
    private function showMessage() {

        // получаем html-код страницы
        ob_start();
        $ip = $this->ip;
        $time = $this->time;
        require $this->config->site->theme . '/frontend/template/protector.php';
        $content = ob_get_clean();

        // отправляем заголовки
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: ' . $time);
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Length: ' . strlen($content));

        // выводим сформированную страницу в браузер
        echo $content;
        die();

    }

    /**
     * Функция записывает ip-адрес в таблицу БД, чтобы накапливать статистику
     */
    private function addRecord() {

        $query = "INSERT INTO `protect_ips` (`ip`, `added`) VALUES (:ip, NOW())";
        $this->database->execute($query, array('ip' => $this->ip));

    }

    /**
     * Функция блокирект те ip-адреса, с которых идет слишком много запросов
     */
    private function lockIpAddresses() {

        $query = "SELECT
                      `ip`, COUNT(*)
                  FROM
                      `protect_ips`
                  WHERE
                      `added` > NOW() - INTERVAL :stack SECOND
                  GROUP BY
                      `ip`
                  HAVING
                      COUNT(*) > :hits";
        $ips = $this->database->fetchAll(
            $query,
            array(
                'stack' => $this->config->protector->stack,
                'hits'  => $this->config->protector->hits
            )
        );
        if ( ! empty($ips)) { // такие ip-адреса есть?
            $temp = array();
            foreach($ips as $ip) {
                $temp[] = "('" . $ip['ip'] . "', NOW())";
            }
            $query = "INSERT INTO `protect_ban` (`ip`, `added`) VALUES " . implode (',', $temp);
            $this->database->execute($query);
        }

    }

    /**
     * Функция удаляет старые записи из таблиц `protect_ban` и `protect_ips`
     */
    private function removeOldRecords() {

        $hour = date('H', time()+3600);
        $query = "ALTER TABLE `protect_ban` TRUNCATE PARTITION `hour" . $hour . "`";
        $this->database->execute($query);
        $query = "ALTER TABLE `protect_ips` TRUNCATE PARTITION `hour" . $hour . "`";
        $this->database->execute($query);

        /*
        $query = "DELETE FROM `protect_ban` WHERE `added` < NOW() - INTERVAL 24 HOUR";
        $this->database->execute($query, array('time' => $seconds));
        $query = "DELETE FROM `protect_ips` WHERE `added` < NOW() - INTERVAL 24 HOUR";
        $this->database->execute($query);
        */
    }

}