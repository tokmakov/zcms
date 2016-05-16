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
     * Возвращает латиницу
     */
    public function getLatinLetters() {
        $letters = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
        );
        return $letters;
    }
    
    /**
     * Возвращает кириллицу
     */
    public function getCyrillicLetters() {
        $letters = array(
            'А',
            'Б',
            'В',
            'Г',
            'Д',
            'Е',
            'Ж',
            'З',
            'И',
            'К',
            'Л',
            'М',
            'Н',
            'О',
            'П',
            'Р',
            'С',
            'Т',
            'У',
            'Ф',
            'Х',
            'Ц',
            'Ч',
            'Ш',
            'Щ',
            'Э',
            'Ю',
            'Я'
        );
        return $letters;
    }

}