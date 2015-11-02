<?php
/**
 * Класс Solutions_Backend_Model для работы с типовыми решениями,
 * взаимодействует с базой данных, административная часть сайта
 */
class Solutions_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Функция возвращает массив всех категорий и всех типовых решений
     */
    public function getAllSolutions() {
        $query = "SELECT
                      `a`.`id` AS `ctg_id`, `a`.`name` AS `ctg_name`,
                      `b`.`id` AS `item_id`, `b`.`name` AS `item_name`
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
                $solutions[$counter] = array('id' => $value['ctg_id'], 'name' => $value['ctg_name']);
            }
            $solutions[$counter]['childs'][] = array('id' => $value['item_id'], 'name' => $value['item_name']);
        }

        // добавляем в массив URL ссылок для редактирования
        foreach ($solutions as $key => $value) {
            $solutions[$key]['edit'] = $this->getURL('backend/solutions/editctg/id/' . $value['id']);
            if (isset($value['childs'])) {
                foreach ($value['childs'] as $k => $v) {
                    $solutions[$key]['childs'][$k]['url'] = array(
                        'show' => $this->getURL('backend/solutions/show/id/' . $v['id']),
                        'edit' => $this->getURL('backend/solutions/editsltn/id/' . $v['id'])
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
                      `id`, `name`
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
                'show'   => $this->getURL('backend/solutions/category/id/' . $value['id']),
                'edit'   => $this->getURL('backend/solutions/editctg/id/' . $value['id']),
                'remove' => $this->getURL('backend/solutions/rmvctg/id/' . $value['id'])
            );
        }
        return $categories;
    }

    /**
     * Функция возвращает массив всех типовых решений выбранной категории $id
     */
    public function getCategorySolutions($id) {
        $query = "SELECT
                      `id`, `name`
                  FROM
                      `solutions`
                  WHERE
                      `category` = :category
                  ORDER BY
                      `sortorder`";
        $solutions = $this->database->fetchAll($query, array('category' => $id));
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($solutions  as $key => $value) {
            $solutions [$key]['url'] = array(
                'show'   => $this->getURL('backend/solutions/show/id/' . $value['id']),
                'edit'   => $this->getURL('backend/solutions/editsltn/id/' . $value['id']),
                'remove' => $this->getURL('backend/solutions/rmvsltn/id/' . $value['id'])
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
        // TODO: проверить, что категория пустая
        $query = "DELETE FROM
                      `solutions_categories`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
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
        foreach ($codes as $code) {
            $this->addProductByCode($id, $code);
        }

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

        // создаем директорию для хранения файлов типового решения
        if ( ! is_dir('files/solutions/' . $id)) {
            mkdir('files/solutions/' . $id);
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
                        'files/solutions/' . $id . '/' . $id . '.jpg',
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

        // создаем директорию для хранения файлов типового решения
        if ( ! is_dir('files/solutions/' . $id)) {
            mkdir('files/solutions/' . $id);
        }

        // проверяем, пришел ли файл PDF
        if ( ! empty($_FILES['pdf']['name'])) {
            // проверяем, что при загрузке не произошло ошибок
            if (0 == $_FILES['pdf']['error']) {
                // если файл загружен успешно, то проверяем - PDF?
                if ('application/pdf' == $_FILES['pdf']['type']) {
                    move_uploaded_file($_FILES['pdf']['tmp_name'], 'files/solutions/' . $id . '/' . $id . '.pdf');
                }
            }
        }

    }

    /**
     * Функция добавляет товра в типовое решение $id по коду $code
     */
    private function addProductByCode($id, $code) {

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
    }

    /**
     * Функция возвращает массив товаров типового решения $id
     */
    public function getSolutionProducts($id) {
        $query = "SELECT
                      `id`, `code`, `name`, `title`, `count`,
                      `price`, `heading`, `note`, `sortorder`
                  FROM
                      `solutions_products`
                  WHERE
                      `parent` = :parent
                  ORDER BY
                      `sortorder`";
        return $this->database->fetch($query, array('parent' => $id));
    }

    /**
     * Функция удаляет типовое решение
     */
    public function removeSolution($id) {
        $query = "DELETE FROM
                      `solutions`
                  WHERE
                      `id` = :id";
        $this->database->execute($query, array('id' => $id));
    }

}
