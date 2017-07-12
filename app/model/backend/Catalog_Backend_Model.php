<?php
/**
 * Класс Catalog_Backend_Model для работы с каталогом товаров,
 * взаимодействует с базой данных, административная часть сайта
 */
class Catalog_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех категорий (дерево каталога)
     */
    public function getAllCategories() {
        $query = "SELECT
                      `id`, `name`, `parent`
                  FROM
                      `categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Возвращает массив дочерних категорий категории с уникальным идентификатором $id
     */
    public function getChildCategories($id) {
        $query = "SELECT
                      `id`, `name`, `sortorder`, `globalsort`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :id
                  ORDER BY
                      `sortorder`";
        $childs = $this->database->fetchAll($query, array('id' => $id));
        // добавляем в массив URL ссылок для перехода, ссылок для смещения вверх/вниз,
        // редактирования и удаления категорий
        foreach($childs as $key => $value) {
            $childs[$key]['url'] = array(
                'link'   => $this->getURL('backend/catalog/category/id/' . $value['id']),
                'up'     => $this->getURL('backend/catalog/ctgup/id/' . $value['id']),
                'down'   => $this->getURL('backend/catalog/ctgdown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/catalog/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/catalog/rmvctg/id/' . $value['id'])
            );
        }
        return $childs;
    }

    /**
     * Возвращает массив идентификаторов дочерних категорий
     * категории с уникальным идентификатором $id
     */
    private function getChildIds($id) {
        $query = "SELECT
                      `id`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :id
                  ORDER BY
                      `sortorder`";
        $res = $this->database->fetchAll($query, array('id' => $id));
        $ids = array();
        foreach ($res as $item) {
            $ids[] = $item['id'];
        }
        return $ids;
    }

    /**
     * Возвращает массив идентификаторов всех потомков категории $id,
     * т.е. дочерние, дочерние дочерних и т.п.
     */
    public function getAllChildIds($id) {
        $childs = array();
        $ids = $this->getChildIds($id);
        foreach ($ids as $item) {
            $childs[] = $item;
            $c = $this->getChildIds($item);
            for ($i = 0; $i < count($c); $i++) {
                $childs[] = $c[$i];
            }
        }
        return $childs;
    }

    /**
     * Функция возвращает идентификатор родительского элемента категории
     * с уникальным идентификатором $id
     */
    public function getCategoryParent($id) {
        $query = "SELECT
                      `parent`
                  FROM
                      `categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает массив товаров категории с уникальным идентификатором $id
     */
    public function getCategoryProducts($id, $start) {
        $query = "SELECT
                      `id`, `name`, `title`, `sortorder`
                  FROM
                      `products`
                  WHERE
                      `category` = :id
                  ORDER BY
                      `sortorder`
                  LIMIT " . $start . ", " . $this->config->pager->backend->products->perpage;
        $products = $this->database->fetchAll($query, array('id' => $id));
        // добавляем в массив URL ссылок ссылок для смещения вверх/вниз, редактирования и удаления
        foreach($products as $key => $value) {
            $products[$key]['url'] = array(
                'up'     => $this->getURL('backend/catalog/prdup/id/' . $value['id']),
                'down'   => $this->getURL('backend/catalog/prddown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/catalog/editprd/id/' . $value['id']),
                'remove' => $this->getURL('backend/catalog/rmvprd/id/' . $value['id'])
            );
        }
        return $products;
    }

    /**
     * Возвращает количество товаров в категории с уникальным идентификатором $id
     */
    public function getCountCategoryProducts($id) {
        $query = "SELECT
                      COUNT(*)
                  FROM
                      `products`
                  WHERE
                      `category` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция возвращает путь от корня каталога до категории с уникальным
     * идентификатором $id
     */
    public function getCategoryPath($id) {
        $path = array();
        $current = $id;
        while ($current) {
            $query = "SELECT
                          `parent`, `name`
                      FROM
                          `categories`
                      WHERE
                          `id` = :current";
            $res = $this->database->fetch($query, array('current' => $current));
            $path[] = array(
                'url' => $this->getURL('backend/catalog/category/id/' . $current),
                'name' => $res['name']);
            $current = $res['parent'];
        }
        $path[] = array('url' => $this->getURL('backend/catalog/index'), 'name' => 'Каталог');
        $path[] = array('url' => $this->getURL('backend/index/index'), 'name' => 'Главная');
        $path = array_reverse($path);
        return $path;
    }

    /**
     * Функция возвращает уровень вложенности категории
     */
    private function getCategoryLevel($id) {
        return count($this->getCategoryParents($id));
    }

    /**
     * Функция возвращает массив всех предков категории $id
     */
    private function getCategoryParents($id) {
        $parents = array();
        $current = $id;
        while ($current) {
            $parents[] = $current;
            $query = "SELECT
                          `parent`
                      FROM
                          `categories`
                      WHERE
                          `id` = :current";
            $current = $this->database->fetchOne($query, array('current' => $current));
        }
        return $parents;
    }

    /**
     * Функция возвращает идентификатор родительской категории товара
     * с уникальным идентификатором $id
     */
    public function getProductParent($id) {
        $query = "SELECT
                      `category`
                  FROM
                      `products`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает информацию о товаре с уникальным идентификатором $id
     */
    public function getProduct($id) {

        $query = "SELECT
                      `category`, `category2`, `group`, `maker`, `code`, `name`, `title`,
                      `keywords`, `description`, `shortdescr`, `purpose`, `techdata`,
                      `features`, `complect`, `equipment`, `padding`, `price`, `price2`,
                      `price3`, `price4`, `price5`, `price6`, `price7`, `unit`, `image`,
                      `related`, `sortorder`
                  FROM
                      `products`
                  WHERE
                      `id` = :id";
        $product = $this->database->fetch($query, array('id' => $id));
        if (false === $product) {
            return null;
        }
        // добавляем информацию о параметрах подбора
        $product['params'] = array();
        if (!empty($product['group'])) {
            $query = "SELECT
                          `a`.`id` AS `param_id`, GROUP_CONCAT(`b`.`id`) AS `ids`
                      FROM
                          `product_param_value` `c`
                          INNER JOIN `params` `a` ON `c`.`param_id` = `a`.`id`
                          INNER JOIN `values` `b` ON `c`.`value_id` = `b`.`id`
                      WHERE
                          `c`.`product_id` = :product_id
                      GROUP BY
                          `a`.`id`";
            $result = $this->database->fetchAll($query, array('product_id' => $id));
            foreach ($result as $item) {
                $product['params'][$item['param_id']] = explode(',', $item['ids']);
            }
        }
        // добавляем информацию о файлах документации
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`title` AS `title`,
                      `a`.`filename` AS `filename`, `a`.`filetype` AS `filetype`
                  FROM
                      `docs` `a` INNER JOIN `doc_prd` `b`
                      ON `a`.`id`=`b`.`doc_id`
                  WHERE
                      `b`.`prd_id` = :id
                  ORDER BY
                      `a`.`title`";
        $product['docs'] = $this->database->fetchAll($query, array('id' => $id));
        // файл изображения
        if ( ! empty($product['image'])) {
            $product['image'] = $this->config->site->url . 'files/catalog/imgs/big/' . $product['image'];
        }

        return $product;

    }

    /**
     * Функция добавляет новый товар (новую запись в таблицу products базы данных)
     */
    public function addProduct($data) {

        // порядок сортировки
        $data['sortorder'] = 0;
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `products`
                  WHERE
                      `category` = :category";
        $data['sortorder'] = $this->database->fetchOne($query, array('category' => $data['category'])) + 1;

        $params = $data['params'];
        unset($data['params']);

        $query = "INSERT INTO `products`
                  (
                      `category`,
                      `category2`,
                      `group`,
                      `maker`,
                      `code`,
                      `name`,
                      `title`,
                      `keywords`,
                      `description`,
                      `shortdescr`,
                      `purpose`,
                      `techdata`,
                      `features`,
                      `complect`,
                      `equipment`,
                      `padding`,
                      `price`,
                      `price2`,
                      `price3`,
                      `price4`,
                      `price5`,
                      `price6`,
                      `price7`,
                      `unit`,
                      `related`,
                      `sortorder`,
                      `updated`
                  )
                  VALUES
                  (
                      :category,
                      :category2,
                      :group,
                      :maker,
                      :code,
                      :name,
                      :title,
                      :keywords,
                      :description,
                      :shortdescr,
                      :purpose,
                      :techdata,
                      :features,
                      :complect,
                      :equipment,
                      :padding,
                      :price,
                      :price2,
                      :price3,
                      :price4,
                      :price5,
                      :price6,
                      :price7,
                      :unit,
                      :related,
                      :sortorder,
                      NOW()
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // параметры подбора
        foreach ($params as $key => $value) {
            foreach ($value as $k => $v) {
                $query = "INSERT INTO `product_param_value`
                          (
                              `product_id`,
                              `param_id`,
                              `value_id`
                          )
                          VALUES
                          (
                              :product_id,
                              :param_id,
                              :value_id
                          )";
                $this->database->execute(
                    $query,
                    array(
                        'product_id' => $id,
                        'param_id'   => $key,
                        'value_id'   => $v
                    )
                );
            }
        }

        // загружаем файл изображения
        $this->uploadProductImage($id);

        // загружаем файлы документации
        $this->uploadDocFiles($id);

    }

    /**
     * Функция обновляет товар (запись в таблице product базы данных)
     */
    public function updateProduct($data) {

        // порядок сортировки
        $query = "SELECT
                      `category`
                  FROM
                      `products`
                  WHERE
                      `id` = :id";
        $oldCategory = $this->database->fetchOne($query, array('id' => $data['id']));
        if ($data['category'] != $oldCategory) { // если товар перемещается в другую категорию
            // в новой категории он будет в конце списка
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `products`
                      WHERE
                          `category` = :category";
            $sortorder = $this->database->fetchOne($query, array('category' => $data['category'])) + 1;
            // перемещаем товар в новую категорию
            $query = "UPDATE
                          `products`
                      SET
                          `category`  = :category,
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'category'  => $data['category'],
                    'sortorder' => $sortorder,
                    'id'        => $data['id']
                )
            );
            // обновляем порядок сортировки в старой родительской категории
            $query = "SELECT
                          `id`
                      FROM
                          `products`
                      WHERE
                          `category` = :old_category
                      ORDER BY
                          `sortorder`";
            $result = $this->database->fetchAll($query, array('old_category' => $oldCategory));
            $sortorder = 1;
            foreach ($result as $prd) {
                $query = "UPDATE
                              `products`
                          SET
                              `sortorder` = :sortorder
                          WHERE
                              `id` = :id";
                $this->database->execute(
                    $query,
                    array(
                        'sortorder' => $sortorder,
                        'id'        => $prd['id']
                    )
                );
                $sortorder++;
            }
        }
        unset($data['category']);
        $params = $data['params'];
        unset($data['params']);

        $query = "UPDATE
                      `products`
                  SET
                      `category2`   = :category2,
                      `group`       = :group,
                      `maker`       = :maker,
                      `code`        = :code,
                      `name`        = :name,
                      `title`       = :title,
                      `keywords`    = :keywords,
                      `description` = :description,
                      `shortdescr`  = :shortdescr,
                      `purpose`     = :purpose,
                      `techdata`    = :techdata,
                      `features`    = :features,
                      `complect`    = :complect,
                      `equipment`   = :equipment,
                      `padding`     = :padding,
                      `price`       = :price,
                      `price2`      = :price2,
                      `price3`      = :price3,
                      `price4`      = :price4,
                      `price5`      = :price5,
                      `price6`      = :price6,
                      `price7`      = :price7,
                      `unit`        = :unit,
                      `related`     = :related,
                      `updated`     = NOW()
                  WHERE
                      `id` = :id";

        $this->database->execute($query, $data);

        // параметры подбора
        $query = "DELETE FROM
                      `product_param_value`
                  WHERE
                      `product_id` = :product_id";
        $this->database->execute($query, array('product_id' => $data['id']));
        foreach ($params as $key => $value) {
            foreach ($value as $k => $v) {
                $query = "INSERT INTO `product_param_value`
                          (
                              `product_id`,
                              `param_id`,
                              `value_id`
                          )
                          VALUES
                          (
                              :product_id,
                              :param_id,
                              :value_id
                          )";
                $this->database->execute(
                    $query,
                    array(
                        'product_id' => $data['id'],
                        'param_id'   => $key,
                        'value_id'   => $v
                    )
                );
            }
        }

        // загружаем файл изображения
        $this->uploadProductImage($data['id']);

        // загружаем файлы документации
        $this->uploadDocFiles($data['id']);

    }

    /**
     * Функция возвращает массив всех функциональных групп для
     * возможности выбора при добавлении/редактировании товара
     */
    public function getAllGroups() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `groups`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает массив параметров, привязанных к группе $id и массивы
     * привязанных к этим параметрам значений
     */
    public function getGroupParams($id) {
        if (0 == $id) {
            return array();
        }
        $query = "SELECT
                      `a`.`id` AS `param_id`, `a`.`name` AS `param_name`,
                      `c`.`id` AS `value_id`, `c`.`name` AS `value_name`
                  FROM
                      `params` `a`
                      INNER JOIN `group_param_value` `b` ON `a`.`id` = `b`.`param_id`
                      INNER JOIN `values` `c` ON `b`.`value_id` = `c`.`id`
                  WHERE
                      `b`.`group_id` = :group_id
                  ORDER BY
                      `param_name`, `value_name`";
        $result = $this->database->fetchAll($query, array('group_id' => $id));

        $params = array();
        $param_id = 0;
        $counter = -1;
        foreach ($result as $value) {
            if ($param_id != $value['param_id']) {
                $counter++;
                $param_id = $value['param_id'];
                $params[$counter] = array('id' => $value['param_id'], 'name' => $value['param_name']);
            }
            $params[$counter]['values'][] = array('id' => $value['value_id'], 'name' => $value['value_name']);
        }

        return $params;
    }

    /**
     * Функция возвращает массив параметров, привязанных к товару $id и массивы
     * привязанных к этим параметрам значений
     */
    public function getProductParams($id) {
        if (0 == $id) {
            return array();
        }
        $query = "SELECT
                      `a`.`id` AS `param_id`, GROUP_CONCAT(`b`.`id`) AS `ids`
                  FROM
                      `product_param_value` `c`
                      INNER JOIN `params` `a` ON `c`.`param_id` = `a`.`id`
                      INNER JOIN `values` `b` ON `c`.`value_id` = `b`.`id`
                  WHERE
                      `c`.`product_id` = :product_id
                  GROUP BY
                      `a`.`id`
                  ORDER BY
                      `a`.`name`, `b`.`name`";
        $result = $this->database->fetchAll($query, array('product_id' => $id));
        $params = array();
        foreach ($result as $item) {
            $params[$item['param_id']] = explode(',', $item['ids']);
        }
        return $params;
    }

    /**
     * Функция загружает файл изображения товара с уникальным идентификатором $id
     */
    private function uploadProductImage($id) {

        // удалить файл изображения товара (при обновлении товара)?
        if (isset($_POST['remove_image'])) {
            $this->removeProductImage($id);
        }

        // проверяем, пришел ли файл изображения товара
        if ( ! empty($_FILES['image']['name'])) {
            // проверяем, что при загрузке не произошло ошибок
            if ($_FILES['image']['error'] == 0) {
                // если файл загружен успешно, то проверяем - изображение?
                $mimetypes = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
                if (in_array($_FILES['image']['type'], $mimetypes)) {
                    // сначала удаляем старое изображение (при обновлении товара)
                    // если оно уже не было удалено чуть раньше
                    if ( ! isset($_POST['remove_image'])) {
                        $this->removeProductImage($id);
                    }
                    // имя файла
                    $name = md5(uniqid(rand(), true)).'.jpg';
                    $image = $name[0] . '/' . $name[1] . '/' . $name;
                    if ( ! is_dir('files/catalog/imgs/small/' . $name[0])) {
                        mkdir('files/catalog/imgs/small/' . $name[0]);
                    }
                    if ( ! is_dir('files/catalog/imgs/small/' . $name[0] . '/' . $name[1])) {
                        mkdir('files/catalog/imgs/small/' . $name[0] . '/' . $name[1]);
                    }
                    if ( ! is_dir('files/catalog/imgs/medium/' . $name[0])) {
                        mkdir('files/catalog/imgs/medium/' . $name[0]);
                    }
                    if ( ! is_dir('files/catalog/imgs/medium/' . $name[0] . '/' . $name[1])) {
                        mkdir('files/catalog/imgs/medium/' . $name[0] . '/' . $name[1]);
                    }
                    if ( ! is_dir('files/catalog/imgs/big/' . $name[0])) {
                        mkdir('files/catalog/imgs/big/' . $name[0]);
                    }
                    if ( ! is_dir('files/catalog/imgs/big/' . $name[0] . '/' . $name[1])) {
                        mkdir('files/catalog/imgs/big/' . $name[0] . '/' . $name[1]);
                    }
                    // изменяем размер изображения
                    $this->resizeImage( // маленькое
                        $_FILES['image']['tmp_name'],
                        'files/catalog/imgs/small/' . $image,
                        100,
                        100,
                        'jpg'
                    );
                    $this->resizeImage( // среднее
                        $_FILES['image']['tmp_name'],
                        'files/catalog/imgs/medium/' . $image,
                        200,
                        200,
                        'jpg'
                    );
                    $this->resizeImage( // большое
                        $_FILES['image']['tmp_name'],
                        'files/catalog/imgs/big/' . $image,
                        500,
                        500,
                        'jpg'
                    );
                    // обновляем запись в таблице products
                    $query = "UPDATE
                                  `products`
                             SET
                                 `image` = :image
                             WHERE
                                 `id` = :id";
                    $this->database->execute(
                        $query,
                        array(
                            'image' => $image,
                            'id'    => $id
                        )
                    );
                }
            }
        }
    }

    /**
     * Функция удаляет файл изображения товара с уникальным идентификатором $id
     */
    private function removeProductImage($id) {
        $query = "SELECT
                      `image`
                  FROM
                      `products`
                  WHERE
                      `id` = :id";
        $image = $this->database->fetchOne($query, array('id' => $id));
        if (empty($image)) {
            return;
        }
        if (is_file('files/catalog/imgs/big/' . $image)) {
            unlink('files/catalog/imgs/big/' . $image);
        }
        if (is_file('files/catalog/imgs/medium/' . $image)) {
            unlink('files/catalog/imgs/medium/' . $image);
        }
        if (is_file('files/catalog/imgs/small/' . $image)) {
            unlink('files/catalog/imgs/small/' . $image);
        }
        $query = "UPDATE
                      `products`
                  SET
                      `image` = ''
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
    }

    /**
     * Функция загружает новые файлы документации для товара с уникальным
     * идентификатором $id, удаляет старые и обновляет информацию о файлах
     * документации
     */
    private function uploadDocFiles($id) {

        // изменяем поле наименования ранее загруженных файлов
        if (isset($_POST['update_doc_ids'])) {
            $count = count($_POST['update_doc_ids']);
            for ($i = 0; $i < $count; $i++) {
                if ( ! ctype_digit($_POST['update_doc_ids'][$i])) {
                    continue;
                }
                $title = trim(iconv_substr($_POST['update_doc_titles'][$i], 0, 120));
                if (empty($title)) {
                    continue;
                }
                $query = "UPDATE
                              `docs`
                          SET
                              `title` = :title
                          WHERE
                              `id` = :id";
                $this->database->execute($query, array('title' => $title, 'id' => $_POST['update_doc_ids'][$i]));
            }
        }

        // удаляем файлы документации, загруженные ранее
        if (isset($_POST['remove_doc_ids'])) {
            foreach ($_POST['remove_doc_ids'] as $doc_id) {
                // получаем имя файла
                $query = "SELECT
                              `a`.`filename`
                          FROM
                              `docs` `a` INNER JOIN `doc_prd` `b`
                              ON `a`.`id`=`b`.`doc_id`
                          WHERE
                              `b`.`doc_id` = :doc_id AND `b`.`prd_id` = :prd_id";
                $fileName = $this->database->fetchOne($query, array('prd_id' => $id, 'doc_id' => $doc_id));
                if (false === $fileName) {
                    continue;
                }
                // удаляем привязку файла документации к товару
                $query = "DELETE FROM
                              `doc_prd`
                          WHERE
                              `doc_id` = :doc_id AND `prd_id` = :prd_id";
                $this->database->execute($query, array('doc_id' => $doc_id, 'prd_id' => $id));
                // проверяем, надо ли удалять файл - он может быть привязан еще к какому-нибудь товару
                $query = "SELECT
                              1
                          FROM
                              `doc_prd`
                          WHERE
                              `doc_id` = :doc_id";
                $res = $this->database->fetchOne($query, array('doc_id' => $doc_id));
                if (false === $res) { // можно удалять сам файл и запись о нем в таблице БД docs
                    if (is_file( 'files/catalog/docs/' . $fileName ) ) {
                        unlink( 'files/catalog/docs/' . $fileName );
                    }
                    $query = "DELETE FROM
                                  `docs`
                              WHERE
                                  `id` = :doc_id";
                    $this->database->execute($query, array('doc_id' => $doc_id));
                }
            }
        }

        // загружаем новые файлы документации
        $mimeTypes = array(
            'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png', 'image/bmp',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip', 'application/x-zip-compressed', 'application/pdf');
        $exts = array(
            'jpg', 'jpeg', 'gif', 'png', 'bmp', 'doc', 'docx',
            'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'pdf'
        );
        $count = count($_FILES['add_doc_files']['name']);
        for ($i = 0; $i < $count; $i++) { // цикл по всем загруженным файлам
            if (empty($_POST['add_doc_titles'][$i])) { // если не заполнено поле наименования документа
                continue;
            }
            if ($_FILES['add_doc_files']['error'][$i]) { // произошла ошибка при загрузке файла
                continue;
            }
            $ext = pathinfo($_FILES['add_doc_files']['name'][$i], PATHINFO_EXTENSION);
            if ( ! in_array($ext, $exts)) { // недопустимое расширение файла
                continue;
            }
            if ( ! in_array($_FILES['add_doc_files']['type'][$i], $mimeTypes) ) { // недопустимый mime-тип файла
                continue;
            }
            $title = trim(iconv_substr($_POST['add_doc_titles'][$i], 0, 120));
            // сумма md5 загружаемого файла; нам надо проверить - есть уже такой файл?
            $md5 = md5_file($_FILES['add_doc_files']['tmp_name'][$i]);
            $query = "SELECT
                          `id`
                      FROM
                          `docs`
                      WHERE
                          `md5` = :md5";
            $res = $this->database->fetchOne($query, array('md5' => $md5));
            if (false === $res) { // такого файла еще нет
                $name = md5(uniqid(rand(), true)) . '.' . $ext;
                if (!is_dir('files/catalog/docs/' . $name[0])) {
                    mkdir('files/catalog/docs/' . $name[0]);
                }
                if (!is_dir('files/catalog/docs/' . $name[0] . '/' . $name[1])) {
                    mkdir('files/catalog/docs/' . $name[0] . '/' . $name[1]);
                }
                $fileName = $name[0] . '/' . $name[1] . '/' . $name;
                // сохраняем файл
                if ( ! move_uploaded_file($_FILES['add_doc_files']['tmp_name'][$i], 'files/catalog/docs/' . $fileName)) {
                    continue;
                }
                $query = "INSERT INTO `docs`
                          (
                              `title`,
                              `filename`,
                              `filetype`,
                              `md5`,
                              `uploaded`
                          )
                          VALUES
                          (
                              :title,
                              :filename,
                              :filetype,
                              :md5,
                              NOW()
                          )";
                $data = array(
                    'title'    => $title,
                    'filename' => $fileName,
                    'filetype' => $ext,
                    'md5'      => $md5
                );
                $this->database->execute($query, $data);
                $docId = $this->database->lastInsertId();
            } else { // такой файл уже есть
                $docId = $res;
            }
            // теперь привязываем файл к товару
            $query = "INSERT IGNORE INTO `doc_prd`
                      (
                          `prd_id`,
                          `doc_id`
                      )
                      VALUES
                      (
                          :prd_id,
                          :doc_id
                      )";
            $this->database->execute($query, array('prd_id' => $id, 'doc_id' => $docId));
        }
    }

    /**
     * Функция опускает товар вниз в списке
     */
    public function moveProductDown($id) {
        $id_item_down = $id;
        // получаем порядок следования и родительскую категорию товара, который опускается вниз
        $query = "SELECT
                      `sortorder`, `category`
                  FROM
                      `products`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $parent = $res['category'];
        // получаем порядок следования и id товара, который находится ниже и будет
        // поднят вверх, поменявшись местами с товаром, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `products`
                  WHERE
                      `category` = :parent AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('parent' => $parent, 'order_down' => $order_down));
        // если запрос вернул false, значит товар и так самый последний
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами товары
            $query = "UPDATE
                          `products`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute(
                $query,
                array(
                    'order_down' => $order_down,
                    'id_item_up' => $id_item_up
                )
            );
            $query = "UPDATE
                          `products`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up'     => $order_up,
                    'id_item_down' => $id_item_down
                )
            );
        }
    }

    /**
     * Функция поднимает товар вверх в списке
     */
    public function moveProductUp($id) {
        $id_item_up = $id;
        // получаем порядок следования и родительскую категорию товара, который поднимается вверх
        $query = "SELECT
                      `sortorder`, `category`
                  FROM
                      `products`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $parent = $res['category'];
        // получаем порядок следования и id товара, который находится выше и будет
        // опущен вниз, поменявшись местами с товаром, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `products`
                  WHERE
                      `category` = :parent AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('parent' => $parent, 'order_up' => $order_up));
        // если запрос вернул false, значит товар и так самый первый
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами товары
            $query = "UPDATE
                          `products`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute(
                $query,
                array(
                    'order_down' => $order_down,
                    'id_item_up' => $id_item_up
                )
            );
            $query = "UPDATE
                          `products`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up'     => $order_up,
                    'id_item_down' => $id_item_down
                )
            );
        }
    }

    /**
     * Функция возвращает информацию о категории с уникальным идентификатором $id
     */
    public function getCategory($id) {
        $query = "SELECT
                      `parent`, `name`, `keywords`, `description`
                  FROM
                      `categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет новую категорию (новую запись в таблицу categories базы данных)
     */
    public function addCategory($data) {
        // добавляем новую категорию в конец списка дочерних категорий
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `categories`
                  WHERE
                      `parent` = :parent";
        $data['sortorder'] = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;
        /*
         * Устанавливаем для новой категории значение globalsort, которое имеет вид 07120315000000000000;
         * здесь
         * 07 - порядок сортировки категории самого верхнего уровня,
         * 12 - порядок сортировки следующей категории в иерархии предков и т.п.
         * 15 - это порядок сортировки самой категории, совпадает с sortorder
         * Таким образом, чтобы получить значение globalsort для новой категории, нужно получить значение
         * globalsort родительской категории, например 07120300000000000000, взять оттуда 071203, добавить
         * к этому sortorder новой категории, например 15, и добавить в конце нули: 071203+15+000000000000
         */
        if ($data['parent']) { // если родитель - это обычная категория
            $query = "SELECT
                          `globalsort`
                      FROM
                          `categories`
                      WHERE
                          `id` = :parent";
            $parentSortorder = $this->database->fetchOne($query, array('parent' => $data['parent']));
            $parentLevel = $this->getCategoryLevel($data['parent']);
        } else { // если родитель - это корень каталога
            $parentSortorder = '00000000000000000000';
            $parentLevel = 0;
        }
        // начало и конец строки, задающей сортировку
        $before = substr($parentSortorder, 0, $parentLevel * 2);
        $after = str_repeat('0', 18 - $parentLevel * 2);
        $globalsort = $data['sortorder'];
        if (strlen($globalsort) == 1) {
            $globalsort = '0' . $globalsort;
        }
        $data['globalsort'] = $before . $globalsort . $after;
        // добавляем новую категорию
        $query = "INSERT INTO `categories`
                  (
                      `parent`,
                      `name`,
                      `keywords`,
                      `description`,
                      `sortorder`,
                      `globalsort`
                  )
                  VALUES
                  (
                      :parent,
                      :name,
                      :keywords,
                      :description,
                      :sortorder,
                      :globalsort
                  )";
        $this->database->execute($query, $data);
        // изменяем порядок сортировки категорий
        $this->updateSortOrderAllCategories($data['parent']);
    }

    /**
     * Функция обновляет категорию (запись в таблице categories базы данных)
     */
    public function updateCategory($data) {
        // получаем идентификатор родителя обновляемой категории
        $oldParent = $this->getCategoryParent($data['id']);
        // если изменился родитель обновляемой категории
        if ($oldParent != $data['parent']) {
            // добавляем обновляемую категорию в конец списка дочерних категорий нового родителя
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `categories`
                      WHERE
                          `parent` = :parent";
            $sortorder = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;
            $query = "UPDATE
                          `categories`
                      SET
                          `parent` = :parent,
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'parent'    => $data['parent'],
                    'sortorder' => $sortorder,
                    'id'        => $data['id'],
                )
            );
            // изменяем порядок сортировки категорий
            $this->updateSortOrderAllCategories($data['parent']);
            // обновляем порядок категорий внутри той категории, откуда была перемещена категория
            $query = "SELECT
                          `id`
                      FROM
                          `categories`
                      WHERE
                          `parent` = :oldparent
                      ORDER BY
                          `sortorder`";
            $result = $this->database->fetchAll($query, array('oldparent' => $oldParent));
            $sortorder = 1;
            foreach ($result as $ctg) {
                $query = "UPDATE
                              `categories`
                          SET
                              `sortorder` = :sortorder
                          WHERE
                              `id` = :id";
                $this->database->execute(
                    $query,
                    array(
                        'sortorder' => $sortorder,
                        'id'        => $ctg['id']
                    )
                );
                $sortorder++;
            }
            // изменяем порядок сортировки категорий
            $this->updateSortOrderAllCategories($oldParent);
        }

        // обновляем категорию
        unset($data['parent']);
        $query = "UPDATE
                      `categories`
                  SET
                      `name`        = :name,
                      `keywords`    = :keywords,
                      `description` = :description
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция опускает категорию вниз в списке
     */
    public function moveCategoryDown($id) {
        $id_item_down = $id;
        // получаем порядок следования и родителя категории, которая опускается вниз
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `categories`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $parent = $res['parent'];
        // получаем порядок следования и id категории, которая находится ниже и будет
        // поднята вверх, поменявшись местами с категорией, которая опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :parent AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'parent'     => $parent,
                'order_down' => $order_down
            )
        );
        // если запрос вернул false, значит категория и так самая последняя
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `categories`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute(
                $query,
                array(
                    'order_down' => $order_down,
                    'id_item_up' => $id_item_up
                )
            );
            $query = "UPDATE
                          `categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up'     => $order_up,
                    'id_item_down' => $id_item_down
                )
            );

            // изменяем порядок сортировки категорий
            $this->updateSortOrderAllCategories($parent);
        }
    }

    /**
     * Функция поднимает категорию вверх в списке
     */
    public function moveCategoryUp($id) {
        $id_item_up = $id;
        // получаем порядок следования и родителя категории, которая поднимается вверх
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `categories`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $parent = $res['parent'];
        // получаем порядок следования и id категории, которая находится выше и будет
        // опущена вниз, поменявшись местами с категорией, которая поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :parent AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'parent'   => $parent,
                'order_up' => $order_up
            )
        );
        // если запрос вернул false, значит категория и так самая первая
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `categories`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute(
                $query,
                array(
                    'order_down' => $order_down,
                    'id_item_up' => $id_item_up
                )
            );
            $query = "UPDATE
                          `categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up'     => $order_up,
                    'id_item_down' => $id_item_down
                )
            );

            // изменяем порядок сортировки категорий
            $this->updateSortOrderAllCategories($parent);
        }
    }

    /**
     * Функция удаляет категорию (запись в таблице categories базы данных)
     */
    public function removeCategory($id) {
        // нельзя удалить категорию, у которой есть дочерние категории
        $query = "SELECT
                      1
                  FROM
                      `categories`
                  WHERE
                      `parent` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // нельзя удалить категорию, которая содержит товары
        $query = "SELECT
                      1
                  FROM
                      `products`
                  WHERE
                      `category` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // перед удалением категории получаем идентификатор ее родителя
        $parent = $this->getCategoryParent($id);
        // удаляем категорию
        $query = "DELETE FROM
                      `categories`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // если эта категория была дополнительной для каких-то товаров
        $query = "UPDATE
                      `products`
                  SET
                      `category2` = 0
                  WHERE
                      `category2` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования внутри родительской категории удаленной
        $query = "SELECT
                      `id`
                  FROM
                      `categories`
                  WHERE
                      `parent` = :parent
                  ORDER BY
                      `sortorder`";
        $result = $this->database->fetchAll($query, array('parent' => $parent));
        $sortorder = 1;
        if (count($result) > 0) {
            foreach ($result as $ctg) {
                $query = "UPDATE
                              `categories`
                          SET
                              `sortorder` = :sortorder
                          WHERE
                              `id` = :id";
                $this->database->execute(
                    $query,
                    array(
                        'sortorder' => $sortorder,
                        'id'        => $ctg['id']
                    )
                );
                $sortorder++;
            }
        }

        // изменяем порядок сортировки категорий
        $this->updateSortOrderAllCategories($parent);

        return true;
    }

    /**
     * Функция обновляет порядок сортировки всех потомков категории $id
     */
    private function updateSortOrderAllCategories($id = 0) {
        /*
         * Значение globalsort, которое обновляет данная функция для всех потомков категории $id,
         * имеет вид 07120315000000000000, где
         * 07 - порядок сортировки категории самого верхнего уровня,
         * 12 - порядок сортировки следующей категории в иерархии предков и т.п.
         * 15 - это порядок сортировки самой категории, совпадает с sortorder
         * Таким образом, чтобы получить значение globalsort для какой-нибудь категории, нужно взять
         * значение globalsort родительской категории, например 07120300000000000000, извлечь оттуда
         * 071203, добавить к этому sortorder этой категории, например 15, и добавить в конце нули:
         * 071203+15+000000000000
         */

        // получаем порядок сортировки категории
        if ($id) { // если это обычная категория
            $query = "SELECT `globalsort` FROM `categories` WHERE `id` = :id";
            $parentSortorder = $this->database->fetchOne($query, array('id' => $id));
            $parentLevel = $this->getCategoryLevel($id);
        } else { // если это корень каталога, т.е. $id = 0
            $parentSortorder = '00000000000000000000';
            $parentLevel = 0;
        }

        // начало и конец строки, задающей сортировку
        $before = substr($parentSortorder, 0, $parentLevel * 2);
        $after = str_repeat('0', 18 - $parentLevel * 2);
        // получаем массив дочерних категорий
        $query = "SELECT `id` FROM `categories` WHERE `parent` = :id ORDER BY `sortorder`";
        $childs = $this->database->fetchAll($query, array('id' => $id));

        $i = 1;
        foreach ($childs as $child) {
            $globalsort = $i;
            if (strlen($globalsort) == 1) {
                $globalsort = '0' . $globalsort;
            }
            $globalsort = $before . $globalsort . $after;
            $query = "UPDATE `categories` SET `globalsort` = :globalsort WHERE `id` = :id";
            $this->database->execute($query, array('globalsort' => $globalsort, 'id' => $child['id']));
            // рекурсивно вызываем $this->updateSortOrderAllCategories()
            $this->updateSortOrderAllCategories($child['id']);
            $i++;
        }
    }

    /**
     * Функция возвращает массив всех производителей для контроллера,
     * отвечающего за вывод всех производителей
     */
    public function getAllMakers() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `makers`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        $makers = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($makers as $key => $value) {
            $makers[$key]['url'] = array(
                'edit'   => $this->getURL('backend/catalog/editmkr/id/' . $value['id']),
                'remove' => $this->getURL('backend/catalog/rmvmkr/id/' . $value['id'])
            );
        }
        return $makers;
    }

    /**
     * Функция возвращает массив всех производителей для контроллеров,
     * отвечающих за добавление и редактирование производителей
     */
    public function getMakers() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `makers`
                  WHERE
                      1
                  ORDER BY
                      `name`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает информацию о производителе с уникальным
     * идентификатором $id
     */
    public function getMaker($id) {
        $query = "SELECT
                      `id`, `name`, `altname`, `keywords`, `description`,
                      `brand`, `popular`, `logo`, `cert`, `body`
                  FROM
                      `makers`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет нового производителя (новую запись в таблицу
     * makers базы данных)
     */
    public function addMaker($data) {
        $query = "INSERT INTO `makers`
                  (
                      `name`,
                      `altname`,
                      `keywords`,
                      `description`,
                      `brand`,
                      `popular`,
                      `body`
                  )
                  VALUES
                  (
                      :name,
                      :altname,
                      :keywords,
                      :description,
                      :brand,
                      :popular,
                      :body
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // загружаем файл логотипа
        $this->uploadMakerLogo($id);
        // загружаем файл сертификата
        $this->uploadMakerCert($id);
    }

    /**
     * Функция загружает файл логотипа для производителя с
     * уникальным идентификатором $id
     */
    private function uploadMakerLogo($id) {

        // проверяем, пришел ли файл изображения
        if ( ! empty($_FILES['logo']['name'])) {
            // сначала удаляем старый логотип
            $this->removeMakerLogo($id);
            // проверяем, что при загрузке не произошло ошибок
            if ($_FILES['logo']['error'] == 0) {
                // если файл загружен успешно, то проверяем - изображение?
                $mimetypes = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
                if (in_array($_FILES['logo']['type'], $mimetypes)) {
                    $logo = md5(uniqid(rand(), true));
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['logo']['tmp_name'],
                        'files/catalog/makers/logo/'. $logo . '.jpg',
                        120,
                        60,
                        'jpg'
                    );
                    $query = "UPDATE
                                  `makers`
                              SET
                                  `logo` = :logo
                              WHERE
                                  `id` = :id";
                    $this->database->execute(
                        $query,
                        array(
                            'logo' => $logo,
                            'id' => $id
                        )
                    );
                }
            }
        }
    }

    /**
     * Функция загружает файл партнерского сертификата для производителя
     * с уникальным идентификатором $id
     */
    private function uploadMakerCert($id) {

        // проверяем, пришел ли файл изображения
        if ( ! empty($_FILES['cert']['name'])) {
            // сначала удаляем старый сертификат
            $this->removeMakerCert($id);
            // проверяем, что при загрузке не произошло ошибок
            if ($_FILES['cert']['error'] == 0) {
                // если файл загружен успешно, то проверяем - изображение?
                $mimetypes = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
                if (in_array($_FILES['cert']['type'], $mimetypes)) {
                    $cert = md5(uniqid(rand(), true));
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['cert']['tmp_name'],
                        'files/catalog/makers/cert/thumb/'. $cert . '.jpg',
                        200,
                        200,
                        'jpg',
                        array(245, 245, 245)
                    );
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['cert']['tmp_name'],
                        'files/catalog/makers/cert/image/'. $cert . '.jpg',
                        1000,
                        1000,
                        'jpg',
                        array(245, 245, 245)
                    );
                    $query = "UPDATE
                                  `makers`
                              SET
                                  `cert` = :cert
                              WHERE
                                  `id` = :id";
                    $this->database->execute(
                        $query,
                        array(
                            'cert' => $cert,
                            'id' => $id
                        )
                    );
                }
            }
        }
    }

    /**
     * Функция обновляет информацию о производителе (запись в таблице
     * makers базы данных)
     */
    public function updateMaker($data) {

        $query = "UPDATE
                      `makers`
                  SET
                      `name`        = :name,
                      `altname`     = :altname,
                      `keywords`    = :keywords,
                      `description` = :description,
                      `brand`       = :brand,
                      `popular`     = :popular,
                      `body`        = :body
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

        // удалить файл логотипа?
        if (isset($_POST['remove_logo'])) {
            $this->removeMakerLogo($data['id']);
        }
        // удалить файл сертификата?
        if (isset($_POST['remove_cert'])) {
            $this->removeMakerCert($data['id']);
        }

        // загружаем файл логотипа
        $this->uploadMakerLogo($data['id']);
        // загружаем файл сертификата
        $this->uploadMakerCert($data['id']);

    }

    /**
     * Функция удаляет производителя (запись в таблице makers базы данных)
     */
    public function removeMaker($id) {

        // если есть товары этого производителя
        $query = "SELECT 1 FROM `products` WHERE `maker` = :id LIMIT 1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }

        // удаляем файлы логотипа и сертификата
        $this->removeMakerLogo($id);
        $this->removeMakerCert($id);

        // удаляем производителя
        $query = "DELETE FROM `makers` WHERE `id` = :id";
        $this->database->execute($query, array('id' => $id));
        return true;

    }

    /**
     * Функция удаляет файл изображения логотипа производителя
     */
    private function removeMakerLogo($id) {
        $query = "SELECT
                      `logo`
                  FROM
                      `makers`
                  WHERE
                      `id` = :id";
        $logo = $this->database->fetchOne($query, array('id' => $id));
        if ( ! empty($logo)) {
            if (is_file('files/catalog/makers/logo/'. $logo . '.jpg')) {
                unlink('files/catalog/makers/logo/'. $logo . '.jpg');
            }
        }
        $query = "UPDATE
                      `makers`
                  SET
                      `logo` = ''
                  WHERE
                      `id` = :id";
        $this->database->execute(
            $query,
            array(
                'id' => $id
            )
        );
    }

    /**
     * Функция удаляет файл сертификата производителя
     */
    private function removeMakerCert($id) {
        $query = "SELECT
                      `cert`
                  FROM
                      `makers`
                  WHERE
                      `id` = :id";
        $cert = $this->database->fetchOne($query, array('id' => $id));
        if ( ! empty($cert)) {
            if (is_file('files/catalog/makers/cert/thumb/'. $cert . '.jpg')) {
                unlink('files/catalog/makers/cert/thumb/'. $cert . '.jpg');
            }
            if (is_file('files/catalog/makers/cert/image/'. $cert . '.jpg')) {
                unlink('files/catalog/makers/cert/image/'. $cert . '.jpg');
            }
        }
        $query = "UPDATE
                      `makers`
                  SET
                      `cert` = ''
                  WHERE
                      `id` = :id";
        $this->database->execute(
            $query,
            array(
                'id' => $id
            )
        );
    }

}