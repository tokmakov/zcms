<?php
/**
 * Класс Compare_Frontend_Model отвечает за сравнение товаров, взаимодействует с базой
 * данных, реализует шаблон проектирования «Наблюдатель», общедоступная часть сайта;
 * см. описание интерфейса SplObserver http://php.net/manual/ru/class.splobserver.php
 */
class Compare_Frontend_Model extends Frontend_Model implements SplObserver {

    /**
     * уникальный идентификатор посетителя сайта, который сохраняется в cookie
     * и нужен для работы покупательской корзины, списка отложенных товаров,
     * списка товаров для сравнения и истории просмотренных товаров
     */
    private $visitorId;

    /**
     * хранит идентификатор функциональной группы товаров,
     * которые в настоящий момент добавлены к сравнению
     */
    private $groupId = 0;


    public function __construct() {

        parent::__construct();
        // уникальный идентификатор посетителя сайта
        if ( ! isset($this->register->userFrontendModel)) {
            // экземпляр класса модели для работы с пользователями
            new User_Frontend_Model();
        }
        $this->visitorId = $this->register->userFrontendModel->getVisitorId();
        // идентификатор функциональной группы товаров для сравнения
        $this->groupId = $this->getCompareGroup();
        if ($this->groupId) {
            // обновляем cookie
            setcookie('compare_group', $this->groupId, time() + 31536000, '/');
        } else {
            // удаляем cookie
            setcookie('compare_group', '', time() - 86400, '/');
        }
    }

    /**
     * Функция добавляет товар в список сравнения
     */
    public function addToCompare($productId) {

        // можно добавить к сравнению только 5 товаров
        if ($this->getCompareCount() > 4) {
            return false;
        }

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }

        // данные для выполнения SQL-запросов
        $data = array(
            'visitor_id' => $this->visitorId,
            'product_id' => $productId,
        );
        // функциональная группа нового товара
        $newProductGroupId = $this->getProductGroup($productId);
        // товара не найден и не может быть добавлен к сравнению
        if (false === $newProductGroupId) {
            return false;
        }

        /*
         * список сравнения пуст, можем добавлять любой товар
         */
        if (0 === $this->groupId) {
            $query = "INSERT INTO `compare`
                      (
                          `visitor_id`,
                          `product_id`,
                          `active`,
                          `added`
                      )
                      VALUES
                      (
                          :visitor_id,
                          :product_id,
                          1,
                          NOW()
                      )";
            $this->database->execute($query, $data);
            $this->groupId = $newProductGroupId;
            // обновляем cookie
            setcookie('compare_group', $this->groupId, time() + 31536000, '/');
            return true;
        }

        /*
         * уже есть товары в списке сравнения, нужны дополнительные проверки
         */

        // можно добавить только товар, принадлежащий к той же функциональной
        // группе, что и другие товары в списке сравнения
        if ($this->groupId !== $newProductGroupId) {
            return false;
        }
        // такой товар уже есть в списке сравнения?
        $query = "SELECT
                      1
                  FROM
                      `compare`
                  WHERE
                      `visitor_id` = :visitor_id AND
                      `product_id` = :product_id AND
                      `active`     = 1";
        $res = $this->database->fetchOne($query, $data);
        if (false === $res) { // если товара еще нет в списке сравнения, добавляем его
            $query = "INSERT INTO `compare`
                      (
                          `visitor_id`,
                          `product_id`,
                          `active`,
                          `added`
                      )
                      VALUES
                      (
                          :visitor_id,
                          :product_id,
                          1,
                          NOW()
                      )";
            $this->database->execute($query, $data);
        } else { // если товар уже в списке сравнения, обновляем дату добавления
            $query = "UPDATE
                          `compare`
                      SET
                          `added` = NOW()
                      WHERE
                          `visitor_id` = :visitor_id AND
                          `product_id` = :product_id AND
                          `active`     = 1";
            $this->database->execute($query, $data);
        }
        // обновляем cookie
        setcookie('compare_group', $this->groupId, time() + 31536000, '/');

