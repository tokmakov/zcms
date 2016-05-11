<?php
/**
 * Класс Partner_Backend_Model для работы с партнерами, взаимодействует
 * с базой данных, административная часть сайта
 */
class Partner_Backend_Model extends Backend_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Возвращает массив всех партнеров компании
     */
    public function getAllPartners() {
        $query = "SELECT
                      `id`, `name`, DATE_FORMAT(`expire`, '%d.%m.%Y') AS `expire`,
                      (NOW() > `expire`) AS `expired`, `sortorder`
                  FROM
                      `partners`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $partners = $this->database->fetchAll($query);
        // добавляем в массив URL ссылок для редактирования и удаления
        foreach($partners as $key => $value) {
            $partners[$key]['url'] = array(
                'up'     => $this->getURL('backend/partner/moveup/id/' . $value['id']),
                'down'   => $this->getURL('backend/partner/movedown/id/' . $value['id']),
                'edit'   => $this->getURL('backend/partner/edit/id/' . $value['id']),
                'remove' => $this->getURL('backend/partner/remove/id/' . $value['id'])
            );
        }
        return $partners;
    }

    /**
     * Возвращает информацию о партнере с уникальным идентификатором $id
     */
    public function getPartner($id) {
        $query = "SELECT
                      `id`, `name`, `image`, `alttext`, DATE_FORMAT(`expire`, '%d.%m.%Y') AS `expire`,
                      (NOW() > `expire`) AS `expired`
                  FROM
                      `partners`
                  WHERE
                      `id` = :id";
        return $this->database->fetch($query, array('id' => $id));
    }

    /**
     * Функция добавляет партнера (новую запись в таблицу partners базы данных)
     */
    public function addPartner($data) {

        // порядок сортировки
        $data['sortorder'] = 0;
        $query = "SELECT
                      IFNULL(MAX(`sortorder`), 0)
                  FROM
                      `partners`
                  WHERE
                      1";
        $data['sortorder'] = $this->database->fetchOne($query, array()) + 1;
        
        $tmp           = explode( '.', $data['expire'] );
        $data['expire'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];

        $query = "INSERT INTO `partners`
                  (
                      `name`,
                      `image`,
                      `alttext`,
                      `expire`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :name,
                      '',
                      :alttext,
                      :expire,
                      :sortorder
                  )";
        $this->database->execute($query, $data);
        $id = $this->database->lastInsertId();

        // загружаем файл изображения
        $this->uploadImage($id);

    }

    /**
     * Функция обновляет партнера (запись в таблице partners базы данных)
     */
    public function updatePartner($data) {
        
        $tmp           = explode( '.', $data['expire'] );
        $data['expire'] = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];

        $query = "UPDATE
                      `partners`
                  SET
                      `name`    = :name,
                      `alttext` = :alttext,
                      `expire`  = :expire
                  WHERE
                      `id` = :id";
        $this->database->execute($query, $data);

        // загружаем файл изображения
        $this->uploadImage($data['id']);

    }

    /**
     * Функция загружает файл изображения для партнера с
     * уникальным идентификатором $id
     */
    private function uploadImage($id) {

        // проверяем, пришел ли файл изображения
        if ( ! empty($_FILES['image']['name'])) {
            // сначала удаляем старые файлы
            $query = "SELECT
                          `image`
                      FROM
                          `partners`
                      WHERE
                          `id` = :id";
            $image = $this->database->fetchOne($query, array('id' => $id));
            if ( ! empty($image)) {
                if (is_file('files/partner/thumbs/'. $image . '.jpg')) {
                    unlink('files/partner/thumbs/'. $image . '.jpg');
                }
                if (is_file('files/partner/images/'. $image . '.jpg')) {
                    unlink('files/partner/images/'. $image . '.jpg');
                }
                $query = "UPDATE
                              `partners`
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
                        'files/partner/thumbs/'. $image . '.jpg',
                        200,
                        200,
                        'jpg',
                        array(245,245,245)
                    );
                    // изменяем размер изображения
                    $this->resizeImage(
                        $_FILES['image']['tmp_name'],
                        'files/partner/images/'. $image . '.jpg',
                        1000,
                        1000,
                        'jpg',
                        array(245,245,245)
                    );
                    $query = "UPDATE
                                  `partners`
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
     * Функция опускает партнера вниз в списке
     */
    public function movePartnerDown($id) {
        $id_item_down = $id;
        // порядок следования партнера, который опускается вниз
        $query = "SELECT
                      `sortorder`
                  FROM
                      `partners`
                  WHERE
                      `id` = :id_item_down";
        $order_down = $this->database->fetchOne($query, array('id_item_down' => $id_item_down));
        // порядок следования и id партнера, который находится ниже и будет поднят вверх,
        // поменявшись местами с партнером, который опускается вниз
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `partners`
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
            // меняем местами партнеров
            $query = "UPDATE
                          `partners`
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
                          `partners`
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
     * Функция поднимает партнера вверх в списке
     */
    public function movePartnerUp($id) {
        $id_item_up = $id;
        // порядок следования партнера, который поднимается вверх
        $query = "SELECT
                      `sortorder`
                  FROM
                      `partners`
                  WHERE
                      `id` = :id_item_up";
        $order_up = $this->database->fetchOne($query, array('id_item_up' => $id_item_up));
        // порядок следования и id партнера, который находится выше и будет опущен вниз,
        // поменявшись местами с партнером, который поднимается вверх
        $query = "SELECT
                      `id`, `sortorder`
                  FROM
                      `partners`
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
            // меняем местами партнеров
            $query = "UPDATE
                          `partners`
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
                          `partners`
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
     * Функция удаляет партнера с уникальным идентификатором $id
     */
    public function removePartner($id) {
        // удаляем файлы изображения
        $query = "SELECT
                      `image`
                  FROM
                      `partners`
                  WHERE
                      `id` = :id";
        $image = $this->database->fetchOne($query, array('id' => $id));
        if ( ! empty($image)) {
            if (is_file('files/partner/thumbs/'. $image . '.jpg')) {
                unlink('files/partner/thumbs/'. $image . '.jpg');
            }
            if (is_file('files/partner/images/'. $image . '.jpg')) {
                unlink('files/partner/images/'. $image . '.jpg');
            }
        }
        // удаляем запись в таблице `partners` БД
        $query = "DELETE FROM `partners` WHERE `id` = :id";
        $this->database->execute($query, array('id' => $id));
        // обновляем порядок следования партнеров
        $query = "SELECT
                      `id`
                  FROM
                      `partners`
                  WHERE
                      1
                  ORDER BY
                      `sortorder`";
        $partners = $this->database->fetchAll($query, array());
        $sortorder = 1;
        foreach ($partners as $partner) {
            $query = "UPDATE
                          `partners`
                      SET
                          `sortorder` = :sortorder
                      WHERE
                          `id` = :id";
            $this->database->execute($query, array('sortorder' => $sortorder, 'id' => $partner['id']));
            $sortorder++;
        }
    }

}
