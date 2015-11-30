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

        // получаем все товары
        $query = "SELECT
                      `a`.`id` AS `id`, `a`.`code` AS `code`,
                      `a`.`name` AS `name`, `a`.`count` AS `count`,
                      `a`.`price1` AS `price1`, `a`.`price2` AS `price2`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `sale_products` `a` INNER JOIN `sale_categories` `b`
                      ON `a`.`category` = `b`.`id`
                  WHERE
                      1
                  ORDER BY
                      `b`.`sortorder`, `a`.`sortorder`";
        $products = $this->database->fetchAll($query);

        // добавляем в массив товаров URL ссылок для редактирования и удаления
        foreach($products as $key => $value) {
            $products[$key]['url'] = array(
                'edit'   => $this->getURL('backend/sale/editprd/id/' . $value['id']),
                'remove' => $this->getURL('backend/sale/rmvprd/id/' . $value['id'])
            );
        }
        return $products;

    }


    /**
     * Возвращает информацию о товаре с уникальным идентификатором $id
     */
    public function getProduct($id) {
        $query = "SELECT
                      `a`.`code` AS `code`, `a`.`name` AS `name`,
                      `a`.`count` AS `count`, `a`.`description` AS `description`,
                      `a`.`price1` AS `price1`, `a`.`price2` AS `price2`,
                      `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
                  FROM
                      `sale_products` `a` INNER JOIN `sale_categories` `b`
                      ON `a`.`category` = `b`.`id`
                  WHERE
                      `a`.`id` = :id";
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
                      `count`,
                      `description`,
                      `price1`,
                      `price2`
                  )
                  VALUES
                  (
                      :category,
                      :code,
                      :name,
                      :count,
                      :description,
                      :price1,
                      :price2
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // загружаем файл изображения
        $this->uploadImage($id);

    }

    /**
     * Функция обновляет товар (запись в таблице `sales_products` базы данных)
     */
    public function updateProduct($data) {

        // обновляем товар
        $query = "UPDATE
                      `sales_products`
                  SET
                      `category`    = :category,
                      `code`        = :code,
                      `name`        = :name,
                      `count`       = :count,
                      `description` = :description,
                      `price1`      = :price1,
                      `price2`      = :price2
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
                        50,
                        50,
                        'jpg'
                    );
                }
            }
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
                    'id' => $item['id']
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
     * Возвращает массив категорий новостей для контроллера, отвечающего
     * за вывод всех категорий
     */
    public function getAllCategories() {

        // получаем все категории
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `sale_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);

        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = array(
                'edit'   => $this->getURL('backend/sale/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/sale/rmvctg/id/' . $value['id'])
            );
        }
        return $categories;

    }

    /**
     * Возвращает массив категорий новостей для контроллеров, отвечающих
     * за добавление и редактирование товаров по сниженным ценам
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
    public function getCategory($id) {
        $query = "SELECT
                      `name`
                  FROM
                      `sale_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
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
}