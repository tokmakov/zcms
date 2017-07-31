<?php
/**
 * Класс Brand_Catalog_Frontend_Model для работы с брендами (производителей, отмеченных
 * в админке как бренд), взаимодействует с базой данных, общедоступная часть сайта
 */
class Brand_Catalog_Frontend_Model extends Catalog_Frontend_Model {

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
                      `a`.`id` AS `id`, `a`.`name` AS `name`,
                      `a`.`logo` AS `logo`, COUNT(*) AS `count`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `groups` `d` ON `b`.`group` = `d`.`id`
                  WHERE
                      `a`.`brand` = 1 AND `a`.`popular` = 1 AND `b`.`visible` = 1
                  GROUP BY
                      `a`.`id`, `a`.`name`, `a`.`logo`
                  ORDER BY
                      `a`.`name`";
        $brands = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок на страницу товаров бренда (производителя)
        foreach($brands as $key => $value) {
            $brands[$key]['url'] = $this->getURL('frontend/catalog/maker/id/' . $value['id']);
            $logo = 'files/catalog/makers/logo/default.jpg';
            if ( ! empty($value['logo']) && is_file('files/catalog/makers/logo/' . $value['logo'] . '.jpg')) {
                $logo = 'files/catalog/makers/logo/' . $value['logo'] . '.jpg';
            }
            $brands[$key]['img'] = $this->config->site->url . $logo;
        }
        return $brands;

    }

    /**
     * Возвращает массив брендов A-Z
     */
    private function getLatinBrands() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`logo` AS `logo`,
                      UPPER(LEFT(`a`.`name`, 1)) AS `letter`, COUNT(*) AS `count`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `groups` `d` ON `b`.`group` = `d`.`id`
                  WHERE
                      `a`.`brand` = 1 AND `b`.`visible` = 1
                      AND UPPER(LEFT(`a`.`name`, 1)) REGEXP '^[A-Z]'
                  GROUP BY
                      `a`.`id`, `a`.`name`, `a`.`logo`, UPPER(LEFT(`a`.`name`, 1))
                  ORDER BY
                      `a`.`name`";
        $items = $this->database->fetchAll($query);

        $brands = array();
        $letter = '';
        foreach ($items as $item) {
            if ($letter != $item['letter']) {
                $letter = $item['letter'];
            }
            $logo = 'files/catalog/makers/logo/default.jpg';
            if ( ! empty($item['logo']) && is_file('files/catalog/makers/logo/' . $item['logo'] . '.jpg')) {
                $logo = 'files/catalog/makers/logo/' . $item['logo'] . '.jpg';
            }
            $brands[$letter][] = array(
                'id'        => $item['id'],
                'name'      => $item['name'],
                'url'       => $this->getURL('frontend/catalog/maker/id/' . $item['id']),
                'img'       => $this->config->site->url . $logo
            );
        }

        return $brands;

    }

    /**
     * Возвращает массив брендов А-Я
     */
    private function getCyrillicBrands() {

        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`logo` AS `logo`,
                      UPPER(LEFT(`a`.`name`, 1)) AS `letter`, COUNT(*) AS `count`
                  FROM
                      `makers` `a`
                      INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `groups` `d` ON `b`.`group` = `d`.`id`
                  WHERE
                      `a`.`brand` = 1 AND `b`.`visible` = 1
                      AND UPPER(LEFT(`a`.`name`, 1)) REGEXP '^[А-Я]'
                  GROUP BY
                      `a`.`id`, `a`.`name`, `a`.`logo`, UPPER(LEFT(`a`.`name`, 1))
                  ORDER BY
                      `a`.`name`";
        $items = $this->database->fetchAll($query);

        $brands = array();
        $letter = '';
        foreach ($items as $item) {
            if ($letter != $item['letter']) {
                $letter = $item['letter'];
            }
            $logo = 'files/catalog/makers/logo/default.jpg';
            if ( ! empty($item['logo']) && is_file('files/catalog/makers/logo/' . $item['logo'] . '.jpg')) {
                $logo = 'files/catalog/makers/logo/' . $item['logo'] . '.jpg';
            }
            $brands[$letter][] = array(
                'id'        => $item['id'],
                'name'      => $item['name'],
                'url'       => $this->getURL('frontend/catalog/maker/id/' . $item['id']),
                'img'       => $this->config->site->url . $logo
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