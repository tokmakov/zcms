<?php
/**
 * Класс Solution_Backend_Model для работы с типовыми решениями,
 * взаимодействует с базой данных, административная часть сайта
 */
class Solution_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив всех категорий и всех типовых решений
     */
    public function getAllSolutions() {
        $query = "SELECT
                      `a`.`id` AS `ctg_id`, `a`.`name` AS `ctg_name`,
                      `b`.`id` AS `item_id`, `b`.`name` AS `item_name`,
                      `a`.`sortorder` AS `ctg_sortorder`,
                      `b`.`sortorder` AS `item_sortorder`
                  FROM
                      `solutions_categories` `a`
                      INNER JOIN `solutions` `b` ON `a`.`id` = `b`.`category`
                  WHERE
                      1
                  ORDER BY
                      `a`.`sortorder`, `b`.`sortorder`";
        $result = $this->database->fetchAll($query);

        $solutions = array();
        $ctg_id = 0;
        $counter = -1;
        foreach ($result as $value) {
            if ($ctg_id != $value['ctg_id']) {
                $counter++;
                $ctg_id = $value['ctg_id'];
                $solutions[$counter] = array(
                    'id' => $value['ctg_id'],
                    'name' => $value['ctg_name'],
                    'sortorder' => $value['ctg_sortorder']
                );
            }
            $solutions[$counter]['childs'][] = array(
                'id' => $value['item_id'],
                'name' => $value['item_name'],
                'sortorder' => $value['item_sortorder']
            );
        }

        // добавляем в массив URL ссылок для редактирования
        foreach ($solutions as $key => $value) {
            $solutions[$key]['edit'] = $this->getURL('backend/solution/editctg/id/' . $value['id']);
            if (isset($value['childs'])) {
                foreach ($value['childs'] as $k => $v) {
                    $solutions[$key]['childs'][$k]['url'] = array(
                        'show' => $this->getURL('backend/solution/show/id/' . $v['id']),
                        'edit' => $this->getURL('backend/solution/editsltn/id/' . $v['id'])
                    );
                }
            }
        }

        return $solutions;
    }

    /**
     * Функция возвращает массив всех категорий типовых решений
     */
    public function getCategories() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `solutions_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция возвращает массив всех категорий типовых решений
     */
    public function getAllCategories() {
        $query = "SELECT
                      `id`, `name`, `sortorder`
                  FROM
                      `solutions_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($categories as $key => $value) {
            $categories[$key]['url'] = array(
                'show'   => $this->getURL('backend/solution/category/id/' . $value['id']),
                'up'     => $this->getURL('backend/solution/ctgup/id/' . $value['id']),
                'down'   => $this->getURL('backend/solution/ctgdown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/solution/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/solution/rmvctg/id/' . $value['id'])
            );
        }
        return $categories;
    }

    /**
     * Функция возвращает массив всех типовых решений выбранной категории $id
     */
    public function getCategorySolutions($id) {
        $query = "SELECT
                      `id`, `name`, `sortorder`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`";
        $solutions = $this->database->fetchAll($query, array('category' => $id));
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($solutions  as $key => $value) {
            $solutions[$key]['url'] = array(
                'show'   => $this->getURL('backend/solution/show/id/' . $value['id']),
                'up'     => $this->getURL('backend/solution/sltnup/id/' . $value['id']),
                'down'   => $this->getURL('backend/solution/sltndown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/solution/editsltn/id/' . $value['id']),
                'remove' => $this->getURL('backend/solution/rmvsltn/id/' . $value['id'])
            );
        }
        return $solutions ;
    }

    /**
     * Возвращает информацию о категории с уникальным идентификатором $id
     */
    public function getCategory($id) {
        $query = "SELECT
                      `name`, `keywords`, `description`, `excerpt`
                  FROM
                      `solutions_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Возвращает наименование категории с уникальным идентификатором $id
     */
    public function getCategoryName($id) {
        $query = "SELECT
                      `name`
                  FROM
                      `solutions_categories`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция добавляет новую категорию
     */
    public function addCategory($data) {
        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `solutions_categories`
                  WHERE
                      1";
        $data['sortorder'] = $this->database->fetchOne($query) + 1;
        // добавляем категорию
        $query = "INSERT INTO `solutions_categories`
                  (
                      `name`,
                      `keywords`,
                      `description`,
                      `excerpt`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :keywords,
                      :description,
                      :excerpt,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
    }

    /**
     * Функция обновляет категорию
     */
    public function updateCategory($data) {
        $query = "UPDATE
                      `solutions_categories`
                  SET
                      `name`        = :name,
                      `keywords`    = :keywords,
                      `description` = :description,
                      `excerpt`     = :excerpt
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);
    }

    /**
     * Функция удаляет категорию
     */
    public function removeCategory($id) {
        // проверяем, что не существует типовых решений в этой категории
        $query = "SELECT
                      1
                  FROM
                      `solutions`
                  WHERE
                      `category` = :id
                  LIMIT
                      1";
        $res = $this->database->fetchOne($query, array('id' => $id));
        if ($res) {
            return false;
        }
        // удаляем категорию
        $query = "DELETE FROM
                      `solutions_categories`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования категорий
        $query = "SELECT
                      `id`
                  FROM
                      `solutions_categories`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $categories = $this->database->fetchAll($query);
        $sortorder = 1;
        foreach ($categories as $item) {
            $query = "UPDATE
                          `solutions_categories`
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
     * Функция опускает категорию вниз в списке
     */
    public function moveCategoryDown($id) {
        $id_item_down = $id;
        // порядок следования категории, которое опускается вниз
        $query = "SELECT
                      `sortorder`
                  FROM
                      `solutions_categories`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id категории, которая находится ниже
        // и будет поднята вверх, поменявшись местами с категорией,
        // которая опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `solutions_categories`
                  WHERE
                      `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_down' => $order_down));
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `solutions_categories`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute($query, array('order_down' => $order_down, 'id_item_up' => $id_item_up));
            $query = "UPDATE
                          `solutions_categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute($query, array('order_up' => $order_up, 'id_item_down' => $id_item_down));
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
                      `solutions_categories`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id категории, которая находится выше
        // и будет опущена вниз, поменявшись местами с категорией,
        // которая поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `solutions_categories`
                  WHERE
                      `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('order_up' => $order_up));
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами категории
            $query = "UPDATE
                          `solutions_categories`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute($query, array('order_down' => $order_down, 'id_item_up' => $id_item_up));
            $query = "UPDATE
                          `solutions_categories`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute($query, array('order_up' => $order_up, 'id_item_down' => $id_item_down));
        }
    }

    /**
     * Возвращает информацию о типовом решении с уникальным идентификатором $id
     */
    public function getSolution($id) {
        $query = "SELECT
                      `category`, `name`, `keywords`, `description`,
                      `excerpt`, `content1`, `content2`
                  FROM
                      `solutions`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Возвращает наименование типового решения с уникальным идентификатором $id
     */
    public function getSolutionName($id) {
        $query = "SELECT
                      `name`
                  FROM
                      `solutions`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция добавляет новое типовое решение
     */
    public function addSolution($data) {

        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category";
        $data['sortorder'] = $this->database->fetchOne($query, array('category' => $data['category'])) + 1;
        $codes = $data['codes'];
        unset($data['codes']);
        // добавляем типовое решение
        $query = "INSERT INTO `solutions`
                  (
                      `category`,
                      `name`,
                      `keywords`,
                      `description`,
                      `excerpt`,
                      `content1`,
                      `content2`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :category,
                      :name,
                      :keywords,
                      :description,
                      :excerpt,
                      :content1,
                      :content2,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // добавляем товары в типовое решение
        $codes = explode(' ', $codes);
        $this->addProductsByCodes($id, $codes);

        // загружаем файл изображения
        $this->uploadImage($id);

        // загружаем файл PDF
        $this->uploadPDF($id);

    }

    /**
     * Функция загружает файл изображения для типового решения с уникальным
     * идентификатором $id
     */
    private function uploadImage($id) {

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
                        'files/solution/' . $id . '.jpg',
                        1200,
                        0,
                        'jpg'
                    );
                }
            }
        }
    }

    /**
     * Функция загружает файл PDF для типового решения с уникальным
     * идентификатором $id
     */
    private function uploadPDF($id) {

        // проверяем, пришел ли файл PDF
        if ( ! empty($_FILES['pdf']['name'])) {
            // проверяем, что при загрузке не произошло ошибок
            if (0 == $_FILES['pdf']['error']) {
                // если файл загружен успешно, то проверяем - PDF?
                if ('application/pdf' == $_FILES['pdf']['type']) {
                    move_uploaded_file($_FILES['pdf']['tmp_name'], 'files/solution/' . $id . '.pdf');
                }
            }
        }

    }

    /**
     * Функция добавляет товары в типовое решение $id по кодам $codes
     */
    private function addProductsByCodes($id, $codes) {
        $sortorder = 1;
        foreach ($codes as $code) {
            $query = "INSERT INTO `solutions_products`
                      (
                          `parent`,
                          `group`,
                          `require`,
                          `product_id`,
                          `code`,
                          `name`,
                          `title`,
                          `shortdescr`,
                          `count`,
                          `price`,
                          `unit`,
                          `changeable`,
                          `sortorder`
                      )
                      SELECT
                          :parent,
                          1,
                          1,
                          `id`,
                          `code`,
                          `name`,
                          `title`,
                          `shortdescr`,
                          1,
                          `price`,
                          `unit`,
                          0,
                          :sortorder
                      FROM
                          `products`
                      WHERE
                          `code` = :code";
            $this->database->execute(
                $query,
                array(
                    'parent'    => $id,
                    'code'      => $code,
                    'sortorder' => $sortorder
                )
            );
            $sortorder++;
        }
        $query = "SELECT COUNT(*) FROM `solutions_products` WHERE `parent` = :parent";
        $count = $this->database->fetchOne($query, array('parent' => $id));
        if ($count != count($codes)) {
            $query = "SELECT
                          `id`
                      FROM
                          `solutions_products`
                      WHERE
                          `parent` = :parent
                      ORDER BY
                          `sortorder`";
            $items = $this->database->fetchAll($query, array('parent' => $id));
            $sortorder = 1;
            foreach ($items as $item) {
                $query = "UPDATE
                              `solutions_products`
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
    }

    /**
     * Функция обновляет типовое решение
     */
    public function updateSolution($data) {

        $query = "UPDATE
                      `solutions`
                  SET
                      `category`    = :category,
                      `name`        = :name,
                      `keywords`    = :keywords,
                      `description` = :description,
                      `excerpt`     = :excerpt,
                      `content1`    = :content1,
                      `content2`    = :content2
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

        // загружаем файл изображения
        $this->uploadImage($data['id']);

        // загружаем файл PDF
        $this->uploadPDF($data['id']);

    }

    /**
     * Функция возвращает массив товаров типового решения $id
     */
    public function getSolutionProducts($id) {

        $query = "SELECT
                      `a`.`id` AS `id`,
                      `c`.`id` AS `group_id`,
                      `c`.`name` AS `group_name`,
                      `a`.`require` AS `require`,
                      `a`.`code` AS `code`,
                      `a`.`name` AS `name`,
                      `a`.`title` AS `title`,
                      CASE WHEN `b`.`price` IS NULL THEN `a`.`price` ELSE `b`.`price` END AS `price`,
                      `a`.`unit` AS `unit`,
                      `a`.`count` AS `count`,
                      `a`.`changeable` AS `changeable`,
                      `a`.`sortorder` AS `sortorder`,
                      CASE WHEN `b`.`id` IS NULL THEN 1 ELSE 0 END AS `empty`
                FROM
                    `solutions_products` `a`
                    LEFT JOIN `products` `b` ON `a`.`product_id` = `b`.`id` AND `b`.`visible` = 1
                    INNER JOIN `solutions_groups` `c` ON `a`.`group` = `c`.`id`
                WHERE
                    `a`.`parent` = :parent
                ORDER BY
                    `c`.`sortorder`, `a`.`sortorder`";
        $result = $this->database->fetchAll($query, array('parent' => $id));
        
        if (empty($result)) {
            return array();
        }

        $products = array();
        $group_id = 0;
        $counter = -1;
        $amount = 0;
        foreach ($result as $value) {
            if ($group_id != $value['group_id']) {
                $counter++;
                $group_id = $value['group_id'];
                $products[$counter] = array(
                    'name'   => $value['group_name'],
                    'amount' => $amount,
                );
                if ($counter) {
                    $products[$counter-1]['amount'] = $amount;
                }
                $amount = 0;
            }
            $cost = $value['price'] * $value['count'];
            $amount = $amount + $cost;
            $products[$counter]['products'][] = array(
                'id'         => $value['id'],
                'require'    => $value['require'],
                'code'       => $value['code'],
                'name'       => $value['name'],
                'title'      => $value['title'],
                'price'      => $value['price'],
                'unit'       => $value['unit'],
                'count'      => $value['count'],
                'cost'       => $cost,
                'changeable' => $value['changeable'],
                'sortorder'  => $value['sortorder'],
                'empty'      => $value['empty'],
                'url'        => array(
                    'up'     => $this->getURL('backend/solution/prdup/id/' . $value['id']),
                    'down'   => $this->getURL('backend/solution/prddown/id/' . $value['id']),
                    'edit'   => $this->getURL('backend/solution/editprd/id/' . $value['id']),
                    'remove' => $this->getURL('backend/solution/rmvprd/id/' . $value['id'])
                )
            );
        }
        $products[$counter]['amount'] = $amount;

        return $products;

    }

    /**
     * Функция удаляет типовое решение
     */
    public function removeSolution($id) {
        // родительская категория типового решения
        $category = $this->getSolutionCategory($id);
        // удаляем товары типового решения
        $query = "DELETE FROM
                      `solutions_products`
                  WHERE
                      `parent` = :id";
        $this->database->execute($query, array('id' => $id));
        // удаляем типовое решение
        $query = "DELETE FROM
                      `solutions`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования типовых решений
        $query = "SELECT
                      `id`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`";
        $solutions = $this->database->fetchAll($query, array('category' => $category));
        $sortorder = 1;
        foreach ($solutions as $item) {
            $query = "UPDATE
                          `solutions`
                      SET
                          `sortorder`=:sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute($query, array('sortorder' => $sortorder,'id' => $item['id']));
            $sortorder++;
        }
        // удаляем файлы
        if (is_file('files/solution/' . $id . '.pdf')) {
            unlink('files/solution/' . $id . '.pdf');
        }
        if (is_file('files/solution/' . $id . '.jpg')) {
            unlink('files/solution/' . $id . '.jpg');
        }
    }

    /**
     * Функция опускает типовое решение вниз в списке
     */
    public function moveSolutionDown($id) {
        $id_item_down = $id;
        // порядок следования типового решения, которое опускается вниз
        $query = "SELECT
                      `sortorder`, `category`
                  FROM
                      `solutions`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $category = $res['category'];
        // порядок следования и id типового решения, которое находится ниже
        // и будет поднято вверх, поменявшись местами с типовым решением,
        // которое опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('category' => $category, 'order_down' => $order_down));
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами типовые решения
            $query = "UPDATE
                          `solutions`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute($query, array('order_down' => $order_down, 'id_item_up' => $id_item_up));
            $query = "UPDATE
                          `solutions`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute($query, array('order_up' => $order_up, 'id_item_down' => $id_item_down));
        }
    }

    /**
     * Функция поднимает типовое решение вверх в списке
     */
    public function moveSolutionUp($id) {
        $id_item_up = $id;
        // порядок следования типового решения, которое поднимается вверх
        $query = "SELECT
                      `sortorder`, `category`
                  FROM
                      `solutions`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $category = $res['category'];
        // порядок следования и id типового решения, которое находится выше
        // и будет опущено вниз, поменявшись местами с типовым решением,
        // которое поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch($query, array('category' => $category, 'order_up' => $order_up));
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами типовые решения
            $query = "UPDATE
                          `solutions`
                      SET
                          `sortorder` = :order_down
                      WHERE
                          `id` = :id_item_up";
            $this->database->execute($query, array('order_down' => $order_down, 'id_item_up' => $id_item_up));
            $query = "UPDATE
                          `solutions`
                      SET
                          `sortorder` = :order_up
                      WHERE
                          `id` = :id_item_down";
            $this->database->execute($query, array('order_up' => $order_up, 'id_item_down' => $id_item_down));
        }
    }

    /**
     * Функция возвращает идентификатор категории типового решения
     * с уникальным идентификатором $id
     */
    public function getSolutionCategory($id) {
        $query = "SELECT
                      `category`
                  FROM
                      `solutions`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция возвращает массив групп (типов) товаров:основное, объектовое, пультовое, дополнительное
     */
    public function getGroups() {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `solutions_groups`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        return $this->database->fetchAll($query);
    }

    /**
     * Функция добавляет товар в типовое решение
     */
    public function addSolutionProduct($data) {

        // порядок сортировки
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `solutions_products`
                  WHERE
                      `parent` = :parent AND `group` = :group";
        $sortorder = $this->database->fetchOne(
            $query,
            array(
                'parent' => $data['parent'],
                'group'  => $data['group']
            )
        );
        $data['sortorder'] = $sortorder + 1;
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
        $query = "INSERT INTO `solutions_products`
                  (
                      `parent`,
                      `group`,
                      `require`,
                      `product_id`,
                      `code`,
                      `name`,
                      `title`,
                      `shortdescr`,
                      `count`,
                      `price`,
                      `unit`,
                      `changeable`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :parent,
                      :group,
                      :require,
                      :product_id,
                      :code,
                      :name,
                      :title,
                      :shortdescr,
                      :count,
                      :price,
                      :unit,
                      :changeable,
                      :sortorder
                  )";
        $this->database->execute($query, $data);

    }

    /**
     * Функция возвращает информацию о товаре типового решения с
     * уникальным идентификатором $id
     */
    public function getSolutionProduct($id) {
        $query = "SELECT
                      `parent`, `group`, `require`, `code`, `name`, `title`,
                      `shortdescr`, `count`, `price`, `unit`, `changeable`
                  FROM
                      `solutions_products`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция обновляет товар типового решения
     */
    public function updateSolutionProduct($data) {

        // порядок сортировки
        $oldGroup = $this->getProductGroup($data['id']);
        if ($data['group'] != $oldGroup) { // если товар перемещается в другую группу
            $parent = $this->getProductParent($data['id']);
            // в новой группе он будет в конце списка
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `solutions_products`
                      WHERE
                          `parent` = :parent AND `group` = :group";
            $sortorder = $this->database->fetchOne(
                $query,
                array(
                    'parent' => $parent,
                    'group'  => $data['group']
                )
            );
            $sortorder = $sortorder + 1;
            // перемещаем товар в другую группу
            $query = "UPDATE
                          `solutions_products`
                      SET
                          `group` = :group,
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'group'     => $data['group'],
                    'sortorder' => $sortorder,
                    'id'        => $data['id']
                )
            );
            // обновляем порядок сортировки в старой группе
            $query = "SELECT
                          `id`
                      FROM
                          `solutions_products`
                      WHERE
                          `parent` = :parent AND `group` = :old_group
                      ORDER BY
                          `sortorder`";
            $result = $this->database->fetchAll(
                $query,
                array(
                    'parent'    => $parent,
                    'old_group' => $oldGroup
                )
            );
            $sortorder = 1;
            foreach ($result as $item) {
                $query = "UPDATE
                              `solutions_products`
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
        unset($data['group']);

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
        $query = "UPDATE
                      `solutions_products`
                  SET
                      `require`    = :require,
                      `product_id` = :product_id,
                      `code`       = :code,
                      `name`       = :name,
                      `title`      = :title,
                      `shortdescr` = :shortdescr,
                      `count`      = :count,
                      `price`      = :price,
                      `unit`       = :unit,
                      `changeable` = :changeable
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

    }

    /**
     * Функция удаляет товар из типового решения
     */
    public function removeSolutionProduct($id) {
        // идентификатор типового решения
        $parent = $this->getProductParent($id);
        // идентификатор группы товаров
        $group = $this->getProductGroup($id);
        // удаляем товар
        $query = "DELETE FROM
                      `solutions_products`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования товаров в типовом решении
        $query = "SELECT
                      `id`
                  FROM
                      `solutions_products`
                  WHERE
                      `parent` = :parent AND `group` = :group
                  ORDER BY
                      `sortorder`";
        $products = $this->database->fetchAll(
            $query,
            array(
                'parent' => $parent,
                'group'  => $group
            )
        );
        $sortorder = 1;
        foreach ($products as $item) {
            $query = "UPDATE
                          `solutions_products`
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
     * Функция опускает товар вниз в списке
     */
    public function moveProductDown($id) {
        $id_item_down = $id;
        // порядок следования товара, который опускается вниз
        $query = "SELECT
                      `sortorder`, `parent`, `group`
                  FROM
                      `solutions_products`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $order_down = $res['sortorder'];
        $parent = $res['parent'];
        $group = $res['group'];
        // порядок следования и id товара, который находится ниже и будет поднят вверх,
        // поменявшись местами с товаром, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `solutions_products`
                  WHERE
                      `parent` = :parent AND `group` = :group AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'parent'     => $parent,
                'group'      => $group,
                'order_down' => $order_down
            )
        );
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами товары
            $query = "UPDATE
                          `solutions_products`
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
                          `solutions_products`
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
        // порядок следования товара, который поднимается вверх
        $query = "SELECT
                      `sortorder`, `parent`, `group`
                  FROM
                      `solutions_products`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $order_up = $res['sortorder'];
        $parent = $res['parent'];
        $group = $res['group'];
        // порядок следования и id товара, который находится выше и будет опущен вниз,
        // поменявшись местами с товаром, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `solutions_products`
                  WHERE
                      `parent` = :parent AND `group` = :group AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'parent'   => $parent,
                'group'    => $group,
                'order_up' => $order_up
            )
        );
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами товары
            $query = "UPDATE
                          `solutions_products`
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
                          `solutions_products`
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
     * Функция возвращает идентификатор типового решения для товара
     * с уникальным идентификатором $id
     */
    public function getProductParent($id) {
        $query = "SELECT
                      `parent`
                  FROM
                      `solutions_products`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция возвращает идентификатор группы товаров (основное, объектовое, пультовое,
     * дополнительное) для товара с уникальным идентификатором $id
     */
    public function getProductGroup($id) {
        $query = "SELECT
                      `group`
                  FROM
                      `solutions_products`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция возвращает информацию о товаре с кодом $code, получает информацию
     * из таблицы БД products
     */
    public function getProductByCode($code) {
        $query = "SELECT
                      `name`, `title`, `shortdescr`, `price`, `unit`
                  FROM
                      `products`
                  WHERE
                      `code` = :code";
        return $this->database->fetch($query, array('code' => $code));
    }

}
