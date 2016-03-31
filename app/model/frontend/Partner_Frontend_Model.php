<?php
/**
 * Класс Partner_Frontend_Model для работы с партнерами компании,
 * взаимодействует с базой данных, общедоступная часть сайта
 */
class Partner_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Возвращает массив всех партнеров компании
     */
    public function getAllPartners() {
        $query = "SELECT
                      `id`, `name`, `alttext`
                  FROM
                      `partners`
                  WHERE
                      `visible` = 1
                  ORDER BY
                      `sortorder`";
        $partners = $this->database->fetchAll($query);
        // добавляем в массив URL картинки сертификата
        foreach($partners as $key => $value) {
            $partners[$key]['url']['image'] = $this->config->site->url . 'files/partner/images/' . $value['id'] . '.jpg';
            $partners[$key]['url']['thumb'] = $this->config->site->url . 'files/partner/thumbs/' . $value['id'] . '.jpg';
        }
        return $partners;
    }

}