        return true;

    }

    /**
     * Функция возвращает количество товаров в списке сравнения; результат
     * работы кэшируется
     */
    public function getCompareCount() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->compareCount();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает количество товаров в списке сравнения
     */
    protected function compareCount() {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND `a`.`active` = 1 AND `b`.`visible` = 1";
        return $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
    }



    /**
     * Функция возвращает идентификатор функциональной группы товара $id
     */
    private function getProductGroup($id) {
        $query = "SELECT
                      `d`.`id`
                  FROM
                      `products` `a`
                      INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
                      INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
                      INNER JOIN `groups` `d` ON `a`.`group` = `d`.`id`
                  WHERE
                      `a`.`id` = :id AND
                      `a`.`visible` = 1";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция возвращает идентификатор функциональной группы товаров,
     * которые уже есть в списке сравнения; если список сравнения пустой,
     * функция возвращает ноль; результат работы кэшируется
     */
    private function getCompareGroup() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->compareGroup();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает идентификатор функциональной группы товаров,
     * которые уже есть в списке сравнения; если список сравнения пустой,
     * функция возвращает ноль
     */
    protected function compareGroup() {
        $query = "SELECT
                      `e`.`id`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND `a`.`active` = 1 AND `b`.`visible` = 1
                  ORDER BY
                      `a`.`added` DESC
                  LIMIT
                      1";
        $group = $this->database->fetchOne($query, array('visitor_id' => $this->visitorId));
        if (false === $group) {
            return 0;
        }
        return $group;
    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения;
     * для центральной колонки, полный вариант
     */
    public function getCompareProducts() {
        $query = "SELECT
                      `b`.`id` AS `id`,
                      `b`.`code` AS `code`,
                      `b`.`name` AS `name`,
                      `b`.`title` AS `title`,
                      `b`.`shortdescr` AS `shortdescr`,
                      `b`.`price` AS `price`,
                      `b`.`price2` AS `price2`,
                      `b`.`price3` AS `price3`,
                      `b`.`unit` AS `unit`,
                      `b`.`image` AS `image`,
                      `b`.`hit` AS `hit`,
                      `b`.`new` AS `new`,
                      `c`.`id` AS `ctg_id`,
                      `c`.`name` AS `ctg_name`,
                      `d`.`id` AS `mkr_id`,
                      `d`.`name` AS `mkr_name`,
                      `e`.`id` AS `grp_id`,
                      `e`.`name` AS `grp_name`,
                      DATE_FORMAT(`a`.`added`, '%d.%m.%Y') AS `date`,
                      DATE_FORMAT(`a`.`added`, '%H:%i:%s') AS `time`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND `a`.`active` = 1 AND `b`.`visible` = 1
                  ORDER BY
                      `a`.`added` DESC";
        $products = $this->database->fetchAll($query, array('visitor_id' => $this->visitorId));
        // добавляем в массив товаров информацию об URL товаров, производителей, фото
        $host = $this->config->site->url;
        if ($this->config->cdn->enable->img) {
            $host = $this->config->cdn->url;
        }
        foreach($products as $key => $value) {
            // URL ссылки на страницу товара
            $products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            // URL ссылки на страницу производителя
            $products[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
            // URL файла изображения товара
            if ((!empty($value['image'])) && is_file('files/catalog/imgs/small/' . $value['image'])) {
                $products[$key]['url']['image'] = $host . 'files/catalog/imgs/small/' . $value['image'];
            } else {
                $products[$key]['url']['image'] = $host . 'files/catalog/imgs/small/nophoto.jpg';
            }
            // атрибут action тега form для добавления товара в корзину
            $products[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
            // атрибут action тега form для добавления товара в список отложенных
            $products[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
            // атрибут action тега form для удаления товара из списка сравнения
            $products[$key]['action']['compare'] = $this->getURL('frontend/compare/rmvprd');
        }

        // удаляем старые товары
        if (rand(1, 100) === 50) {
            $this->removeOldCompare();
        }

        return $products;
    }

    /**
     * Функция возвращает наименование функциональной группы
     */
    public function getGroupName() {
        if (0 === $this->groupId) {
            return '';
        }
        $query = "SELECT
                      `name`
                  FROM
                      `groups`
                  WHERE
                      `id` = :group_id";
        return $this->database->fetchOne($query, array('group_id' => $this->groupId));
    }

    /**
     * Функция возвращает массив параметров, привязанных к группе
     */
    public function getGroupParams() {

        /*
         * TODO: подумать, как уменьшить кол-во запросов
         */

        if (0 === $this->groupId) {
            return array();
        }

        // получаем массив товаров, отложенных для сравнения
        $query = "SELECT
                      `b`.`id` AS `id`, `b`.`code` AS `code`, `b`.`title` AS `title`,
                      `b`.`shortdescr` AS `shortdescr`, `b`.`techdata` AS `techdata`,
                      `d`.`name` AS `maker`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                  WHERE
                      `e`.`id` = :group_id AND
                      `a`.`visitor_id` = :visitor_id AND
                      `a`.`active` = 1 AND
                      `b`.`visible` = 1
                  ORDER BY
                      `a`.`added` DESC";
        $products = $this->database->fetchAll(
            $query,
            array(
                'group_id' => $this->groupId,
                'visitor_id' => $this->visitorId
            )
        );
        $title[]      = 'Функциональное наименование';
        $code[]       = 'Код';
        $maker[]      = 'Производитель';
        $techdata[]   = 'Технические характеристики';
        $shortdescr[] = 'Краткое описание';
        foreach ($products as $product) {
            $title[]      = $product['title'];
            $code[]       = $product['code'];
            $maker[]      = $product['maker'];
            // $product['techdata'] это не массив технических характеристик, а
            // ссылка на страницу товара, которая открывается в модальном окне
            if ( ! empty($product['techdata'])) {
                $techdata[] = $this->getURL('frontend/catalog/product/id/' . $product['id']);
            } else {
                $techdata[] = '';
            }
            $shortdescr[] = $product['shortdescr'];
        }

        /*
         * Получаем массив параметров подбора для функциональной группы. Здесь
         * мы получаем не полный набор параметров подбора для функциональной
         * группы, а только ту часть параметров, которая характерна для товаров,
         * участвующих в сравнении.
         *
         * Например, для функционала «Видеокамеры» в таблице `group_param_value`
         * заданы три параметра подбора:
         *   1. ИК подсветка: да, нет
         *   2. Напряжение питания: 12 Вольт, 24 Вольт
         *   3. Тип корпуса: корпусная, купольная
         * Но для трех видеокамер AB-12, CD-34, EF-56, участвующих в сравнении,
         * в таблице `product_param_value` заданы только такие параметры:
         *   AB-12: ИК подсветка — да, Напряжение питания — 12 Вольт
         *   CD-34: ИК подсветка — нет, Напряжение питания — 24 Вольт
         *   EF-56: параметры подбора не заданы, нет записей в таблице БД
         * В этом случае мы получим в выборке только два параметра подбора:
         * «ИК подсветка» и «Напряжение питания». «Тип корпуса» в выборку не
         * попадет.
         *
         * Для получения полного набора параметров надо использовать таблицу БД
         * `group_param_value` вместо `product_param_value`.
         */

        /*
         * $result1 = Array (
         *   [0] => Array (
         *     [id] => 159
         *     [name] => Расстояние срабатывания
         *   )
         *   [1] => Array (
         *     [id] => 161
         *     [name] => Материал
         *   )
         *   [2] => Array (
         *     [id] => 162
         *     [name] => Напряжение питания
         *   )
         * )
         */
        $query = "SELECT
                      `g`.`id` AS `id`, `g`.`name` AS `name`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                      INNER JOIN `product_param_value` `f` ON `b`.`id` = `f`.`product_id`
                      INNER JOIN `params` `g` ON `f`.`param_id` = `g`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND
                      `a`.`active` = 1 AND
                      `e`.`id` = :group_id AND
                      `b`.`visible` = 1
                  GROUP BY
                      1, 2
                  ORDER BY
                      `g`.`name`";
        $result1 = $this->database->fetchAll(
            $query,
            array(
                'visitor_id' => $this->visitorId,
                // вообще, условие в запросе `e`.`id` = :group_id — лишнее, к
                // сравнению добавляются только товары с одинаковым фунционалом
                'group_id' => $this->groupId
            )
        );


        /*
         * Получаем значения параметров подбора для товаров из списка сравнения
         *
         * $result2 = Array (
         *   [0] => Array (
         *     [product_id] => 1005
         *     [param_id] => 159
         *     [param_name] => Расстояние срабатывания
         *     [value_id] => 1608
         *     [value_name] => 10
         *   )
         *   [1] => Array (
         *     [product_id] => 1005
         *     [param_id] => 162
         *     [param_name] => Напряжение питания
         *     [value_id] => 1606
         *     [value_name] => 24 Вольт
         *   )
         *   [2] => Array (
         *     [product_id] => 1001
         *     [param_id] => 161
         *     [param_name] => Материал
         *     [value_id] => 1614
         *     [value_name] => Металл
         *   )
         *   [4] => Array (
         *     [product_id] => 1004
         *     [param_id] => 161
         *     [param_name] => Материал
         *     [value_id] => 1605
         *     [value_name] => Пластик
         *   )
         *   ..........
         *   [6] => Array (
         *     [product_id] => 1003
         *     [param_id] => 161
         *     [param_name] => Материал
         *     [value_id] => 1605
         *     [value_name] => Пластик
         *   )
         *   [7] => Array (
         *     [product_id] => 1003
         *     [param_id] => 162
         *     [param_name] => Напряжение питания
         *     [value_id] => 1610
         *     [value_name] => 12 Вольт
         *   )
         *   [8] => Array (
         *     [product_id] => 1003
         *     [param_id] => 162
         *     [param_name] => Напряжение питания
         *     [value_id] => 1606
         *     [value_name] => 24 Вольт
         *   )
         * )
         */
        $query = "SELECT
                      `b`.`id` AS `product_id`, `g`.`id` AS `param_id`, `g`.`name` AS `param_name`,
                      `h`.`id` AS `value_id`, `h`.`name` AS `value_name`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                      INNER JOIN `product_param_value` `f` ON `b`.`id` = `f`.`product_id`
                      INNER JOIN `params` `g` ON `f`.`param_id` = `g`.`id`
                      INNER JOIN `values` `h` ON `f`.`value_id` = `h`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND
                      `a`.`active` = 1 AND
                      `e`.`id` = :group_id AND
                      `b`.`visible` = 1
                  ORDER BY
                      `a`.`added` DESC,
                      `g`.`name`, `h`.`name`";
        $result2 = $this->database->fetchAll(
            $query,
            array(
                'visitor_id' => $this->visitorId,
                // вообще, условие в запросе `e`.`id` = :group_id — лишнее, к
                // сравнению добавляются только товары с одинаковым фунционалом
                'group_id' => $this->groupId
            )
        );

        /*
         * структура массива, который будет сформирован в цикле:
         *
         * $result3 = Array (
         *   [1005] => Array (
         *     [159] => Array (
         *       [0] => Array (
         *         [0] => Расстояние срабатывания
         *         [1] => 10
         *       )
         *     )
         *     [162] => Array (
         *       [0] => Array (
         *         [0] => Напряжение питания
         *         [1] => 24 Вольт
         *       )
         *     )
         *   )
         *   [1001] => Array (
         *     [161] => Array (
         *       [0] => Array (
         *         [0] => Материал
         *         [1] => Металл
         *       )
         *     )
         *   )
         *   [1004] => Array (.....)
         *   [1002] => Array (.....)
         *   [1003] => Array (
         *     [161] => Array (
         *       [0] => Array (
         *         [0] => Материал
         *         [1] => пластик
         *       )
         *     )
         *     [162] => Array (
         *       [0] => Array (
         *         [0] => Напряжение питания
         *         [1] => 12 Вольт
         *       )
         *       [1] => Array(
         *         [0] => Напряжение питания
         *         [1] => 24 Вольт
         *       )
         *     )
         *   )
         * )
         */
        $result3 = array();
        foreach ($result2 as $item) {
            $result3[$item['product_id']][$item['param_id']][] = array(
                $item['param_name'],
                $item['value_name']
            );
        }

        /*
         * Перебираем все параметры подбора, для каждого товара
         * получаем конкретное значение параметра.
         *
         * Количество элементов в массиве равно количеству параметров,
         * каждый элемент массива — вложенный массив. Первый элемент
         * вложенного массива (с индексом ноль) содержит название
         * параметра подбора, остальные элементы — значения параметров
         * подбора для каждого товара сравнения. Если для какого-то
         * товара параметр подбора не задан, тогда значением параметра
         * будет пустая строка.
         *
         * $params = Array (
         *   [0] => Array (
         *     [0] => Расстояние срабатывания
         *     [1] => 10
         *     [2] =>
         *     [3] => 10
         *     [4] => 15
         *     [5] =>
         *   )
         *   [1] => Array (
         *     [0] => Материал
         *     [1] =>
         *     [2] => Металл
         *     [3] => Пластик
         *     [4] =>
         *     [5] => Пластик
         *   )
         *   [2] => Array (
         *     [0] => Напряжение питания
         *     [1] => 24 Вольт
         *     [2] =>
         *     [3] => 12 Вольт
         *     [4] =>
         *     [5] => Array (
         *       [0] => 12 Вольт
         *       [1] => 24 Вольт
         *     )
         *   )
         * )
         */
        $params = array();
        // цикл по параметрам подбора
        foreach ($result1 as $i => $item) {
            $params[$i][] = $item['name'];
            // цикл по товарам, отложенным для сравнения
            foreach ($products as $j => $product) {
                if ( ! isset($result3[$product['id']][$item['id']])) {
                    // значение параметра не задано, записываем пустую строку
                    $params[$i][$j+1] = '';
                    continue;
                }
                if (count($result3[$product['id']][$item['id']]) > 1) {
                    // для товара задано два значения параметра подбора, например
                    // «Напряжение питания» : «12 Вольт», «24 Вольт»
                    foreach($result3[$product['id']][$item['id']] as $value) {
                        $params[$i][$j+1][] = $value[1];
                    }
                } else {
                    $params[$i][$j+1] = $result3[$product['id']][$item['id']][0][1];
                }

            }
        }

        $result = array_merge(array($title, $code, $maker, $techdata, $shortdescr), $params);

        return $result;

    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения; для
     * правой колонки, сокращенный вариант; результат работы кэшируется
     */
    public function getSideCompareProducts() {

        /*
         * если не включено кэширование данных, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $this->enableDataCache) {
            return $this->sideCompareProducts();
        }

        /*
         * включено кэширование данных, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Функция возвращает массив товаров, отложенных для сравнения;
     * для правой колонки, сокращенный вариант
     */
    protected function sideCompareProducts() {
        $query = "SELECT
                      `b`.`id` AS `id`,
                      `b`.`code` AS `code`,
                      `b`.`name` AS `name`,
                      `b`.`price` AS `price`,
                      `b`.`unit` AS `unit`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND
                      `a`.`active` = 1 AND
                      `b`.`visible` = 1
                  ORDER BY
                      `a`.`added` DESC";
        $products = $this->database->fetchAll(
            $query,
            array('visitor_id' => $this->visitorId)
        );
        // добавляем в массив URL ссылок на страницы товаров
        foreach($products as $key => $value) {
            $products[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
            $products[$key]['action'] = $this->getURL('frontend/compare/rmvprd');
        }
        return $products;
    }

    /**
     * Функция «удаляет» товар из списка отложенных для сравнения товаров
     */
    public function removeFromCompare($productId) {

        $query = "UPDATE
                      `compare`
                  SET
                      `active` = 0
                  WHERE
                      `product_id` = :product_id AND
                      `visitor_id` = :visitor_id";
        $this->database->execute(
            $query,
            array(
                'product_id' => $productId,
                'visitor_id' => $this->visitorId
            )
        );
        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }
        // на случай, если это последний товар из списка сравнения
        $this->groupId = $this->getCompareGroup();
        if ($this->groupId) {
            // обновляем cookie
            setcookie('compare_group', $this->groupId, time() + 31536000, '/');
        } else {
            // удаляем cookie
            setcookie('compare_group', '', time() - 86400, '/');
        }

    }

    /**
     * Функция «удаляет» все товары из списка сравнения пользователя
     */
    public function clearCompareList() {
        $query = "UPDATE
                      `compare`
                  SET
                      `active` = 0
                  WHERE
                      `visitor_id` = :visitor_id AND `active` = 1";
        $this->database->execute(
            $query,
            array(
                'visitor_id' => $this->visitorId
            )
        );
        $this->groupId = 0;
        // удаляем cookie
        setcookie('compare_group', '', time() - 86400, '/');

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            $key = __CLASS__ . '-group-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-count-visitor-' . $this->visitorId;
            $this->cache->removeValue($key);
        }
    }

    /**
     * Функция удаляет все старые товары для сравения
     */
    public function removeOldCompare() {
        $query = "DELETE FROM
                      `compare`
                  WHERE
                      `product_id` NOT IN (SELECT `id` FROM `products` WHERE 1)";
        $this->database->execute($query);

        $query = "DELETE FROM
                      `compare`
                  WHERE
                      `added` < NOW() - INTERVAL :days DAY";
        $this->database->execute($query, array('days' => $this->config->user->cookie));
    }

    /**
     * Функция объединяет списки отложенных для сравнения товаров (ещё) не
     * авторизованного посетителя и (уже) авторизованного пользователя сразу
     * после авторизации, реализация шаблона проектирования «Наблюдатель»; см.
     * описание интерфейса SplObserver http://php.net/manual/ru/class.splobserver.php
     */
    public function update(SplSubject $userFrontendModel) {

        /*
         * Уникальный идентификатор посетителя сайта сохраняется в cookie и нужен
         * для хранения списка товаров для сравнения. По нему можно получить из
         * таблицы БД `compare` все товары, добавленные к сравнению.
         *
         * Если в cookie есть идентификатор посетителя, значит он уже просматривал
         * страницы сайта с этого компьютера. Если идентификатора нет в cookie,
         * значит посетитель на сайте первый раз (и просматривает первую страницу),
         * или зашел с другого компьютера. В этом случае записываем в cookie новый
         * идентификатор.
         *
         * Если в cookie не было идентификатора посетителя и ему был записан новый
         * идентификатор, это еще не означает, что посетитель здесь в первый раз.
         * Он мог зайти с другого компьютера, удалить cookie или истекло время жизни
         * cookie.
         *
         * Сразу после авторизации проверяем — совпадает временный идентификатор
         * посетителя (который сохранен в cookie) с постоянным (который хранится в
         * в БД `users`). Если совпадает — ничего не делаем, если нет — записываем
         * в cookie вместо временного постоянный идентификатор и обновляем записи
         * таблицы БД `compare`, заменяя временный идентификатор на постоянный.
         */
        $newVisitorId = $userFrontendModel->getVisitorId();
        $oldVisitorId = $this->visitorId;

        if ($newVisitorId == $oldVisitorId) {
            return;
        }

        $query = "UPDATE
                      `compare`
                  SET
                      `visitor_id` = :new_visitor_id
                  WHERE
                      `visitor_id` = :old_visitor_id";
        $this->database->execute(
            $query,
            array(
                'old_visitor_id' => $oldVisitorId,
                'new_visitor_id' => $newVisitorId
            )
        );

        // удаляем кэш, потому как он теперь не актуален
        if ($this->enableDataCache) {
            // кэш (ещё) не авторизованного посетителя
            $key = __CLASS__ . '-group-visitor-' . $oldVisitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $oldVisitorId;
            $this->cache->removeValue($key);
            // кэш (уже) авторизованного пользователя
            $key = __CLASS__ . '-group-visitor-' . $newVisitorId;
            $this->cache->removeValue($key);
            $key = __CLASS__ . '-products-visitor-' . $newVisitorId;
            $this->cache->removeValue($key);
        }

        $this->visitorId = $newVisitorId;

        $this->groupId = $this->getCompareGroup();

        // если список сравнения и после объединения пустой,
        // больше ничего делать не надо
        if (0 === $this->groupId) {
            // удаляем cookie
            setcookie('compare_group', '', time() - 86400, '/');
            return;
        }

        // в объединенном списке сравнения есть товары, но в нем
        // могут быть товары из разных функциональных групп
        $query = "SELECT
                      `a`.`id` AS `id`
                  FROM
                      `compare` `a`
                      INNER JOIN `products` `b` ON `a`.`product_id` = `b`.`id`
                      INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
                      INNER JOIN `makers` `d` ON `b`.`maker` = `d`.`id`
                      INNER JOIN `groups` `e` ON `b`.`group` = `e`.`id`
                  WHERE
                      `a`.`visitor_id` = :visitor_id AND
                      `a`.`active` = 1 AND
                      `b`.`visible` = 1 AND
                      `e`.`id` <> :group_id";
        $temp = $this->database->fetchAll(
            $query,
            array(
                'visitor_id' => $this->visitorId,
                'group_id'   => $this->groupId
            )
        );
        if ( ! empty($temp)) { // «удаляем» товары из «старого» сравнения
            foreach ($temp as $item) {
                $ids[] = $item['id'];
            }
            $query = "UPDATE
                          `compare`
                      SET
                          `active` = 0
                      WHERE
                          `id` IN (" . implode(',', $ids) . ") AND
                          `visitor_id` = :visitor_id";
            $this->database->execute(
                $query,
                array('visitor_id' => $this->visitorId)
            );
        }
        // обновляем cookie
        setcookie('compare_group', $this->groupId, time() + 31536000, '/');

        // если в списке сравнения оказались два одинаковых товара (это возможно,
        // если оба списка содержали товары одной функциональной группы)
        $query = "SELECT
                      MAX(`id`) AS `id`, `product_id`, COUNT(*) AS `count`
                  FROM
                      `compare`
                  WHERE
                      `visitor_id` = :visitor_id AND
                      `active` = 1
                  GROUP BY
                      `product_id`
                  HAVING
                      COUNT(*) > 1";
        $result = $this->database->fetchAll(
            $query,
            array('visitor_id' => $this->visitorId)
        );
        if (empty($result)) {
            return;
        }
        foreach ($result as $item) {
            $query = "DELETE FROM
                          `compare`
                      WHERE
                          `id` < :id AND
                          `product_id` = :product_id AND
                          `visitor_id` = :visitor_id AND
                          `active` = 1";
            $this->database->execute(
                $query,
                array(
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'visitor_id' => $this->visitorId
                )
            );
        }

    }

    // Получаем рекомендации для пользователя на основе отложенных для сравнения товаров
    public function getRecommendations() {

    }
}
