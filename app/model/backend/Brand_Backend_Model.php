<?php
/**
 * Класс Brand_Backend_Model для работы с брендами, взаимодействует
 * с базой данных, административная часть сайта
 */
class Brand_Backend_Model extends Backend_Model {

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
                      `id`, `name`, `popular` AS `sortorder`
                  FROM
                      `brands`
                  WHERE
                      `popular` <> 0
                  ORDER BY
                      `popular`";
        $brands = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($brands as $key => $value) {
            $brands[$key]['url'] = array(
                'up'     => $this->getURL('backend/brand/popularup/id/' . $value['id']),
                'down'   => $this->getURL('backend/brand/populardown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/brand/edit/id/' . $value['id']),
                'remove' => $this->getURL('backend/brand/remove/id/' . $value['id'])
            );
        }
        return $brands;
    }
    
    /**
     * Возвращает массив брендов A-Z
     */
    private function getLatinBrands() {

        $query = "SELECT
                      `id`, `name`, `letter`, `sortorder`
                  FROM
                      `brands`
                  WHERE
                      `letter` REGEXP '[A-Z]'
                  ORDER BY
                      `letter`, `sortorder`";
        $items = $this->database->fetchAll($query);
        
        $brands = array();
        $letter = '';
        foreach ($items as $item) {
            if ($letter != $item['letter']) {
                $letter = $item['letter'];
            }
            $brands[$letter][] = array(
                'id'        => $item['id'],
                'name'      => $item['name'],
                'sortorder' => $item['sortorder'],
                'url'       => array(
                    'up'     => $this->getURL('backend/brand/moveup/id/' . $item['id']),
                    'down'   => $this->getURL('backend/brand/movedown/id/' . $item['id']),
                    'edit'   => $this->getURL('backend/brand/edit/id/' . $item['id']),
                    'remove' => $this->getURL('backend/brand/remove/id/' . $item['id'])   
                )
            );
        }

        return $brands;
    }
    
    /**
     * Возвращает массив брендов А-Я
     */
    private function getCyrillicBrands() {

        $query = "SELECT
                      `id`, `name`, `letter`, `sortorder`
                  FROM
                      `brands`
                  WHERE
                      `letter` REGEXP '[А-Я]'
                  ORDER BY
                      `letter`, `sortorder`";
        $items = $this->database->fetchAll($query);
        
        $brands = array();
        $letter = '';
        foreach ($items as $item) {
            if ($letter != $item['letter']) {
                $letter = $item['letter'];
            }
            $brands[$letter][] = array(
                'id'        => $item['id'],
                'name'      => $item['name'],
                'sortorder' => $item['sortorder'],
                'url'       => array(
                    'up'     => $this->getURL('backend/brand/moveup/id/' . $item['id']),
                    'down'   => $this->getURL('backend/brand/movedown/id/' . $item['id']),
                    'edit'   => $this->getURL('backend/brand/edit/id/' . $item['id']),
                    'remove' => $this->getURL('backend/brand/remove/id/' . $item['id'])   
                )
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
    
    /**
     * Возвращает массив всех производителей
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
        return $this->database->fetchAll($query);
    }

    /**
     * Возвращает информацию о бренде с уникальным идентификатором $id
     */
    public function getBrand($id) {
        $query = "SELECT
                      `id`, `name`, `letter`, `maker`, `popular`, `image`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }
    
    /**
     * Возвращает true, если бренд $id популярный
     */
    private function isPopularBrand($id) {
        $query = "SELECT
                      `popular`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id";
        return (boolean)$this->database->fetchOne($query, array('id' => $id));
    }
    
    /**
     * Возвращает букву бренда $id
     */
    private function getBrandLetter($id) {
        $query = "SELECT
                      `letter`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id";
        return $this->database->fetchOne($query, array('id' => $id));
    }

    /**
     * Функция добавляет бренд (новую запись в таблицу `brands` базы данных)
     */
    public function addBrand($data) {

        // порядок сортировки, помещаем новый бренд в конец списка
        // брендов на букву «A», «B», «C» и т.п.
        $data['sortorder'] = 0;
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `brands`
                  WHERE
                      `letter` = :letter";
        $data['sortorder'] = $this->database->fetchOne($query, array('letter' => $data['letter'])) + 1;
        
        // если это популярный бренд, помещаем его в конец списка популярных брендов; поле `popular`
        // таблицы `brands` не только указывает на популярность, но и задает порядок сортировки
        if ($data['popular']) {
            $query = "SELECT
                          IFNULL(MAX(`popular`), 0)
                      FROM
                          `brands`
                      WHERE
                          `popular` = 1";
            $data['popular'] = $this->database->fetchOne($query) + 1;
        } else {
            $data['popular'] = 0;
        }

        $query = "INSERT INTO `brands`
                  (
                      `name`,
                      `letter`,
                      `maker`,
                      `image`,
                      `popular`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      :letter,
                      :maker,
                      '',
                      :popular,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // загружаем файл изображения
        $this->uploadImage($id);

    }

    /**
     * Функция обновляет бренд (запись в таблице `brands` базы данных)
     */
    public function updateBrand($data) {
        
        // если бренд был популярным, а теперь нет
        if ($this->isPopularBrand($data['id']) && !$data['popular']) {
            $query = "UPDATE
                          `brands`
                      SET
                          `popular` = 0
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'id' => $data['id']
                )
            );
            // обновляем порядок сортировки популярных брендов; поле `popular` таблицы `brands`
            // не только указывает на популярность, но и задает порядок сортировки
            $query = "SELECT
                          `id`
                      FROM
                          `brands`
                      WHERE
                          `popular` <> 0
                      ORDER BY
                          `popular`";
            $brands = $this->database->fetchAll($query);
            $popular = 1;
            foreach ($brands as $item) {
                $query = "UPDATE
                              `brands`
                          SET
                              `popular` = :popular
                          WHERE
                              `id` = :id";
                $this->database->execute(
                    $query,
                    array(
                        'popular' => $popular,
                        'id' => $item['id']
                    )
                );
                $popular++;
            }
        }
        
        // если бренд не был популярным, а теперь да
        if (!$this->isPopularBrand($data['id']) && $data['popular']) {
            // помещаем его в конец списка популярных брендов; поле `popular` таблицы `brands`
            // не только указывает на популярность, но и задает порядок сортировки
            $query = "SELECT
                          IFNULL(MAX(`popular`), 0)
                      FROM
                          `brands`
                      WHERE
                          `popular` <> 0";
            $popular = $this->database->fetchOne($query) + 1;
            $query = "UPDATE
                          `brands`
                      SET
                          `popular` = :popular
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'popular' => $popular,
                    'id' => $data['id']
                )
            );
        }
        
        // с популярностью разобрались, эти данные больше не нужны
        unset($data['popular']);
        
        // если изменилась буква бренда
        $oldLetter = $this->getBrandLetter($data['id']);
        if ($oldLetter !== $data['letter']) {
            // помещаем бренд в конец списка брендов новой буквы
            $query = "SELECT
                          IFNULL(MAX(`sortorder`), 0)
                      FROM
                          `brands`
                      WHERE
                          `letter` = :letter";
            $sortorder = $this->database->fetchOne($query, array('letter' => $data['letter'])) + 1;
            $query = "UPDATE
                          `brands`
                      SET
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute(
                $query,
                array(
                    'sortorder' => $sortorder,
                    'id' => $data['id']
                )
            );
            // обновляем порядок сортировки брендов старой буквы
            $query = "SELECT
                          `id`
                      FROM
                          `brands`
                      WHERE
                          `letter` = :letter
                      ORDER BY
                          `sortorder`";
            $brands = $this->database->fetchAll($query, array('letter' => $oldLetter));
            $sortorder = 1;
            foreach ($brands as $item) {
                $query = "UPDATE
                              `brands`
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
        
        // с буквой разобрались, эти данные больше не нужны
        unset($data['letter']);

        // осталось только обновить наименование и производителя
        $query = "UPDATE
                      `brands`
                  SET
                      `name`  = :name,
                      `maker` = :maker
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

        // загружаем файл изображения
        $this->uploadImage($data['id']);

    }

    /**
     * Функция загружает файл изображения для бренда с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // проверяем, пришел ли файл изображения
        if ( ! empty($_FILES['image']['name'])) {
            // сначала удаляем старый файл
            $query = "SELECT
                          `image`
                      FROM
                          `brands`
                      WHERE
                          `id` = :id";
            $image = $this->database->fetchOne($query, array('id' => $id));
            if ( ! empty($image)) {
                if (is_file('files/brand/'. $image . '.jpg')) {
                    unlink('files/brand/'. $image . '.jpg');
                }
                $query = "UPDATE
                              `brands`
                          SET
                              `image` = ''
                          WHERE
                              `id` = :id";
                $this->database->execute(
                    $query,
                    array(
                        'id' => $id
                    )
                );
            }
            // проверяем, что при загрузке не произошло ошибок
            if ($_FILES['image']['error'] == 0) {
                // если файл загружен успешно, то проверяем - изображение?
                $mimetypes = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');
                if (in_array($_FILES['image']['type'], $mimetypes)) {
                    $image = md5(uniqid(rand(), true));
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['image']['tmp_name'],
                        'files/brand/'. $image . '.jpg',
                        120,
                        60,
                        'jpg'
                    );
                    $query = "UPDATE
                                  `brands`
                              SET
                                  `image` = :image
                              WHERE
                                  `id` = :id";
                    $this->database->execute(
                        $query,
                        array(
                            'image' => $image,
                            'id' => $id
                        )
                    );
                }
            }
        }
    }

    /**
     * Функция опускает бренд вниз в списке
     */
    public function moveBrandDown($id) {
        $id_item_down = $id;
        // буква и порядок следования бренда, который опускается вниз
        $query = "SELECT
                      `letter`, `sortorder`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id_item_down";
        $res = $this->database->fetch($query, array('id_item_down' => $id_item_down));
        $letter = $res['letter'];
        $order_down = $res['sortorder'];
        // порядок следования и id бренда, который находится ниже и будет поднят вверх,
        // поменявшись местами с брендом, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `brands`
                  WHERE
                      `letter` = :letter AND `sortorder` > :order_down
                  ORDER BY
                      `sortorder`
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'letter' => $letter,
                'order_down' => $order_down
            )
        );
        // если запрос вернул false, значит бренд и так самый последний
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['sortorder'];
            // меняем местами бренды
            $query = "UPDATE
                          `brands`
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
                          `brands`
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
     * Функция поднимает бренд вверх в списке
     */
    public function moveBrandUp($id) {
        $id_item_up = $id;
        // буква и порядок следования бренда, который поднимается вверх
        $query = "SELECT
                      `letter`, `sortorder`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id_item_up";
        $res = $this->database->fetch($query, array('id_item_up' => $id_item_up));
        $letter = $res['letter'];
        $order_up = $res['sortorder'];
        // порядок следования и id бренда, который находится выше и будет опущен вниз,
        // поменявшись местами с брендом, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `brands`
                  WHERE
                      `letter` = :letter AND `sortorder` < :order_up
                  ORDER BY
                      `sortorder` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'letter' => $letter,
                'order_up' => $order_up
            )
        );
        // если запрос вернул false, значит бренд и так самый первый
        // в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['sortorder'];
            // меняем местами бренды
            $query = "UPDATE
                          `brands`
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
                          `brands`
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
     * Функция опускает популярный бренд вниз в списке
     */
    public function movePopularDown($id) {
        /*
         * поле `popular` таблицы `brands` не только указывает на популярность бренда,
         * но и задает порядок сортировки популярных брендов
         */
        $id_item_down = $id;
        // порядок следования популярного бренда, который опускается вниз
        $query = "SELECT
                      `popular`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id популярного бренда, который находится ниже и будет поднят вверх,
        // поменявшись местами с популярным брендом, который опускается вниз
        $query = "SELECT
                      `id`, `popular`
                  FROM
                      `brands`
                  WHERE
                      `popular` <> 0 AND `popular` > :order_down
                  ORDER BY
                      `popular`
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'order_down' => $order_down
            )
        );
        // если запрос вернул false, значит популярный бренд и так
        // самый последний в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_up = $res['id'];
            $order_up = $res['popular'];
            // меняем местами популярные бренды
            $query = "UPDATE
                          `brands`
                      SET
                          `popular` = :order_down
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
                          `brands`
                      SET
                          `popular` = :order_up
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
     * Функция поднимает популярный бренд вверх в списке
     */
    public function movePopularUp($id) {
        /*
         * поле `popular` таблицы `brands` не только указывает на популярность бренда,
         * но и задает порядок сортировки популярных брендов
         */
        $id_item_up = $id;
        // порядок следования популярного бренда, который поднимается вверх
        $query = "SELECT
                      `popular`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id популярного бренда, который находится выше и будет опущен вниз,
        // поменявшись местами с популярным брендом, который поднимается вверх
        $query = "SELECT
                      `id`, `popular`
                  FROM
                      `brands`
                  WHERE
                      `popular` <> 0 AND `popular` < :order_up
                  ORDER BY
                      `popular` DESC
                  LIMIT
                      1";
        $res = $this->database->fetch(
            $query,
            array(
                'order_up' => $order_up
            )
        );
        // если запрос вернул false, значит популярный бренд и так
        // самый первый в списке, ничего делать не надо
        if (is_array($res)) {
            $id_item_down = $res['id'];
            $order_down = $res['popular'];
            // меняем местами популярные бренды
            $query = "UPDATE
                          `brands`
                      SET
                          `popular` = :order_down
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
                          `brands`
                      SET
                          `popular` = :order_up
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
     * Функция удаляет бренд с уникальным идентификатором $id
     */
    public function removeBrand($id) {
        
        // буква удаляемого бренда
        $letter = $this->getBrandLetter($id);
        // удаляемый бренд популярный?
        $popular = $this->isPopularBrand($id);
        
        // удаляем файл изображения
        $query = "SELECT
                      `image`
                  FROM
                      `brands`
                  WHERE
                      `id` = :id";
        $image = $this->database->fetchOne($query, array('id' => $id));
        if ( ! empty($image)) {
            if (is_file('files/brand/'. $image . '.jpg')) {
                unlink('files/brand/'. $image . '.jpg');
            }
        }
        
        // удаляем запись в таблице `brands` БД
        $query = "DELETE FROM `brands` WHERE `id` = :id";
        $this->database->execute($query, array('id' => $id));
        
        // обновляем порядок следования брендов для буквы
        $query = "SELECT
                      `id`
                  FROM
                      `brands`
                  WHERE
                      `letter` = :letter
                  ORDER BY
                      `sortorder`";
        $brands = $this->database->fetchAll($query, array('letter' => $letter));
        $sortorder = 1;
        foreach ($brands as $item) {
            $query = "UPDATE
                          `brands`
                      SET
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $item['id']));
            $sortorder++;
        }
        
        // если это был популяный бренд
        if ($popular) {
            // обновляем порядок сортировки популярных брендов; поле `popular` таблицы `brands`
            // не только указывает на популярность, но и задает порядок сортировки
            $query = "SELECT
                          `id`
                      FROM
                          `brands`
                      WHERE
                          `popular` <> 0
                      ORDER BY
                          `popular`";
            $brands = $this->database->fetchAll($query);
            $popular = 1;
            foreach ($brands as $item) {
                $query = "UPDATE
                              `brands`
                          SET
                              `popular` = :popular
                          WHERE
                              `id` = :id";
                $this->database->execute(
                    $query,
                    array(
                        'popular' => $popular,
                        'id' => $item['id']
                    )
                );
                $popular++;
            }
            
        }

    }

}
