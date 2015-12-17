<?php
/**
 * Класс Sale_Backend_Model для работы с товарами по сниженным ценам,
 * взаимодействует с базой данных, административная часть сайта
 */
class Sale_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех товаров по сниженным ценам
     */
    public function getAllProducts() {

        // получаем все товары и категории
        $query = "SELECT
                      `b`.`id` AS `id`, `b`.`code` AS `code`,
                      `b`.`name` AS `name`, `b`.`title` AS `title`,
                      `b`.`count` AS `count`, `b`.`price1` AS `price1`,
                      `b`.`price2` AS `price2`, `b`.`unit` AS `unit`,
                      `a`.`id` AS `ctg_id`, `a`.`name` AS `ctg_name`,
                      `a`.`sortorder` AS `ctg_sort`, `b`.`sortorder` AS `prd_sort`
                  FROM
                      `sale_categories` `a` LEFT JOIN `sale_products` `b`
                      ON `a`.`id` = `b`.`category`
                  WHERE
                      1
                  ORDER BY
                      `a`.`sortorder`, `b`.`sortorder`";
        $result = $this->database->fetchAll($query);

        // добавляем в массив товаров URL ссылок для редактирования
        // и удаления и изменяем структуру массива
        $products = array();
        $ctg_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($ctg_id != $value['ctg_id']) {
                $counter++;
                $ctg_id = $value['ctg_id'];
                $products[$counter] = array(
                    'number'  => $value['ctg_sort'],
                    'name'    => $value['ctg_name'],
                    'ctgup'   => $this->getURL('backend/sale/ctgup/id/' . $value['ctg_id']),
                    'ctgdown' => $this->getURL('backend/sale/ctgdown/id/' . $value['ctg_id']),
                    'edit'    => $this->getURL('backend/sale/editctg/id/' . $value['ctg_id']),
                    'remove'  => $this->getURL('backend/sale/rmvctg/id/' . $value['ctg_id'])
                );
            }
            if ( ! empty($value['id'])) {
                $products[$counter]['products'][] = array(
                    'number'  => $value['prd_sort'],
                    'name'    => $value['name'],
                    'title'   => $value['title'],
                    'count'   => $value['count'],
                    'price1'  => $value['price1'],
                    'price2'  => $value['price2'],
                    'unit'    => $value['unit'],
                    'prdup'   => $this->getURL('backend/sale/prdup/id/' . $value['id']),
                    'prddown' => $this->getURL('backend/sale/prddown/id/' . $value['id']),
                    'edit'    => $this->getURL('backend/sale/editprd/id/' . $value['id']),
                    'remove'  => $this->getURL('backend/sale/rmvprd/id/' . $value['id'])
                );
            } else {
                $products[$counter]['products'] = array();
            }
        }

        return $products;

    }


    /**
     * Возвращает информацию о товаре с уникальным идентификатором $id
     */
    public function getProduct($id) {
        $query = "SELECT
                      `code`, `name`, `title`, `count`, `description`,
                      `price1`, `price2`, `unit`, `category`
                  FROM
                      `sale_products`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет товар (новую запись в таблицу `sale_products` базы данных)
     */
    public function addProduct($data) {

        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `sale_products`
                  WHERE
                      `category` = :category";
        $data['sortorder'] = $this->database->fetchOne($query, array('category' => $data['category'])) + 1;

        // добавляем товар
        $query = "INSERT INTO `sale_products`
                  (
                      `category`,
                      `code`,
                      `name`,
                      `title`,
                      `count`,
                      `description`,
                      `price1`,
                      `price2`,
                      `unit`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :category,
                      :code,
                      :name,
                      :title,
                      :count,
                      :description,
                      :price1,
                      :price2,
                      :unit,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // загружаем файл изображения
        $this->uploadImage($id);

    }

    /**
     * Функция обновляет товар (запись в таблице `sale_products` базы данных)
     */
    public function updateProduct($data) {

        // порядок сортировки
        $oldCategory = $this->getProductCategory($data['id']);
        if ($data['category'] != $oldCategory) { // если товар перемещается в другую категорию
            // в новой категории он будет в конце списка
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `sale_products`
                      WHERE
                          `category` = :category";
            $sortorder = $this->database->fetchOne($query, array('category' => $data['category'])) + 1;
            // перемещаем товар в новую категорию
            $query = "UPDATE
                          `sale_products`
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
                          `sale_products`
                      WHERE
                          `category` = :old_category
                      ORDER BY
                          `sortorder`";
            $result = $this->database->fetchAll($query, array('old_category' => $oldCategory));
            $sortorder = 1;
            foreach ($result as $item) {
                $query = "UPDATE
                              `sale_products`
                          SET
                              `sortorder` = :sortorder
                          WHERE
                              `id` = :id";
                $this->database->execute(
                    $query,
                    array(
                        'sortorder' => $sortorder,
                        'id'        => $item['id']
                    )
                );
                $sortorder++;
            }
        }
        unset($data['category']);

        // обновляем товар
        $query = "UPDATE
                      `sale_products`
                  SET
                      `code`        = :code,
                      `name`        = :name,
                      `title`       = :title,
                      `count`       = :count,
                      `description` = :description,
                      `price1`      = :price1,
                      `price2`      = :price2,
                      `unit`        = :unit
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

        // загружаем файл изображения
        $this->uploadImage($data['id']);

    }

    /**
     * Функция загружает файл изображения для товара с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // удаляем изображение, загруженное ранее
        if (isset($_POST['remove_image'])) {
            if (is_file('files/sale/' . $id . '.jpg')) {
                unlink('files/sale/' . $id . '.jpg');
            }
        }

        // проверяем, пришел ли файл изображения
        if ( ! empty($_FILES['image']['name'])) {
            // проверяем, что при загрузке не произошло ошибок
            if (0 == $_FILES['image']['error']) {
                // если файл загружен успешно, то проверяем - изображение?
                $mimetypes = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
                if (in_array($_FILES['image']['type'], $mimetypes)) {
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['image']['tmp_name'],
                        'files/sale/' . $id . '.jpg',
                        60,
                        0,
                        'jpg'
                    );
                }
            }
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
                      `sale_products`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $category = $res['category'];
        // получаем порядок следования и id товара, который находится ниже и будет
        // поднят вверх, поменявшись местами с товаром, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `sale_products`
                  WHERE
                      `category` = :category AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'category' => $category,
                'order_down' => $order_down
            )
        );
        // если запрос вернул false, значит товар и так самый последний
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами товары
            $query = "UPDATE
                          `sale_products`
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
                          `sale_products`
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
                      `sale_products`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $category = $res['category'];
        // получаем порядок следования и id товара, который находится выше и будет
        // опущен вниз, поменявшись местами с товаром, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `sale_products`
                  WHERE
                      `category` = :category AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'category' => $category,
                'order_up' => $order_up
            )
        );
        // если запрос вернул false, значит товар и так самый первый
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами товары
            $query = "UPDATE
                          `sale_products`
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
                          `sale_products`
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
     * Функция удаляет товар с уникальным идентификатором $id
     */
    public function removeProduct($id) {

        // категория удаляемого товара
        $category = $this->getProductCategory($id);
        // удаляем запись в таблице `sale_products` БД
        $query = "DELETE FROM
                      `sale_products`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // удаляем файл изображения
        if (is_file('files/sale/' . $id . '.jpg')) {
            unlink('files/sale/' . $id . '.jpg');
        }
        // обновляем порядок следования товаров в типовом решении
        $query = "SELECT
                      `id`
                  FROM
                      `sale_products`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`";
        $products = $this->database->fetchAll($query, array('category' => $category));
        $sortorder = 1;
        foreach ($products as $item) {
            $query = "UPDATE
                          `sale_products`
                      SET
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'sortorder' => $sortorder,
                    'id'        => $item['id']
                )
            );
            $sortorder++;
        }

    }

    /**
     * Функция возвращает идентификатор категории для товара
     * с уникальным идентификатором $id
     */
    private function getProductCategory($id) {
        $query = "SELECT
                      `category`
                  FROM
                      `sale_products`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает массив категорий товаров со скидкой
     */
    public function getCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `sale_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает информацию о категории с уникальным идентификатором $id
     */
    public function getCategoryName($id) {
        $query = "SELECT
                      `name`
                  FROM
                      `sale_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция добавляет новую категорию (новую запись в таблицу `sale_categories` базы данных)
     */
    public function addCategory($data) {

        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `sale_categories`
                  WHERE
                      1";
        $data['sortorder'] = $this->database->fetchOne($query) + 1;
        // добавляем категорию
        $query = "INSERT INTO `sale_categories`
                  (
                      `name`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :sortorder
                  )";
        $this->database->execute($query, $data);

    }

    /**
     * Функция обновляет категорию (запись в таблице `sale_categories` базы данных)
     */
    public function updateCategory($data) {
        $query = "UPDATE
                      `sale_categories`
                  SET
                      `name` = :name
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция удаляет категорию (запись в таблице `sale_categories` базы данных)
     */
    public function removeCategory($id) {

        // проверяем, что не существует товаров в этой категории
        $query = "SELECT
                      1
                  FROM
                      `sale_products`
                  WHERE
                      `category` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // удаляем запись в таблице `sale_categories` БД
        $query = "DELETE FROM
                      `sale_categories`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования категорий
        $query = "SELECT
                      `id`
                  FROM
                      `sale_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        $sortorder = 1;
        foreach ($categories as $item) {
            $query = "UPDATE
                          `sale_categories`
                      SET
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'sortorder' => $sortorder,
                    'id'        => $item['id']
                )
            );
            $sortorder++;
        }
        return true;

    }

    /**
     * Функция опускает категорию вниз в списке
     */
    public function moveCategoryDown($id) {
        $id_item_down = $id;
        // порядок следования категории, которая опускается вниз
        $query = "SELECT
                      `sortorder`
                  FROM
                      `sale_categories`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id категории, которая находится ниже
        // и будет поднята вверх, поменявшись местами с категорией,
        // которая опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `sale_categories`
                  WHERE
                      `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_down' => $order_down));
        // если запрос вернул false, значит категория и так самая последняя
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `sale_categories`
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
                          `sale_categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up' => $order_up,
                    'id_item_down' => $id_item_down
                )
            );
        }
    }

    /**
     * Функция поднимает категорию вверх в списке
     */
    public function moveCategoryUp($id) {
        $id_item_up = $id;
        // порядок следования категории, которая поднимается вверх
        $query = "SELECT
                      `sortorder`
                  FROM
                      `sale_categories`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id категории, которая находится выше
        // и будет опущена вниз, поменявшись местами с категорией,
        // которая поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `sale_categories`
                  WHERE
                      `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_up' => $order_up));
        // если запрос вернул false, значит категория и так самая первая
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `sale_categories`
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
                          `sale_categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute(
                $query,
                array(
                    'order_up' => $order_up,
                    'id_item_down' => $id_item_down
                )
            );
        }
    }

}