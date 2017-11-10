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
     * Функция возвращает результаты поиска по каталогу; вызывается из
     * self::getSearchResults()
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
        $result = $this->database->fetchAll($query, array(), $this->enableDataCache, true);
        // добавляем в массив результатов поиска информацию об URL товаров и фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) { // Content Delivery Network для фотографий товаров
            $host = $this->config->cdn->url;
        }
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
                    $result[$key]['mkr_name'],
                    $result[$key]['relevance']
                );
            } else { // для страницы поиска
                // URL ссылки на страницу товара
                $result[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
                // URL ссылки на страницу производителя
                $result[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
                // URL ссылки на фото товара
                if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
                    $result[$key]['url']['image'] = $host . 'files/catalog/imgs/small/' . $value['image'];
                } else {
                    $result[$key]['url']['image'] = $host . 'files/catalog/imgs/small/nophoto.jpg';
                }
                // атрибут action тега form для добавления товара в корзину
                $result[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
                // атрибут action тега form для добавления товара в избранное
                $result[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
                // атрибут action тега form для добавления товара к сравнению
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
     * Функция возвращает количество результатов поиска по каталогу;
     * вызывается из self::getCountSearchResults()
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
        return $this->database->fetchOne($query, array(), $this->enableDataCache, true);
    }

    /**
     * Функция возвращает SQL-запрос для поиска по каталогу
     */
    protected function getSearchQuery($search) {

        if (empty($search)) {
            return '';
        }
        if (iconv_strlen($search) < 2) {
            return '';
        }

        /*
        // на случай опечатки пользователя
        $typoLatin = array('a', 'c', 'e', 'o', 'p', 'x'); // латинские буквы, похожие на русские
        $typoCyril = array('а', 'с', 'е', 'о', 'р', 'х'); // русские буквы, похожие на латинские
        $latinOrCyril = array();
        $cyrilOrLatin = array();
        for ($i = 0; $i < 6; $i++) {
            $latinOrCyril[] = '(' . $typoLatin[$i] . '|' . $typoCyril[$i] . ')';
            $cyrilOrLatin[] = '(' . $typoCyril[$i] . '|' . $typoLatin[$i] . ')';
        }
        */

        // небольшой хак: разделяем строку ABC123 на ABC и 123 (пример: LG100 или NEC200);
        // сохраняем в $matches строки (слова) типа ABC123 до их разделения на ABC и 123
        if (preg_match('#[a-zа-яё]+\d+#u', $search)) {
            preg_match_all('#[a-zа-яё]+\d+#u', $search, $temp1);
            $search = preg_replace('#([a-zа-яё]+)(\d+)#u', '$1 $2', $search);
        }
        if (preg_match('#\d+[a-zа-яё]+#u', $search)) {
            preg_match_all('#\d+[a-zа-яё]+#u', $search, $temp2);
            $search = preg_replace('#(\d+)([a-zа-яё]+)#u', '$1 $2', $search);
        }
        $matches = array_merge(
            isset($temp1[0]) ? $temp1[0] : array(),
            isset($temp2[0]) ? $temp2[0] : array()
        );

        /*
         * Коэффициенты веса для функционального наименования и наименования производителя.
         * Релевантность товара поисковому запросу рассчитывается по формуле:
         * relevance = nameRelevance + titleWeight*titleRelevance +
         *             makerWeight*makerRelevance + codeRelevance
         * Учитываются торговое наименование, функциональное наименование, наименование
         * производителя и код (артикул) товара. При этом торговое наименование и код
         * (коэффициент веса 1.0) имеют приоритет перед функциональным наименованием и
         * наименованием производителя (коэффициент веса 0.8).
         */
        $titleWeight = 0.8;
        $makerWeight = 0.8;

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
                      `c`.`name` AS `mkr_name`,
                      `d`.`id` AS `grp_id`,
                      `d`.`name` AS `grp_name`";

        /*
         * Расчет релевантности для торгового наименования, например «ИП-212»
         */
        // если первое слово поискового запроса совпадает с первым словом торгового наименования
        $weight = 0.05;
        if (iconv_strlen($words[0]) > 1) $weight = 0.1;
        $query = $query.", IF( LOWER(`a`.`name`) REGEXP '^".$words[0]."', ".$weight.", 0 )";
        if (isset($words[1])) { // если совпадают первое и второе слово
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '^".$words[0]."[^0-9a-zа-яё]?".$words[1]."', " . $weight . ", 0 )";
        }

        // учитываем каждое слово поискового запроса на основе его длины, т.е. если совпало короткое
        // слово (длиной 1-2 символа), то взнос в релевантность такого совпадения невелик (0.1—0.2);
        // если совпало длинное слово (длиной 4-5 символов), то взнос в релевантность такого совпадения
        // гораздо выше (0.4—0.5); это позволяет немного уменьшить искажения от случайных совпадений
        // коротких слов
        for ($i = 0; $i < count($words); $i++) {
            $length = iconv_strlen($words[$i]);
            $weight = 0.5;
            if ($length < 5) {
                $weight = 0.1 * $length;
            }
            $query = $query." + IF( `a`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
            if (preg_match('#^[a-zа-яё]{2,}$#u', $words[$i])) {
                // если слово поискового запроса встречается и в торговом и в функциональном наименовании,
                // не учитываем его два раза; т.е. здесь не учитываем, а ниже (при расчете релевантности
                // функционального наименования) — учитываем
                $w = $titleWeight * $weight;
                $query = $query." - IF( `a`.`name` LIKE '%".$words[$i]."%' AND `a`.`title` LIKE '%".$words[$i]."%', ".$w.", 0 )";
                // если слово поискового запроса встречается и в торговом наименовании и в наименовании
                // производителя, не учитываем его два раза; т.е. здесь не учитываем, а ниже (при расчете
                // релевантности наименования производителя) — учитываем
                $w = $makerWeight * $weight;
                $query = $query." - IF( `a`.`name` LIKE '%".$words[$i]."%' AND `c`.`name` LIKE '%".$words[$i]."%', ".$w.", 0 )";
            }
        }
        // если слова расположены рядом и в нужном порядке
        for ($i = 1; $i < count($words); $i++) {
            $query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i-1]."[^0-9a-zа-яё]?".$words[$i]."', 0.1, 0 )";
        }
        // если мы разделяли строку ABC123 на ABC и 123
        if ( ! empty($matches)) {
            foreach ($matches as $item) {
                $query = $query." + IF( `a`.`name` LIKE '%".$item."%', 0.1, 0 )";
            }
        }

        /*
         * Расчет релевантности для функционального наименования, например «Извещатель пожарный дымовой»
         */
        // рассчитываем релевантность, только если слово достаточно длинное
        $longs = array();
        foreach ($words as $word) {
            if (preg_match('#^[a-zа-яё]{2,}$#u', $word)) {
                $longs[] = $word;
            }
        }
        if ( ! empty($longs)) {
            // учитываем каждое слово поискового запроса на основе его длины, по аналогии с расчетом
            // релевантности для торгового наименования изделия
            for ($i = 0; $i < count($longs); $i++) {
                $length = iconv_strlen($longs[$i]);
                $weight = 0.5;
                if ($length < 5) {
                    $weight = 0.1 * $length;
                }
                if ($i === 0) {
                    $query = $query." + ".$titleWeight."*( IF( `a`.`title` LIKE '%".$longs[$i]."%', ".$weight.", 0 )";
                } else {
                    $query = $query." + IF( `a`.`title` LIKE '%".$longs[$i]."%', ".$weight.", 0 )";
                }
                $query = $query." + IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$longs[$i]."', 0.1, 0 )";
            }
            $query = $query." )";
        }

        /*
         * Расчет релевантности для наименования производителя, например «Аргус-Спектр»
         */
        // рассчитываем релевантность, только если слово достаточно длинное
        if ( ! empty($longs)) {
            // учитываем каждое слово поискового запроса на основе его длины, по аналогии с расчетом
            // релевантности для торгового наименования изделия
            for ($i = 0; $i < count($longs); $i++) {
                $length = iconv_strlen($longs[$i]);
                $weight = 0.5;
                if ($length < 5) {
                    $weight = 0.1 * $length;
                }
                if ($i === 0) {
                    $query = $query." + ".$makerWeight."*( IF( `c`.`name` LIKE '%".$longs[$i]."%', ".$weight.", 0 )";
                } else {
                    $query = $query." + IF( `c`.`name` LIKE '%".$longs[$i]."%', ".$weight.", 0 )";
                }
                $query = $query." + IF( LOWER(`c`.`name`) REGEXP '[[:<:]]".$longs[$i]."', 0.1, 0 )";
            }
            $query = $query." )";
        }

        /*
         * Расчет релевантности для артикула (кода) товара, например «001001»
         */
        $codes = array();
        foreach($words as $word) {
            if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
        }
        for ($i = 0; $i < count($codes); $i++) {
            $query = $query." + IF( `a`.`code`='".$codes[$i]."', 1.0, 0 )";
        }

        $query = $query." AS `relevance`";

        $query = $query." FROM
                              `products` `a`
                              INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                              INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                              INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                          WHERE (";

        /*
         * Условие WHERE SQL-запроса
         */
        $query = $query."`a`.`name` LIKE '%".$words[0]."%'";
        $count = count($words);
        for ($i = 1; $i < $count; $i++) {
            $query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < $count; $i++) {
            $query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < $count; $i++) {
            $query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
        }
        $count = count($codes);
        for ($i = 0; $i < $count; $i++) {
            $query = $query." OR `a`.`code`='".$codes[$i]."'";
        }
        $query = $query.") AND `a`.`visible` = 1";

        /*
         * Сортировка результатов SQL-запроса
         */
        $query = $query." ORDER BY `relevance` DESC, CHAR_LENGTH(`a`.`name`), `a`.`name`";

        return $query;

    }

    /**
     * Функция возвращает SQL-запрос для получения кол-ва результатов поиска по каталогу
     */
    protected function getCountSearchQuery($search) {

        if (empty($search)) {
            return '';
        }
        if (iconv_strlen($search) < 2) {
            return '';
        }
        // небольшок хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
        $search = preg_replace('#([a-zа-яё]+)(\d+)#u', '$1 $2', $search);
        $search = preg_replace('#(\d+)([a-zа-яё]+)#u', '$1 $2', $search);

        $words = explode(' ', $search);
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                  WHERE (";

        $query = $query."`a`.`name` LIKE '%".$words[0]."%'";
        $count = count($words);
        for ($i = 1; $i < $count; $i++) {
            $query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < $count; $i++) {
            $query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
        }
        for ($i = 0; $i < $count; $i++) {
            $query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
        }
        $codes = array();
        foreach($words as $word) {
            if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
        }
        $count = count($codes);
        for ($i = 0; $i < $count; $i++) {
            $query = $query." OR `a`.`code`='".$codes[$i]."'";
        }
        $query = $query.") AND `a`.`visible` = 1";

        return $query;

    }

}
