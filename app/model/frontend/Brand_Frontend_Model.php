<?php
/**
 * Класс Brand_Frontend_Model для работы с брендами, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Brand_Frontend_Model extends Frontend_Model {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Возвращает массив всех брендов
     */
    public function getAllBrands() {
        $brands = array(
            'A-Z' => $this->getLatinBrands(),
            'А-Я' => $this->getCyrillicBrands()
        );
        return $brands;
    }

    /**
     * Возвращает массив популярных брендов
     */
    public function getPopularBrands() {
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name`, `a`.`maker` AS `maker`,
                      `a`.`image` AS `image`, `a`.`popular` AS `sortorder`
                  FROM
                      `brands` `a` INNER JOIN `makers` `b`
                      ON `a`.`maker` = `b`.`id`
                  WHERE
                      `a`.`popular` <> 0 AND `a`.`image` <> ''
                  ORDER BY
                      `a`.`popular`";
        $brands = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок на страницу производителя
        foreach($brands as $key => $value) {
            $brands[$key]['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['maker']);
            $brands[$key]['image'] = $this->config->site->url . 'files/brand/' . $value['image'] . '.jpg';
        }
        return $brands;
    }
    
    /**
     * Возвращает массив брендов A-Z
     */
    private function getLatinBrands() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`maker` AS `maker`,
                      `a`.`letter` AS `letter`, `a`.`image` AS `image`,
                      `a`.`sortorder` AS `sortorder`
                  FROM
                      `brands` `a` INNER JOIN `makers` `b`
                      ON `a`.`maker` = `b`.`id`
                  WHERE
                      `letter` REGEXP '[A-Z]' AND `a`.`image` <> ''
                  ORDER BY
                      `a`.`letter`, `a`.`sortorder`";
        $items = $this->database->fetchAll($query);
        
        $brands = array();
        $letter = '';
        foreach ($items as $item) {
            if ($letter != $item['letter']) {
                $letter = $item['letter'];
            }
            $brands[$letter][] = array(
                'id'        => $item['id'],
                'name'      => $item['name'],
                'maker'     => $this->getURL('frontend/catalog/maker/id/' . $item['maker']),
                'image'     => $this->config->site->url . 'files/brand/' . $item['image'] . '.jpg',
                'sortorder' => $item['sortorder'],
            );
        }

        return $brands;

    }
    
    /**
     * Возвращает массив брендов А-Я
     */
    private function getCyrillicBrands() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`maker` AS `maker`,
                      `a`.`letter` AS `letter`, `a`.`image` AS `image`,
                      `a`.`sortorder` AS `sortorder`
                  FROM
                      `brands` `a` INNER JOIN `makers` `b`
                      ON `a`.`maker` = `b`.`id`
                  WHERE
                      `letter` REGEXP '[А-Я]' AND `a`.`image` <> ''
                  ORDER BY
                      `a`.`letter`, `a`.`sortorder`";
        $items = $this->database->fetchAll($query);
        
        $brands = array();
        $letter = '';
        foreach ($items as $item) {
            if ($letter != $item['letter']) {
                $letter = $item['letter'];
            }
            $brands[$letter][] = array(
                'id'        => $item['id'],
                'name'      => $item['name'],
                'maker'     => $this->getURL('frontend/catalog/maker/id/' . $item['maker']),
                'image'     => $this->config->site->url . 'files/brand/' . $item['image'] . '.jpg',
                'sortorder' => $item['sortorder'],
            );
        }

        return $brands;

    }
    
    /**
     * Возвращает латинницу
     */
    public function getLatinLetters() {
        $letters = array(
            1  => 'A',
            2  => 'B',
            3  => 'C',
            4  => 'D',
            5  => 'E',
            6  => 'F',
            7  => 'G',
            8  => 'H',
            9  => 'I',
            10 => 'J',
            11 => 'K',
            12 => 'L',
            13 => 'M',
            14 => 'N',
            15 => 'O',
            16 => 'P',
            17 => 'Q',
            18 => 'R',
            19 => 'S',
            20 => 'T',
            21 => 'U',
            22 => 'V',
            23 => 'W',
            24 => 'X',
            25 => 'Y',
            26 => 'Z',
        );
        return $letters;
    }
    
    /**
     * Возвращает кириллицу
     */
    public function getCyrillicLetters() {
        $letters = array(
            1  => 'А',
            2  => 'Б',
            3  => 'В',
            4  => 'Г',
            5  => 'Д',
            6  => 'Е',
            7  => 'Ж',
            8  => 'З',
            9  => 'И',
            10 => 'К',
            11 => 'Л',
            12 => 'М',
            13 => 'Н',
            14 => 'О',
            15 => 'П',
            16 => 'Р',
            17 => 'С',
            18 => 'Т',
            19 => 'У',
            20 => 'Ф',
            21 => 'Х',
            22 => 'Ц',
            23 => 'Ч',
            24 => 'Ш',
            25 => 'Щ',
            26 => 'Э',
            27 => 'Ю',
            28 => 'Я'
        );
        return $letters;
    }

}