<?php
/**
 * Класс Rating_Backend_Model для работы с рейтингом продаж, взаимодействует
 * с базой данных, административная часть сайта
 */
class Rating_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Возвращает массив всех категорий рейтинга
     */
    public function getAllCategories() {
        $query = "SELECT
                      `id`, `parent`, `name`, `sortorder`
                  FROM
                      `rating_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $data = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для перехода, ссылок для смещения вверх/вниз,
        // редактирования и удаления категорий
        foreach($data as $key => $value) {
            $link = null;
            if (empty($value['parent'])) {
                $link = $this->getURL('backend/rating/root/id/' . $value['id']);
            }
            $data[$key]['url'] = array(
                'link'   => $link,
                'up'     => $this->getURL('backend/rating/ctgup/id/' . $value['id']),
                'down'   => $this->getURL('backend/rating/ctgdown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/rating/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/rating/rmvctg/id/' . $value['id'])
            );
        }
        // строим дерево
        $tree = $this->makeTree($data);
        return $tree;
    }

    /**
     * Возвращает массив дочерних категорий и товаров корневой категории $id
     */
    public function getRootCategory($id) {
        // получаем все товары и категории
        $query = "SELECT
                      `b`.`id` AS `id`, `b`.`code` AS `code`,
                      `b`.`name` AS `name`, `b`.`title` AS `title`,
                      `a`.`id` AS `ctg_id`, `a`.`name` AS `ctg_name`,
                      `a`.`sortorder` AS `ctg_sort`, `b`.`sortorder` AS `prd_sort`
                  FROM
                      `rating_categories` `a` LEFT JOIN `rating_products` `b`
                      ON `a`.`id` = `b`.`category`
                  WHERE
                      `a`.`parent` = :parent
                  ORDER BY
                      `a`.`sortorder`, `b`.`sortorder`";
        $result = $this->database->fetchAll($query, array('parent' => $id));

        // добавляем в массив товаров URL ссылок для редактирования
        // и удаления товаров и изменяем структуру массива
        $root = array();
        $ctg_id = 0;
        $counter = -1;
        foreach($result as $value) {
            if ($ctg_id != $value['ctg_id']) {
                $counter++;
                $ctg_id = $value['ctg_id'];
                $root[$counter] = array(
                    'number'   => $value['ctg_sort'],
                    'category' => $value['ctg_name'],
                );
            }
            if ( ! empty($value['id'])) {
                $root[$counter]['products'][] = array(
                    'number'  => $value['prd_sort'],
                    'name'    => $value['name'],
                    'title'   => $value['title'],
                    'up'      => $this->getURL('backend/rating/prdup/id/' . $value['id']),
                    'down'    => $this->getURL('backend/rating/prddown/id/' . $value['id']),
                    'edit'    => $this->getURL('backend/rating/editprd/id/' . $value['id']),
                    'remove'  => $this->getURL('backend/rating/rmvprd/id/' . $value['id'])
                );
            } else {
                $root[$counter]['products'] = array();
            }
        }

        return $root;
    }
    
    /**
     * Возвращает массив категорий товаров рейтинга для контроллеров, отвечающих
     * за добавление и редактирование товаров
     */
    public function getCategories() {
        $query = "SELECT
                      `id`, `parent`, `name`, `sortorder`
                  FROM
                      `rating_categories`
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
     * Возвращает массив категорий верхнего уровня для контроллеров, отвечающих
     * за добавление и редактирование категорий
     */
    public function getRootCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `rating_categories`
                  WHERE
                      `parent` = 0
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает информацию о категории с уникальным идентификатором $id
     */
    public function getCategory($id) {
        $query = "SELECT
                      `name`, `parent`
                  FROM
                      `rating_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет новую категорию (новую запись в таблицу `rating_categories`
     * базы данных)
     */
    public function addCategory($data) {

        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `rating_categories`
                  WHERE
                      `parent` = :parent";
        $data['sortorder'] = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;
        // добавляем категорию
        $query = "INSERT INTO `rating_categories`
                  (
                      `name`,
                      `parent`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :parent,
                      :sortorder
                  )";
        $this->database->execute($query, $data);

    }

    /**
     * Функция обновляет категорию (запись в таблице `rating_categories` базы данных)
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
                          `rating_categories`
                      WHERE
                          `parent` = :parent";
            $sortorder = $this->database->fetchOne($query, array('parent' => $data['parent'])) + 1;
            $query = "UPDATE
                          `rating_categories`
                      SET
                          `sortorder` = :sortorder, `parent` = :parent
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
            // обновляем порядок категорий внутри той категории, откуда была перемещена категория
            $query = "SELECT
                          `id`
                      FROM
                          `rating_categories`
                      WHERE
                          `parent` = :oldparent
                      ORDER BY
                          `sortorder`";
            $result = $this->database->fetchAll($query, array('oldparent' => $oldParent));
            $sortorder = 1;
            foreach ($result as $ctg) {
                $query = "UPDATE
                              `rating_categories`
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
        
        // обновляем категорию
        unset($data['parent']);
        $query = "UPDATE
                      `rating_categories`
                  SET
                      `name` = :name
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
                      `rating_categories`
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
                      `rating_categories`
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
                          `rating_categories`
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
                          `rating_categories`
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
     * Функция поднимает категорию вверх в списке
     */
    public function moveCategoryUp($id) {
        $id_item_up = $id;
        // получаем порядок следования и родителя категории, которая поднимается вверх
        $query = "SELECT
                      `sortorder`, `parent`
                  FROM
                      `rating_categories`
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
                      `rating_categories`
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
                          `rating_categories`
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
                          `rating_categories`
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
     * Функция удаляет категорию (запись в таблице `rating_categories` базы данных)
     */
    public function removeCategory($id) {

        // проверяем, что не существует товаров в этой категории
        $query = "SELECT
                      1
                  FROM
                      `rating_products`
                  WHERE
                      `category` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // проверяем, что не существует дочерних категорий
        $query = "SELECT
                      1
                  FROM
                      `rating_categories`
                  WHERE
                      `parent` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // родитель удаляемой категории
        $parent = $this->getCategoryParent($id);
        // удаляем запись в таблице `rating_categories` БД
        $query = "DELETE FROM
                      `rating_categories`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования категорий
        $query = "SELECT
                      `id`
                  FROM
                      `rating_categories`
                  WHERE
                      `parent` = :parent
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query, array('parent' => $parent));
        $sortorder = 1;
        foreach ($categories as $item) {
            $query = "UPDATE
                          `rating_categories`
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
     * Функция возвращает идентификатор родительского элемента категории
     * с уникальным идентификатором $id
     */
    public function getCategoryParent($id) {
        $query = "SELECT
                      `parent`
                  FROM
                      `rating_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Возвращает информацию о товаре с уникальным идентификатором $id
     */
    public function getProduct($id) {
        $query = "SELECT
                      `code`, `name`, `title`, `category`
                  FROM
                      `rating_products`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет товар (новую запись в таблицу `rating_products` базы данных)
     */
    public function addProduct($data) {

        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `rating_products`
                  WHERE
                      `category` = :category";
        $data['sortorder'] = $this->database->fetchOne($query, array('category' => $data['category'])) + 1;
        // идентификатор товара
        $data['product_id'] = 0;
        $query = "SELECT
                      `id`
                  FROM
                      `products`
                  WHERE
                      `code` = :code";
        $product_id = $this->database->fetchOne($query, array('code' => $data['code']));
        if ($product_id) {
            $data['product_id'] = $product_id;
        }

        // добавляем товар
        $query = "INSERT INTO `rating_products`
                  (
                      `category`,
                      `product_id`,
                      `code`,
                      `name`,
                      `title`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :category,
                      :product_id,
                      :code,
                      :name,
                      :title,
                      :sortorder
                  )";
        $this->database->execute($query, $data);

    }

    /**
     * Функция обновляет товар (запись в таблице `rating_products` базы данных)
     */
    public function updateProduct($data) {

        // порядок сортировки
        $oldCategory = $this->getProductCategory($data['id']);
        if ($data['category'] != $oldCategory) { // если товар перемещается в другую категорию
            // в новой категории он будет в конце списка
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `rating_products`
                      WHERE
                          `category` = :category";
            $sortorder = $this->database->fetchOne($query, array('category' => $data['category'])) + 1;
            // перемещаем товар в новую категорию
            $query = "UPDATE
                          `rating_products`
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
                          `rating_products`
                      WHERE
                          `category` = :old_category
                      ORDER BY
                          `sortorder`";
            $result = $this->database->fetchAll($query, array('old_category' => $oldCategory));
            $sortorder = 1;
            foreach ($result as $item) {
                $query = "UPDATE
                              `rating_products`
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

        // идентификатор товара в каталоге
        $data['product_id'] = 0;
        $query = "SELECT
                      `id`
                  FROM
                      `products`
                  WHERE
                      `code` = :code";
        $product_id = $this->database->fetchOne($query, array('code' => $data['code']));
        if ($product_id) {
            $data['product_id'] = $product_id;
        }
        // обновляем товар
        $query = "UPDATE
                      `rating_products`
                  SET
                      `product_id` = :product_id,
                      `code`       = :code,
                      `name`       = :name,
                      `title`      = :title
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

    }

    /**
     * Функция опускает товар вниз в списке
     */
    public function moveProductDown($id) {
        $id_item_down = $id;
        // получаем порядок следования и родительскую категорию товара,
        // который опускается вниз
        $query = "SELECT
                      `sortorder`, `category`
                  FROM
                      `rating_products`
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
                      `rating_products`
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
                          `rating_products`
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
                          `rating_products`
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
        // получаем порядок следования и родительскую категорию товара,
        // который поднимается вверх
        $query = "SELECT
                      `sortorder`, `category`
                  FROM
                      `rating_products`
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
                      `rating_products`
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
                          `rating_products`
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
                          `rating_products`
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
        // удаляем запись в таблице `rating_products` БД
        $query = "DELETE FROM
                      `rating_products`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования товаров в рейтинге
        $query = "SELECT
                      `id`
                  FROM
                      `rating_products`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`";
        $products = $this->database->fetchAll($query, array('category' => $category));
        $sortorder = 1;
        foreach ($products as $item) {
            $query = "UPDATE
                          `rating_products`
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
                      `rating_products`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }
    
    /**
     * Функция возвращает идентификатор категории верхнего уровня для товара
     * с уникальным идентификатором $id, т.е. родителя родителя товара
     */
    public function getProductRootCategory($id) {
        $query = "SELECT
                      `parent`
                  FROM
                      `rating_categories`
                  WHERE
                      `id` = (SELECT `category` FROM `rating_products` WHERE `id` = :id)";
        return $this->database->fetchOne($query, array('id' => $id));
    }
    
    /**
     * Функция возвращает информацию о товаре с кодом $code, получает информацию
     * из таблицы БД products
     */
    public function getProductByCode($code) {
        $query = "SELECT
                      `name`, `title`
                  FROM
                      `products`
                  WHERE
                      `code` = :code";
        return $this->database->fetch($query, array('code' => $code));
    }

}