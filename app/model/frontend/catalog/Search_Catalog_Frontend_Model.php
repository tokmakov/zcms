<?php
/**
 * Класс Search_Catalog_Frontend_Model для работы с поиском по каталогу,
 * взаимодействует с БД, общедоступная часть сайта
 */
class Search_Catalog_Frontend_Model extends Catalog_Frontend_Model {
    
    /*
     * public function getSearchResults(...)
     * protected function searchResults(...)
     * public function getCountSearchResults(...)
     * protected function countSearchResults(...)
     * protected function getSearchQuery(...)
     * protected function getCountSearchQuery(...)
     */

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Функция возвращает результаты поиска по каталогу; результат работы
     * кэшируется
     */
    public function getSearchResults($search, $start, $ajax) {

        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->searchResults($search, $start, $ajax);
        }
        // уникальный ключ доступа к кэшу
        $a = ($ajax) ? 'true' : 'false';
        $key = __METHOD__ . '()-search-' . md5($search) . '-start-' . $start . '-ajax-' . $a;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает результаты поиска по каталогу; результат работы
     * кэшируется
     */
    protected function searchResults($search, $start, $ajax) {
        
        $search = $this->cleanSearchString($search);
        if (empty($search)) {
            return array();
        }
        $query = $this->getSearchQuery($search);
        if (empty($query)) {
            return array();
        }
        
        $query = $query . ' LIMIT ' . $start . ', ' . $this->config->pager->frontend->products->perpage;
        $result = $this->database->fetchAll($query, array(), $this->enableDataCache);
        // добавляем в массив результатов поиска информацию об URL товаров и фото
        foreach($result as $key => $value) {
            if ($ajax) { // для поиска в шапке сайта
                $result[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
                unset(
                    $result[$key]['price'],
                    $result[$key]['price2'],
                    $result[$key]['price3'],
                    $result[$key]['shortdescr'],
                    $result[$key]['image'],
                    $result[$key]['hit'],
                    $result[$key]['new'],
                    $result[$key]['ctg_id'],
                    $result[$key]['ctg_name'],
                    $result[$key]['mkr_id'],
                    $result[$key]['mkr_id'],
                    $result[$key]['ctg_name'],
                    $result[$key]['relevance']
                );
            } else { // для страницы поиска
                // URL ссылки на страницу товара
                $result[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
                // URL ссылки на страницу производителя
                $result[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
                // URL ссылки на фото товара
                if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                    $result[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
                } else {
                    $result[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
                }
                // атрибут action тега form для добавления товара в корзину
                $result[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
                // атрибут action тега form для добавления товара в список отложенных
                $result[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
                // атрибут action тега form для добавления товара в список сравнения
                $result[$key]['action']['compare'] = $this->getURL('frontend/compare/addprd');
            }
        }

        return $result;

    }

    /**
     * Функция возвращает количество результатов поиска по каталогу;
     * результат работы кэшируется
     */
    public function getCountSearchResults($search) {
        // если не включено кэширование данных
        if ( ! $this->enableDataCache) {
            return $this->countSearchResults($search);
        }
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-search-' . md5($search);
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
    }
    
    /**
     * Функция возвращает количество результатов поиска по каталогу
     */
    protected function countSearchResults($search) {
        $search = $this->cleanSearchString($search);
        if (empty($search)) {
            return 0;
        }
        $query = $this->getCountSearchQuery($search);
        if (empty($query)) {
            return 0;
        }
        return $this->database->fetchOne($query, array(), $this->enableDataCache);
    }

    /**
     * Функция возвращает SQL-запрос для поиска по каталогу
     */
    protected function getSearchQuery($search) {
        if (empty($search)) {
            return '';
        }
        if (utf8_strlen($search) < 2) {
            return '';
        }
        // небольшой хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
        if (preg_match('#[a-zA-Zа-яА-ЯёЁ]{2,}\d{2,}#u', $search)) {
            preg_match_all('#[a-zA-Zа-яА-ЯёЁ]{2,}\d{2,}#u', $search, $temp1);
            $search = preg_replace('#([a-zA-Zа-яА-ЯёЁ]{2,})(\d{2,})#u', '$1 $2', $search );
        }
        if (preg_match('#\d{2,}[a-zA-Zа-яА-ЯёЁ]{2,}#u', $search)) {
            preg_match_all('#\d{2,}[a-zA-Zа-яА-ЯёЁ]{2,}#u', $search, $temp2);
            $search = preg_replace( '#(\d{2,})([a-zA-Zа-яА-ЯёЁ]{2,})#u', '$1 $2', $search );
        }
        $matches = array_merge(isset($temp1[0]) ? $temp1[0] : array(), isset($temp2[0]) ? $temp2[0] : array());

        $words = explode(' ', $search);
        $query = "SELECT
                      `a`.`id` AS `id`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      `a`.`price` AS `price`,
                      `a`.`price2` AS `price2`,
                      `a`.`price3` AS `price3`,
                      `a`.`unit` AS `unit`,
                      `a`.`shortdescr` AS `shortdescr`,
                      `a`.`image` AS `image`,
                      `a`.`hit` AS `hit`,
                      `a`.`new` AS `new`,
                      `b`.`id` AS `ctg_id`,
                      `b`.`name` AS `ctg_name`,
                      `c`.`id` AS `mkr_id`,
                      `c`.`name` AS `mkr_name`";

        $query = $query.", IF( LOWER(`a`.`name`) REGEXP '^".$words[0]."', 0.1, 0 ) + IF( LOWER(`a`.`title`) REGEXP '^".$words[0]."', 0.05, 0 )";

        $prd_name = 1.0; // коэффициент веса для `name`
        $length = utf8_strlen($words[0]);
        $weight = 0.5;
        if ($length < 5) {
            $weight = 0.1 * $length;
        }
        $query = $query." + ".$prd_name."*( IF( `a`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." + IF( LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
        $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового
        // запроса, как и для первого слова
        for ($i = 1; $i < count($words); $i++) {
            $length = utf8_strlen($words[$i]);
            $weight = 0.5;
            if ($length < 5) {
                $weight = 0.1 * $length;
            }
            $query = $query." + IF( `a`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
        }
        // если слова расположены рядом в нужном порядке
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0 )";
        }
        // если мы разделяли строку ABC123 на ABC и 123
        if ( ! empty($matches)) {
            foreach ($matches as $item) {
                $query = $query." + IF( `a`.`name` LIKE '%".$item."%', 0.1, 0 )";
            }
        }
        $query = $query." )";

        $prd_title = 0.8; // коэффициент веса для `title`
        $length = utf8_strlen($words[0]);
        $weight = 0.5;
        if ($length < 5) {
            $weight = 0.1 * $length;
        }
        $query = $query." + ".$prd_title."*( IF( `a`.`title` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." - IF( `a`.`title` LIKE '%".$words[0]."%' AND `a`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." + IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
        $query = $query." - IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
        $query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
        $query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового
        // запроса, как и для первого слова
        for ($i = 1; $i < count($words); $i++) {
            $length = utf8_strlen($words[$i]);
            $weight = 0.5;
            if ($length < 5) {
                $weight = 0.1 * $length;
            }
            $query = $query." + IF( `a`.`title` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." - IF( `a`.`title` LIKE '%".$words[$i]."%' AND `a`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." + IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
            $query = $query." - IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
            $query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
            $query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
        }
        // если слова расположены рядом в нужном порядке
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0 )";
            $query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[$i-1].".?".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0  )";
        }
        // если мы разделяли строку ABC123 на ABC и 123
        if ( ! empty($matches)) {
            foreach ($matches as $item) {
                $query = $query." + IF( `a`.`title` LIKE '%".$item."%', 0.1, 0 )";
            }
        }
        $query = $query." )";

        $prd_maker = 0.6; // коэффициент веса для `mkr_name`
        $length = utf8_strlen($words[0]);
        $weight = 0.5;
        if ($length < 5) {
            $weight = 0.1 * $length;
        }
        $query = $query." + ".$prd_maker."*( IF( `c`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
        $query = $query." - IF( (`c`.`name` LIKE '%".$words[0]."%' AND `a`.`name` LIKE '%".$words[0]."%') OR (`c`.`name` LIKE '%".$words[0]."%' AND `a`.`title` LIKE '%".$words[0]."%'), ".$weight.", 0 )";
        $query = $query." + IF( LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.1, 0 )";
        $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."') OR (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."'), 0.1, 0 )";
        $query = $query." + IF( LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.1, 0 )";
        $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]') OR (LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]'), 0.1, 0 )";
        // здесь просто выполняются действия для второго, третьего и т.п. слов поискового запроса,
        // как и для первого слова
        for ($i = 1; $i < count($words); $i++) {
            $length = utf8_strlen($words[$i]);
            $weight = 0.5;
            if ($length < 5) {
                $weight = 0.1 * $length;
            }
            $query = $query." + IF( `c`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." - IF( (`c`.`name` LIKE '%".$words[$i]."%' AND `a`.`name` LIKE '%".$words[$i]."%') OR (`c`.`name` LIKE '%".$words[$i]."%' AND `a`.`title` LIKE '%".$words[$i]."%'), ".$weight.", 0 )";
            $query = $query." + IF( LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.1, 0 )";
            $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."') OR (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."'), 0.1, 0 )";
            $query = $query." + IF( LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.1, 0 )";
            $query = $query." - IF( (LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]') OR (LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]'), 0.1, 0 )";
        }
        $query = $query." )";

        $prd_code = 1.0; // коэффициент веса для `code`
        $codes = array();
        foreach($words as $word) {
            if (preg_match('#^\d{4}$#', $word)) $codes[] = '00'.$word;
            if (preg_match('#^\d{5}$#', $word)) $codes[] = '0'.$word;
            if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
        }
        if (count($codes) > 0) {
            $query = $query." + " . $prd_code . "*( IF( `a`.`code`='".$codes[0]."', 1.0, 0 )";
            for ($i = 1; $i < count($codes); $i++) {
                $query = $query." + IF( `a`.`code`='".$codes[$i]."', 1.0, 0 )";
            }
            $query = $query." )";
        }

        $query = $query." AS `relevance`";

        $query = $query." FROM
                              `products` `a`
                              INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                              INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                              INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                          WHERE (";

        $query = $query."`a`.`name` LIKE '%".$words[0]."%'";
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
        }
        if (count($codes) > 0) {
            $query = $query." OR `a`.`code`='".$codes[0]."'";
            for ($i = 1; $i < count( $codes ); $i++) {
              $query = $query." OR `a`.`code`='".$codes[$i]."'";
            }
        }
        $query = $query.") AND `a`.`visible` = 1";
        $query = $query." ORDER BY `relevance` DESC, LENGTH(`a`.`name`), `a`.`name`";

        return $query;
    }

    /**
     * Функция возвращает SQL-запрос для получения кол-ва результатов поиска по каталогу
     */
    protected function getCountSearchQuery($search) {
        if (empty($search)) {
            return '';
        }
        if (utf8_strlen($search) < 2) {
            return '';
        }
        // небольшок хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
        $search = preg_replace('#([a-zA-Zа-яА-ЯёЁ]{2,})(\d{2,})#u', '$1 $2', $search );
        $search = preg_replace( '#(\d{2,})([a-zA-Zа-яА-ЯёЁ]{2,})#u', '$1 $2', $search );

        $words = explode(' ', $search);
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                  WHERE (";

        $query = $query."`a`.`name` LIKE '%".$words[0]."%'";
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < count($words); $i++) {
            $query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
        }
        $codes = array();
        foreach ($words as $word) {
            if (preg_match('#^\d{4}$#', $word)) $codes[] = '00'.$word;
            if (preg_match('#^\d{5}$#', $word)) $codes[] = '0'.$word;
            if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
        }
        if (count($codes) > 0) {
            $query = $query." OR `a`.`code`='".$codes[0]."'";
            for ($i = 1; $i < count($codes); $i++) {
                $query = $query." OR `a`.`code`='".$codes[$i]."'";
            }
        }
        $query = $query.") AND `a`.`visible` = 1";

        return $query;
    }

}
