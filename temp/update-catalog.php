<?php
/**
 * Для запуска из командной строки для чтения xml и обновления каталога
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ZCMS', true);

chdir('..');

// поддержка кодировки UTF-8
require 'app/include/utf8.php';
// автоматическая загрузка классов
require 'app/include/autoload.php';
// правила маршрутизации
require 'app/routing.php';
// настройки приложения
require 'app/settings.php';
Config::init($settings);
// реестр, для хранения всех объектов приложения
$register = Register::getInstance();
// сохраняем в реестре настройки, чтобы везде иметь к ним доступ
$register->config = Config::getInstance();
// кэширование данных
$register->cache = Cache::getInstance();
// база данных
$register->database = Database::getInstance();

if (is_file('temp/errors.txt')) {
    unlink('temp/errors.txt');
}

parseXML($register);
updateTempTables($register);
checkImages($register);
updateWorkTables($register);

function parseXML($register) {
    
    echo 'PARSE XML' . PHP_EOL;
    
    $register->database->execute('TRUNCATE TABLE `tmp_categories`');
    $register->database->execute('TRUNCATE TABLE `tmp_products`');
    $register->database->execute('TRUNCATE TABLE `tmp_makers`');
    $register->database->execute('TRUNCATE TABLE `tmp_groups`');
    $register->database->execute('TRUNCATE TABLE `tmp_params`');
    $register->database->execute('TRUNCATE TABLE `tmp_values`');
    $register->database->execute('TRUNCATE TABLE `tmp_group_param_value`');
    $register->database->execute('TRUNCATE TABLE `tmp_product_param_value`');
    $register->database->execute('TRUNCATE TABLE `tmp_docs`');
    $register->database->execute('TRUNCATE TABLE `tmp_doc_prd`');
    $register->database->execute('TRUNCATE TABLE `tmp_certs`');
    $register->database->execute('TRUNCATE TABLE `tmp_cert_prod`');

    $register->database->execute('TRUNCATE TABLE `temp_related`');
    
    $xml = simplexml_load_file('temp/example.xml');
    
    // категории
    foreach ($xml->categories->category as $category) {
        $data = array();
        $data['code'] = $category['id'];
        echo 'category ' . $data['code'] . PHP_EOL;
        $data['parent'] = $category['parent'];
        $data['sortorder'] = $category['sortorder'];
        $data['name'] = trim($category);
        $query = "INSERT INTO `tmp_categories`
                  (
                      `code`,
                      `parent`,
                      `name`,
                      `sortorder`
                  )
                  VALUES
                  (
                      :code,
                      :parent,
                      :name,
                      :sortorder
                   )";
        $register->database->execute($query, $data);
    }
    
    // производители
    foreach ($xml->makers->maker as $maker) {
        $data = array();
        $data['code'] = $maker['id'];
        echo 'maker ' . $data['code'] . PHP_EOL;
        $data['name'] = trim($maker);
        $query = "INSERT INTO `tmp_makers`
                  (
                      `code`,
                      `name`
                  )
                  VALUES
                  (
                      :code,
                      :name
                  )";
        $register->database->execute($query, $data);
    }
    
    // параметры подбора
    foreach ($xml->params->names->name as $name) {
        $data = array();
        $data['code'] = $name['id'];
        echo 'param name ' . $data['code'] . PHP_EOL;
        $data['name'] = trim($name);
        $query = "INSERT INTO `tmp_params`
                  (
                      `code`,
                      `name`
                  )
                  VALUES
                  (
                      :code,
                      :name
                  )";
        $register->database->execute($query, $data);
    }
    foreach ($xml->params->values->value as $value) {
        $data = array();
        $data['code'] = $value['id'];
        echo 'param value ' . $data['code'] . PHP_EOL;
        $data['name'] = trim($value);
        $query = "SELECT 1 FROM `tmp_values` WHERE `name` = :name";
        $res = $register->database->fetchOne($query, array('name' => $data['name']));
        if ($res) {
            // file_put_contents('temp/errors.txt', 'Дублирование значения параметра параметра «'.$data['name'].'»'.PHP_EOL, FILE_APPEND);
        }
        $query = "INSERT INTO `tmp_values`
                  (
                      `code`,
                      `name`
                  )
                  VALUES
                  (
                      :code,
                      :name
                  )";
        $register->database->execute($query, $data);
    }
    
    // функциональные группы
    foreach ($xml->groups->group as $group) {
        $data = array();
        $data['code'] = $group['id'];
        echo 'group ' . $data['code'] . PHP_EOL;
        $data['name'] = trim($group->name);
        $query = "INSERT INTO `tmp_groups`
                  (
                      `code`,
                      `name`
                  )
                  VALUES
                  (
                      :code,
                      :name
                  )";
        $register->database->execute($query, $data);
        // привязанные к группе параметры подбора
        foreach ($group->params->param as $param) {
            $dt = array();
            $dt['group_code'] = $group['id'];
            $dt['param_code'] = $param['name'];
            $dt['value_code'] = $param['value'];
            $dt['concat_code'] = md5($dt['group_code'] . $dt['param_code'] . $dt['value_code']);
            $query = "INSERT INTO `tmp_group_param_value`
                      (
                          `group_code`,
                          `param_code`,
                          `value_code`,
                          `concat_code`
                      )
                      VALUES
                      (
                          :group_code,
                          :param_code,
                          :value_code,
                          :concat_code
                      )";
              $register->database->execute($query, $dt);
        }
    }
    
    // файлы документации
    foreach ($xml->docs->doc as $doc) {
        $data = array();
        $data['code'] = $doc['id'];
        echo 'doc ' . $data['code'] . PHP_EOL;
        $data['title'] = trim($doc->title);
        $temp = trim($doc->file);
        $data['filename'] = $temp[0] . '/' . $temp[1] . '/' . $temp;
        $data['filetype'] = pathinfo($data['filename'], PATHINFO_EXTENSION);
        $query = "INSERT INTO `tmp_docs`
                  (
                      `code`,
                      `title`,
                      `filename`,
                      `filetype`,
                      `md5`,
                      `uploaded`
                  )
                  VALUES
                  (
                      :code,
                      :title,
                      :filename,
                      :filetype,
                      '',
                      NOW()
                  )";
        $register->database->execute($query, $data);
    }
    
    // сертификаты
    foreach ($xml->certs->cert as $cert) {
        $data = array();
        $data['code'] = $cert['id'];
        echo 'cert ' . $data['code'] . PHP_EOL;
        $data['title'] = trim($cert->title);
        if (empty($data['title'])) continue;
        $filename = trim($cert->files->name);
        if (empty($filename)) continue;
        $data['filename'] = $filename[0] . '/' . $filename[1] . '/' . $filename;
        $data['count'] = (int)$cert->files->count;
        if (empty($data['count'])) continue;
        $query = "INSERT INTO `tmp_certs`
                  (
                      `code`,
                      `title`,
                      `filename`,
                      `count`
                  )
                  VALUES
                  (
                      :code,
                      :title,
                      :filename,
                      :count
                  )";
        $register->database->execute($query, $data);
    }
    
    // товары
    foreach ($xml->products->product as $product) {
        $data = array();
        $data['code'] = $product['code'];
        $data['id'] = (int)$product['code'];
        echo 'product ' . $data['code'] . PHP_EOL;
        
        // родительская категория
        $parents = explode(',', $product['category']);
        $data['category'] = $parents[0];
        $data['category2'] = '';
        if (isset($parents[1])) {
            $data['category2'] = $parents[1];
        }
        // функциональная группа
        $data['group'] = $product['group'];
        // производитель
        $data['maker'] = $product['maker'];
        // лидер продаж?
        $data['hit'] = (int)$product['hit'];
        // новинка?
        $data['new'] = (int)$product['new'];
        // порядок сортировки внутри родительской категории
        $data['sortorder'] = (int)$product['sortorder'];
        
        // торговое наименование
        $data['name'] = trim($product->name);
        // функциональное наименование
        $data['title'] = trim($product->title);
        // единица измерения
        $data['unit'] = 0;
        $km = false;
        if ($product->unit == '29dd8076-30d1-11da-bf68-0011d802924c') {
            $data['unit'] = 1; // шт.
        } elseif ($product->unit == '921a64ec-6fe9-11dc-91f2-0030483135e6') {
            $data['unit'] = 2; // компл.
        } elseif ($product->unit == '650d0173-30d1-11da-bf68-0011d802924c') {
            $data['unit'] = 3; // упак.
        } elseif ($product->unit == '650d0166-30d1-11da-bf68-0011d802924c') {
            $km = true;
            $data['unit'] = 4; // км.
        } elseif ($product->unit == '650d0165-30d1-11da-bf68-0011d802924c') {
            $data['unit'] = 4; // метр
        } elseif ($product->unit == '650d0171-30d1-11da-bf68-0011d802924c') {
           $data['unit'] = 5; // пара 
        }
        // цена
        $data['price']  = (float)$product->price;
        $data['price2'] = (float)$product->price2;
        $data['price3'] = (float)$product->price3;
        $data['price4'] = (float)$product->price4;
        $data['price5'] = (float)$product->price5;
        $data['price6'] = (float)$product->price6;
        $data['price7'] = (float)$product->price7;
        if ($km) {
            $data['price']  = 0.001 * $data['price'];
            $data['price2'] = 0.001 * $data['price2'];
            $data['price3'] = 0.001 * $data['price3'];
            $data['price4'] = 0.001 * $data['price4'];
            $data['price5'] = 0.001 * $data['price5'];
            $data['price6'] = 0.001 * $data['price6'];
            $data['price7'] = 0.001 * $data['price7'];
        }
        // краткое описание
        $data['shortdescr'] = trim($product->shortdescr);
        // назначение
        $data['purpose'] = trim($product->purpose);
        // технические характеристики
        $techdata = array();
        $data['techdata'] = '';
        foreach ($product->techdata->item as $item) {
            $techdata[] = array(trim($item->name), trim($item->value));
        }
        if (!empty($techdata)) {
            $data['techdata'] = serialize($techdata);
        }
        // особенности
        $data['features'] = trim($product->features);
        // комплектация
        $data['complect'] = trim($product->complect);
        // доп.оборудование
        $data['equipment'] = trim($product->equipment);
        // доп.информация
        $data['padding'] = trim($product->padding);

        $name = strtoupper(md5($data['code']));
        $name = $name[0] . '/' . $name[1] . '/' . $name . '.jpg';
        $data['image'] = '';
        if (is_file('files/catalog/src/imgs/'.$name)) {
            $data['image'] = $name;
        }
        // ЭТОТ КОД ПОТОМ УДАЛИТЬ
        /*
        $name = strtoupper(md5($data['code']));
        $name = $name[0] . '/' . $name[1] . '/' . $name;
        $image = false;
        $data['image'] = '';
        if (is_file('files/catalog/src/temp/'.$data['code'].'.jpeg')) {
            $name = $name . '.jpg';
            //copy('files/catalog/src/temp/'.$data['code'].'.jpeg', 'files/catalog/src/imgs/'.$name);
            $image = true;
        }
        if (is_file('files/catalog/src/temp/'.$data['code'].'.jpg')) {
            $name = $name . '.jpg'; // потому как все файлы теперь jpg
            //copy('files/catalog/src/temp/'.$data['code'].'.jpg', 'files/catalog/src/imgs/'.$name);
            $image = true;
        }
        if (is_file('files/catalog/src/temp/'.$data['code'].'.png')) {
            $name = $name . '.jpg'; // потому как все файлы теперь jpg
            //copy('files/catalog/src/temp/'.$data['code'].'.png', 'files/catalog/src/imgs/'.$name);
            $image = true;
        }
        if (is_file('files/catalog/src/temp/'.$data['code'].'.gif')) {
            $name = $name . '.jpg'; // потому как все файлы теперь jpg
            //copy('files/catalog/src/temp/'.$data['code'].'.gif', 'files/catalog/src/imgs/'.$name);
            $image = true;
        }
        if ($image) {
            $data['image'] = $name;
        }
        */
        // фото
        /*
        $data['image'] = '';
        if (!empty($product->image)) {
            $temp = $product->image;
            $data['image'] = $temp[0] . '/' . $temp[1] . '/' . $temp;
        }
        */
        
        // параметры подбора
        foreach ($product->params->param as $param) {
            // TODO: возникают дубли
            $concat_code = md5($data['id'] . $param['name'] . $param['value']);
            $query = "SELECT 1 FROM `tmp_product_param_value` WHERE `concat_code` = :concat_code";
            $res = $register->database->fetchOne($query, array('concat_code' => $concat_code));
            if ($res) {
                file_put_contents('temp/errors.txt', 'Дублирование привязки параметра '.$param['name'].' и значения '.$param['value'].' к товару '.$data['code'].PHP_EOL, FILE_APPEND);
                continue;
            }
            $query = "INSERT INTO `tmp_product_param_value`
                      (
                          `product_id`,
                          `param_code`,
                          `value_code`,
                          `concat_code`
                      )
                      VALUES
                      (
                          :product_id,
                          :param_code,
                          :value_code,
                          :concat_code
                      )";
            $register->database->execute(
                $query,
                array(
                    'product_id' => $data['id'],
                    'param_code' => $param['name'],
                    'value_code' => $param['value'],
                    'concat_code' => $concat_code
                )
            );
        }
        
        // файлы документации
        foreach ($product->docs->doc as $doc) {
            $concat_code = md5($data['id'] . $doc['id']);
            // TODO: возникают дубли
            $query = "SELECT 1 FROM `tmp_doc_prd` WHERE `concat_code` = :concat_code";
            $res = $register->database->fetchOne($query, array('concat_code' => $concat_code));
            if ($res) {
                file_put_contents('temp/errors.txt', 'Дублирование привязки документа '.$doc['id'].' к товару '.$data['code'].PHP_EOL, FILE_APPEND);
                continue;
            }
            $query = "INSERT INTO `tmp_doc_prd`
                      (
                          `prd_id`,
                          `doc_code`,
                          `concat_code`
                      )
                      VALUES
                      (
                          :prd_id,
                          :doc_code,
                          :concat_code
                      )";
            $register->database->execute(
                $query,
                array(
                    'prd_id' => $data['id'],
                    'doc_code' => $doc['id'],
                    'concat_code' => $concat_code
                )
            );
        }
        
        // сертификаты
        foreach ($product->certs->cert as $cert) {
            $concat_code = md5($data['id'] . $cert['id']);
            // TODO: возникают дубли
            $query = "SELECT 1 FROM `tmp_cert_prod` WHERE `concat_code` = :concat_code";
            $res = $register->database->fetchOne($query, array('concat_code' => $concat_code));
            if ($res) {
                file_put_contents('temp/errors.txt', 'Дублирование привязки сертификата '.$cert['id'].' к товару '.$data['code'].PHP_EOL, FILE_APPEND);
                continue;
            }
            $query = "INSERT INTO `tmp_cert_prod`
                      (
                          `prod_id`,
                          `cert_code`,
                          `concat_code`
                      )
                      VALUES
                      (
                          :prod_id,
                          :cert_code,
                          :concat_code
                      )";
            $register->database->execute(
                $query,
                array(
                    'prod_id' => $data['id'],
                    'cert_code' => $cert['id'],
                    'concat_code' => $concat_code
                )
            );
        }
        
        // связанные товары
        foreach ($product->linked->prd as $item) {
            $query = "INSERT INTO `temp_related`
                      (
                          `id1`,
                          `id2`,
                          `count`
                      )
                      VALUES
                      (
                          :id1,
                          :id2,
                          :count
                      )";
            $register->database->execute(
                $query,
                array('id1' => $data['id'], 'id2' => (int)$item['code'], 'count' => (int)$item['count'])
            );
        }
        
        $data['keywords'] = '';
        $data['description'] = '';
        // TODO: возникают дубли
        $query = "SELECT 1 FROM `tmp_products` WHERE `id` = :id";
        $res = $register->database->fetchOne($query, array('id' => $data['id']));
        if ($res) {
            file_put_contents('temp/errors.txt', 'Дублирование товара '.$data['code'].PHP_EOL, FILE_APPEND);
            continue;
        }
        $query = "INSERT INTO `tmp_products`
                  (
                      `id`,
                      `category`,
                      `category2`,
                      `group`,
                      `maker`,
                      `hit`,
                      `new`,
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
                      `image`,
                      `sortorder`,
                      `updated`
                  )
                  VALUES
                  (
                      :id,
                      :category,
                      :category2,
                      :group,
                      :maker,
                      :hit,
                      :new,
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
                      :image,
                      :sortorder,
                      NOW()
                  )";
        $register->database->execute($query, $data);
    }
}

function updateTempTables($register) {
    
    echo 'UPDATE TEMP TABLES'. PHP_EOL;
    
    /*
     * КАТЕГОРИИ
     */
    echo 'update temp table categories'. PHP_EOL;
    // удаляем те категории, которых уже нет в 1С
    $query = "DELETE FROM `temp_categories` WHERE `code` NOT IN (SELECT `code` FROM `tmp_categories` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые категории: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_categories` WHERE `code` NOT IN (SELECT `code` FROM `temp_categories` WHERE 1)";
    $categories = $register->database->fetchAll($query);
    foreach($categories as $category) {
        $query = "INSERT INTO `temp_categories`
                  (
                      `parent`,
                      `name`,
                      `keywords`,
                      `description`,
                      `sortorder`,
                      `globalsort`,
                      `code`
                  )
                  VALUES
                  (
                      :parent,
                      :name,
                      '',
                      '',
                      :sortorder,
                      '00000000000000000000',
                      :code
                  )";
        $data = array();
        $data['parent'] = 0;
        $data['name'] = trim($category['name']);
        $data['sortorder'] = $category['sortorder'];
        $data['code'] = $category['code'];
        $register->database->execute($query, $data);   
    }
    // теперь таблицы tmp_categories и temp_categories содержат одинаковое
    // количество записей; обновляем все записи в таблице temp_categories
    $query = "SELECT * FROM `tmp_categories` WHERE 1";
    $categories = $register->database->fetchAll($query);
    foreach($categories as $category) {
        // получаем идентификатор родителя текущей категории (целое положительное число)
        $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :parent_code";
        $parent = 0; // для корневой категории запрос вернет false
        $temp = $register->database->fetchOne($query, array('parent_code' => $category['parent']));
        if (false !== $temp) {
            $parent = $temp;
        }
        $query = "UPDATE
                      `temp_categories`
                  SET
                      `parent` = :parent,
                      `name` = :name,
                      `sortorder` = :sortorder
                  WHERE
                      `code` = :code";
        $data = array();
        $data['parent'] = $parent;
        $data['name'] = $category['name'];
        $data['sortorder'] = $category['sortorder'];
        $data['code'] = $category['code'];
        $register->database->execute($query, $data);
    }
    // устанавливаем порядок сортировки категорий
    $query ="SELECT `id` FROM `temp_categories` WHERE `parent` = 0 ORDER BY `sortorder`";
    $roots = $register->database->fetchAll($query);
    $i = 1;
    foreach($roots as $root) {
        $sort = $i;
        if (strlen($sort) == 1) $sort = '0' . $sort;
        $query = "UPDATE `temp_categories` SET `globalsort` = '" . $sort . "000000000000000000' WHERE `id` = " . $root['id'];
        $register->database->execute($query);
        updateSortOrderAllCategories($root['id'], $sort . '000000000000000000', 1);
        $i++;
    }
    $query = "UPDATE `temp_categories` SET `name` = :name WHERE `id` = :id";
    $register->database->execute($query, array('name' => 'Охранно-пожарная сигнализация', 'id' => 3));
    $query = "UPDATE `temp_categories` SET `name` = :name WHERE `id` = :id";
    $register->database->execute($query, array('name' => 'Охранное телевидение', 'id' => 185));
    $query = "UPDATE `temp_categories` SET `name` = :name WHERE `id` = :id";
    $register->database->execute($query, array('name' => 'Контроль и управление доступом', 'id' => 651));
    $query = "UPDATE `temp_categories` SET `name` = :name WHERE `id` = :id";
    $register->database->execute($query, array('name' => 'Системы оповещения', 'id' => 883));
    
    /*
     * ПРОИЗВОДИТЕЛИ
     */
    echo 'update temp table makers'. PHP_EOL;
    // удаляем тех производителей, которых уже нет в 1С
    $query = "DELETE FROM `temp_makers` WHERE `code` NOT IN (SELECT `code` FROM `tmp_makers` WHERE 1)";
    $register->database->execute($query);
    // добавляем новых производителей: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_makers` WHERE `code` NOT IN (SELECT `code` FROM `temp_makers` WHERE 1)";
    $makers = $register->database->fetchAll($query);
    foreach ($makers as $maker) {
        $query = "INSERT INTO `temp_makers`
                  (
                      `name`,
                      `altname`,
                      `keywords`,
                      `description`,
                      `body`,
                      `code`
                  )
                  VALUES
                  (
                      :name,
                      :name,
                      '',
                      '',
                      '',
                      :code
                  )";
        $register->database->execute($query, array('name' => trim($maker['name']), 'code' => $maker['code'])); 
    }
    // теперь таблицы tmp_makers и temp_makers содержат одинаковое
    // количество записей; обновляем все записи таблицы temp_makers
    $query = "SELECT * FROM `tmp_makers` WHERE 1";
    $makers = $register->database->fetchAll($query);
    foreach($makers as $maker) {
        $query = "UPDATE
                      `temp_makers`
                  SET
                      `name` = :name,
                      `altname` = :name
                  WHERE
                      `code` = :code";
        $register->database->execute($query, array('name' => $maker['name'], 'code' => $maker['code'])); 
    }
    
    /*
     * ФУНКЦИОНАЛЬНЫЕ ГРУППЫ
     */
    echo 'update temp table groups'. PHP_EOL;
    // удаляем те функциональные группы, которых уже нет в 1С
    $query = "DELETE FROM `temp_groups` WHERE `code` NOT IN (SELECT `code` FROM `tmp_groups` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые группы: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_groups` WHERE `code` NOT IN (SELECT `code` FROM `temp_groups` WHERE 1)";
    $groups = $register->database->fetchAll($query);
    foreach ($groups as $group) {
        $query = "INSERT INTO `temp_groups`
                  (
                      `name`,
                      `code`
                  )
                  VALUES
                  (
                      :name,
                      :code
                  )";
        $register->database->execute($query, array('name' => $group['name'], 'code' => $group['code'])); 
    }
    // теперь таблицы tmp_groups и temp_groups содержат одинаковое
    // количество записей; обновляем все записи таблицы temp_groups
    $query = "SELECT * FROM `tmp_groups` WHERE 1";
    $groups = $register->database->fetchAll($query);
    foreach($groups as $group) {
         $query = "UPDATE
                       `temp_groups`
                   SET
                       `name` = :name
                   WHERE
                       `code` = :code";
         $register->database->execute($query, array('name' => $group['name'], 'code' => $group['code'])); 
    }
    
    /*
     * ТОВАРЫ
     */
    echo 'update temp table products'. PHP_EOL;
    // удаляем те товары, которых уже нет в 1С
    $query = "SELECT `image` FROM `temp_products` WHERE `id` NOT IN (SELECT `id` FROM `tmp_products` WHERE 1) AND `image`<>''";
    $items = $register->database->fetchAll($query);
    foreach ($items as $item) {
        if (is_file('files/catalog/imgs/small/'.$item['image'])) {
            unlink('files/catalog/imgs/small/'.$item['image']);
        }
        if (is_file('files/catalog/imgs/medium/'.$item['image'])) {
            unlink('files/catalog/imgs/medium/'.$item['image']);
        }
        if (is_file('files/catalog/imgs/big/'.$item['image'])) {
            unlink('files/catalog/imgs/big/'.$item['image']);
        }
    }
    $query = "DELETE FROM `temp_products` WHERE `id` NOT IN (SELECT `id` FROM `tmp_products` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые товары: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_products` WHERE `id` NOT IN (SELECT `id` FROM `temp_products` WHERE 1)";
    $products = $register->database->fetchAll($query);
    foreach ($products as $product) {
        $data = array();
        $data['id'] = $product['id'];
        // уникальный идентификатор родительской категории
        $data['category'] = 0;
        // уникальный идентификатор дополнительной категории
        $data['category2'] = 0;
        // уникальный идентификатор функциональной группы
        $data['group'] = 0;
        // уникальный идентификатор производителя
        $data['maker'] = 0;
        
        $data['image'] = '';
        if (!empty($product['image'])) {
            $image = strtolower(substr($product['image'], 0, 36)) . '.jpg';
            if (is_file('files/catalog/src/imgs/'.$product['image'])) {
                // изменяем размер фото
                $resize = resizeImage( // маленькое
                    'files/catalog/src/imgs/'.$product['image'],
                    'files/catalog/imgs/small/' . $image,
                    100,
                    100,
                    'jpg'
                );
                if (!$resize) {
                    file_put_contents('temp/errors.txt', 'Не удалось изменить размер изображения '.$product['image'].' для товара '.$product['code'].PHP_EOL, FILE_APPEND);
                    $image = '';
                } else {
                    resizeImage( // среднее
                        'files/catalog/src/imgs/'.$product['image'],
                        'files/catalog/imgs/medium/' . $image,
                        200,
                        200,
                        'jpg'
                    );
                    resizeImage( // большое
                        'files/catalog/src/imgs/'.$product['image'],
                        'files/catalog/imgs/big/' . $image,
                        500,
                        500,
                        'jpg'
                    );
                }
            }
            $data['image'] = $image;
        }
          
        $data['hit'] = $product['hit'];
        $data['new'] = $product['new'];
        $data['code'] = $product['code'];
        $data['name'] = $product['name'];
        $data['title'] = $product['title'];
        $data['keywords'] = $product['keywords'];
        $data['description'] = $product['description'];
        $data['shortdescr'] = $product['shortdescr'];
        $data['purpose'] = $product['purpose'];
        $data['techdata'] = $product['techdata'];
        $data['features'] = $product['features'];
        $data['complect'] = $product['complect'];
        $data['equipment'] = $product['equipment'];
        $data['padding'] = $product['padding'];
        $data['price'] = $product['price'];
        $data['price2'] = $product['price2'];
        $data['price3'] = $product['price3'];
        $data['price4'] = $product['price4'];
        $data['price5'] = $product['price5'];
        $data['price6'] = $product['price6'];
        $data['price7'] = $product['price7'];
        $data['unit'] = $product['unit'];
        $data['sortorder'] = $product['sortorder'];
        
        $query = "INSERT INTO `temp_products`
                  (
                      `id`,
                      `category`,
                      `category2`,
                      `group`,
                      `maker`,
                      `hit`,
                      `new`,
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
                      `image`,
                      `sortorder`,
                      `updated`
                  )
                  VALUES
                  (
                      :id,
                      :category,
                      :category2,
                      :group,
                      :maker,
                      :hit,
                      :new,
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
                      :image,
                      :sortorder,
                      NOW()
                  )";
        $register->database->execute($query, $data);
    }
    // теперь таблицы tmp_products и temp_products содержат одинаковое
    // количество записей; обновляем все записи таблицы temp_products
    $query = "SELECT * FROM `tmp_products` WHERE 1";
    $products = $register->database->fetchAll($query);
    foreach($products as $product) {
        $data = array();
        $data['id'] = $product['id'];
        // уникальный идентификатор родительской категории (целое положительное число)
        $data['category'] = 0;
        $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :code";
        $temp = $register->database->fetchOne($query, array('code' => $product['category']));
        // TODO: такого быть не должно
        if (empty($temp)) {
            file_put_contents('temp/errors.txt', 'Для товара '.$product['code'].' указана не существующая категория '.$product['category'].PHP_EOL, FILE_APPEND);
        } else {
            $data['category'] = $temp;
        }
        // уникальный идентификатор дополнительной категории (целое положительное число)
        $data['category2'] = 0;
        $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :code";
        $temp = $register->database->fetchOne($query, array('code' => $product['category2']));
        if (!empty($temp)) {
            $data['category2'] = $temp;
        }
        // уникальный идентификатор функциональной группы (целое положительное число)
        $data['group'] = 0;
        $query = "SELECT `id` FROM `temp_groups` WHERE `code` = :code";
        $temp = $register->database->fetchOne($query, array('code' => $product['group']));
        if (!empty($temp)) {
            $data['group'] = $temp;
        }
        // уникальный идентификатор производителя (целое положительное число)
        $data['maker'] = 0;
        $query = "SELECT `id` FROM `temp_makers` WHERE `code` = :code";
        $temp = $register->database->fetchOne($query, array('code' => $product['maker']));
        if (empty($temp)) {
            file_put_contents('temp/errors.txt', 'Для товара '.$product['code'].' указан не существующий производитель '.$product['maker'].PHP_EOL, FILE_APPEND);
        } else {
            $data['maker'] = $temp;
        }
        
        $data['hit'] = $product['hit'];
        $data['new'] = $product['new'];
        $data['code'] = $product['code'];
        $data['name'] = $product['name'];
        $data['title'] = $product['title'];
        $data['shortdescr'] = $product['shortdescr'];
        $data['purpose'] = $product['purpose'];
        $data['techdata'] = $product['techdata'];
        $data['features'] = $product['features'];
        $data['complect'] = $product['complect'];
        $data['equipment'] = $product['equipment'];
        $data['padding'] = $product['padding'];
        $data['price'] = $product['price'];
        $data['price2'] = $product['price2'];
        $data['price3'] = $product['price3'];
        $data['price4'] = $product['price4'];
        $data['price5'] = $product['price5'];
        $data['price6'] = $product['price6'];
        $data['price7'] = $product['price7'];
        $data['unit'] = $product['unit'];
        $data['sortorder'] = $product['sortorder'];
        
        $query = "UPDATE
                      `temp_products`
                  SET
                      `category` = :category,
                      `category2` = :category2,
                      `group` = :group,
                      `maker` = :maker,
                      `hit` = :hit,
                      `new` = :new,
                      `code` = :code,
                      `name` = :name,
                      `title` = :title,
                      `shortdescr` = :shortdescr,
                      `purpose` = :purpose,
                      `techdata` = :techdata,
                      `features` = :features,
                      `complect` = :complect,
                      `equipment` = :equipment,
                      `padding` = :padding,
                      `price` = :price,
                      `price2` = :price2,
                      `price3` = :price3,
                      `price4` = :price4,
                      `price5` = :price5,
                      `price6` = :price6,
                      `price7` = :price7,
                      `unit` = :unit,
                      `sortorder` = :sortorder
                  WHERE
                      `id` = :id";
        $register->database->execute($query, $data); 
    }
    // если у каких-то товаров изменились фото
    $query = "SELECT
                  `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`image` AS `new`, `b`.`image` AS `old`
              FROM
                  `tmp_products` `a` INNER JOIN `temp_products` `b`
                  ON `a`.`id` = `b`.`id`
              WHERE
                  LEFT(LOWER(`a`.`image`), 36) <> LEFT(`b`.`image`, 36)";
    $items = $register->database->fetchAll($query);
    foreach ($items as $item) {
        /*
         * если фото товара было удалено в 1С
         */
        if (empty($item['new'])) {
            // удаляем его и на сайте
            if (is_file('files/catalog/imgs/small/'.$item['old'])) {
                unlink('files/catalog/imgs/small/'.$item['old']);
            }
            if (is_file('files/catalog/imgs/medium/'.$item['old'])) {
                unlink('files/catalog/imgs/medium/'.$item['old']);
            }
            if (is_file('files/catalog/imgs/big/'.$item['old'])) {
                unlink('files/catalog/imgs/big/'.$item['old']);
            }
            $query = "UPDATE `temp_products` SET `image` = '' WHERE `id` = :id";
            $register->database->execute($query, array('id' => $item['id']));
            continue;
        }
        /*
         * если в 1С у товара новое фото
         */
        // сначала удаляем старое фото
        if (!empty($item['old'])) {
            if (is_file('files/catalog/imgs/small/'.$item['old'])) {
                unlink('files/catalog/imgs/small/'.$item['old']);
            }
            if (is_file('files/catalog/imgs/medium/'.$item['old'])) {
                unlink('files/catalog/imgs/medium/'.$item['old']);
            }
            if (is_file('files/catalog/imgs/big/'.$item['old'])) {
                unlink('files/catalog/imgs/big/'.$item['old']);
            }
            $query = "UPDATE `temp_products` SET `image` = '' WHERE `id` = :id";
            $register->database->execute($query, array('id' => $item['id']));
        }
        // потом добавляем новое фото
        $image = strtolower(substr($item['new'], 0, 36)) . '.jpg';
        if (is_file('files/catalog/src/imgs/'.$item['new'])) {
            // изменяем размер фото
            $resize = resizeImage( // маленькое
                'files/catalog/src/imgs/'.$item['new'],
                'files/catalog/imgs/small/' . $image,
                100,
                100,
                'jpg'
            );
            if (!$resize) {
                file_put_contents('temp/errors.txt', 'Не удалось изменить размер изображения '.$item['new'].' для товара '.$item['code'].PHP_EOL, FILE_APPEND);
            } else {
                resizeImage( // среднее
                    'files/catalog/src/imgs/'.$item['new'],
                    'files/catalog/imgs/medium/' . $image,
                    200,
                    200,
                    'jpg'
                );
                resizeImage( // большое
                    'files/catalog/src/imgs/'.$item['new'],
                    'files/catalog/imgs/big/' . $image,
                    500,
                    500,
                    'jpg'
                );
                $query = "UPDATE `temp_products` SET `image` = :image WHERE `id` = :id";
                $register->database->execute($query, array('image' => $image, 'id' => $item['id']));
            }

        }
        
    }
    
    // порядок сортировки товаров внутри родительской категории
    $query = "SELECT `id` FROM `temp_categories` WHERE `id` IN (SELECT `category` FROM `temp_products` WHERE 1)";
    $categories = $register->database->fetchAll($query);
    foreach ($categories as $category) {
        $query = "SELECT `id` FROM `temp_products` WHERE `category` = :category ORDER BY `sortorder`";
        $products = $register->database->fetchAll($query, array('category' => $category['id']));
        $sortorder = 1;
        foreach ($products as $product) {
            $query = "UPDATE `temp_products` SET `sortorder` = :sortorder WHERE `id` = :id";
            $register->database->execute($query, array('sortorder' => $sortorder, 'id' => $product['id']));
            $sortorder++;
        }
    }
    
    /*
     * ПАРАМЕТРЫ ПОДБОРА
     */
    echo 'update temp table params'. PHP_EOL;
    // удаляем те параметры подбора, которых уже нет в 1С
    $query = "DELETE FROM `temp_params` WHERE `code` NOT IN (SELECT `code` FROM `tmp_params` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые параметры: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_params` WHERE `code` NOT IN (SELECT `code` FROM `temp_params` WHERE 1)";
    $params = $register->database->fetchAll($query);
    foreach ($params as $param) {
        $query = "INSERT INTO `temp_params`
                  (
                      `name`,
                      `code`
                  )
                  VALUES
                  (
                      :name,
                      :code
                  )";
        $register->database->execute($query, array('name' => $param['name'], 'code' => $param['code'])); 
    }
    // теперь таблицы tmp_params и temp_params содержат одинаковое
    // количество записей; обновляем все записи таблицы temp_params
    $query = "SELECT * FROM `tmp_params` WHERE 1";
    $params = $register->database->fetchAll($query);
    foreach($params as $param) {
         $query = "UPDATE `temp_params` SET
                      `name` = :name
                  WHERE
                      `code` = :code";
         $register->database->execute($query, array('name' => $param['name'], 'code' => $param['code'])); 
    }
    
    /*
     * ЗНАЧЕНИЯ ПАРАМЕТРОВ ПОДБОРА
     */
    echo 'update temp table values'. PHP_EOL;
    // удаляем те значения параметров подбора, которых уже нет в 1С
    $query = "DELETE FROM `temp_values` WHERE `code` NOT IN (SELECT `code` FROM `tmp_values` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые значения параметров: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_values` WHERE `code` NOT IN (SELECT `code` FROM `temp_values` WHERE 1)";
    $values = $register->database->fetchAll($query);
    foreach ($values as $value) {
        $query = "INSERT INTO `temp_values`
                  (
                      `name`,
                      `code`
                  )
                  VALUES
                  (
                      :name,
                      :code
                  )";
        $register->database->execute($query, array('name' => $value['name'], 'code' => $value['code'])); 
    }
    // теперь таблицы tmp_values и temp_values содержат одинаковое
    // количество записей; обновляем все записи таблицы temp_values
    $query = "SELECT * FROM `tmp_values` WHERE 1";
    $values = $register->database->fetchAll($query);
    foreach($values as $value) {
         $query = "UPDATE `temp_values` SET
                      `name` = :name
                  WHERE
                      `code` = :code";
         $register->database->execute($query, array('name' => $value['name'], 'code' => $value['code'])); 
    }
    
    /*
     * ПРИВЯЗКА ПАРАМЕТРОВ И ДОПУСТИМЫХ ЗНАЧЕНИЙ К ГРУППЕ
     */
    echo 'update temp table group-param-value'. PHP_EOL;
    // удаляем записи, которых уже нет в 1С
    $query = "DELETE FROM `temp_group_param_value` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `tmp_group_param_value` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_group_param_value` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `temp_group_param_value` WHERE 1)";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        // уникальный идентификатор группы (целое положительное число)
        $query = "SELECT `id` FROM `temp_groups` WHERE `code` = :group_code";
        $group_id = $register->database->fetchOne($query, array('group_code' => $row['group_code']));
        // TODO
        if (false === $group_id) {
            file_put_contents('temp/errors.txt', 'Попытка привязать параметр '.$row['param_code'].' и значение '.$row['value_code'].' к не существующей группе '.$row['group_code'].PHP_EOL, FILE_APPEND);
            continue;
        }
        // уникальный идентификатор параметра (целое положительное число)
        $query = "SELECT `id` FROM `temp_params` WHERE `code` = :param_code";
        $param_id = $register->database->fetchOne($query, array('param_code' => $row['param_code']));
        // TODO
        if (false === $param_id) {
            file_put_contents('temp/errors.txt', 'Попытка привязать не существующий параметр '.$row['param_code'].' и значение '.$row['value_code'].' к группе '.$row['group_code'].PHP_EOL, FILE_APPEND);
            continue;
        }
        // уникальный идентификатор значения параметра (целое положительное число)
        $query = "SELECT `id` FROM `temp_values` WHERE `code` = :value_code";
        $value_id = $register->database->fetchOne($query, array('value_code' => $row['value_code']));
        // TODO
        if (false === $value_id) {
            file_put_contents('temp/errors.txt', 'Попытка привязать параметр '.$row['param_code'].' и не существующее значение '.$row['value_code'].' к группе '.$row['group_code'].PHP_EOL, FILE_APPEND);
            continue;
        }
        $query = "INSERT INTO `temp_group_param_value`
                  (
                      `group_id`,
                      `param_id`,
                      `value_id`,
                      `concat_code`
                  )
                  VALUES
                  (
                      :group_id,
                      :param_id,
                      :value_id,
                      :concat_code
                  )";
        $register->database->execute(
            $query,
            array(
                'group_id' => $group_id,
                'param_id' => $param_id,
                'value_id' => $value_id,
                'concat_code' => $row['concat_code']
            )
        ); 
    }
    
    /*
     * ПРИВЯЗКА ПАРАМЕТРОВ И ЗНАЧЕНИЙ К ТОВАРУ
     */
    echo 'update temp table product-param-value'. PHP_EOL;
    // удаляем записи, которых уже нет в 1С
    $query = "DELETE FROM `temp_product_param_value` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `tmp_product_param_value` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_product_param_value` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `temp_product_param_value` WHERE 1)";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        // уникальный идентификатор параметра (целое положительное число)
        $query = "SELECT `id` FROM `temp_params` WHERE `code` = :param_code";
        $param_id = $register->database->fetchOne($query, array('param_code' => $row['param_code']));
        // TODO: такого быть не должно - к товару привязывается параметр, которого нет в таблице temp_params
        if (false === $param_id) {
            file_put_contents('temp/errors.txt', 'Попытка привязать не существующий параметр '.$row['param_code'].' и значение '.$row['value_code'].' к товару '.$row['product_id'].PHP_EOL, FILE_APPEND);
            continue;
        }
        // уникальный идентификатор значения параметра (целое положительное число)
        $query = "SELECT `id` FROM `temp_values` WHERE `code` = :value_code";
        $value_id = $register->database->fetchOne($query, array('value_code' => $row['value_code']));
        // TODO: такого быть не должно - к товару привязывается значение, которого нет в таблице temp_values
        if (false === $value_id) {
            file_put_contents('temp/errors.txt', 'Попытка привязать параметр '.$row['param_code'].' и не существующее значение '.$row['value_code'].' к товару '.$row['product_id'].PHP_EOL, FILE_APPEND);
            continue;
        }
        $query = "INSERT INTO `temp_product_param_value`
                  (
                      `product_id`,
                      `param_id`,
                      `value_id`,
                      `concat_code`
                  )
                  VALUES
                  (
                      :product_id,
                      :param_id,
                      :value_id,
                      :concat_code
                  )";
        $register->database->execute(
            $query,
            array(
                'product_id' => $row['product_id'],
                'param_id' => $param_id,
                'value_id' => $value_id,
                'concat_code' => $row['concat_code']
            )
        ); 
    }
    
    /*
     * ФАЙЛЫ ДОКУМЕНТАЦИИ
     */
    echo 'update temp table docs'. PHP_EOL;
    // удаляем те записи о файлах документации, которых уже нет в 1С
    $query = "SELECT * FROM `temp_docs` WHERE `code` NOT IN (SELECT `code` FROM `tmp_docs` WHERE 1)";
    $items = $register->database->fetchAll($query);
    foreach ($items as $item) {
        if (!is_file('files/catalog/docs/'.$item['filename'])) {
            continue;
        }
        unlink('files/catalog/docs/'.$item['filename']);
    }
    $query = "DELETE FROM `temp_docs` WHERE `code` NOT IN (SELECT `code` FROM `tmp_docs` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_docs` WHERE `code` NOT IN (SELECT `code` FROM `temp_docs` WHERE 1)";
    $docs = $register->database->fetchAll($query);
    foreach ($docs as $doc) {
        $src = 'files/catalog/src/docs/'.$doc['filename'];
        if (!is_file($src)) {
            $register->database->execute("DELETE FROM `tmp_docs` WHERE `code` = :code", array('code' => $doc['code']));
            $register->database->execute("DELETE FROM `tmp_doc_prd` WHERE `doc_code` = :code", array('code' => $doc['code']));
            continue;
        }
        $dst = 'files/catalog/docs/' . strtolower($doc['filename']);
        copy($src, $dst);
        $md5 = md5_file($dst);
        $query = "INSERT INTO `temp_docs`
                  (
                      `title`,
                      `filename`,
                      `filetype`,
                      `md5`,
                      `uploaded`,
                      `code`
                  )
                  VALUES
                  (
                      :title,
                      :filename,
                      :filetype,
                      :md5,
                      NOW(),
                      :code
                  )";
        $register->database->execute(
            $query,
            array(
                'title' => $doc['title'],
                'filename' => strtolower($doc['filename']),
                'filetype' => $doc['filetype'],
                'md5' => $md5,
                'code' => $doc['code']
            )
        ); 
    }
    // теперь таблицы tmp_docs и tmp_docs содержат одинаковое количество записей
    $query = "SELECT * FROM `tmp_docs` WHERE 1";
    $docs = $register->database->fetchAll($query);
    foreach($docs as $doc) {
         $query = "UPDATE
                       `temp_docs`
                   SET
                       `title` = :title
                   WHERE
                       `code` = :code";
         $register->database->execute($query, array('title' => $doc['title'], 'code' => $doc['code'])); 
    }
    
    /*
     * СЕРТИФИКАТЫ
     */
    echo 'update temp table certs'. PHP_EOL;
    // удаляем те записи о сертификатах, которых уже нет в 1С
    $query = "SELECT * FROM `temp_certs` WHERE `code` NOT IN (SELECT `code` FROM `tmp_certs` WHERE 1)";
    $items = $register->database->fetchAll($query);
    foreach ($items as $item) {
        if (!is_file('files/catalog/cetr/'.$item['filename'])) {
            continue;
        }
        unlink('files/catalog/cert/'.$item['filename']);
        if ($item['count'] > 1) {
            $page = 1;
            while ($page < $item['count']) {
                $filename = str_replace('.jpg', $page.'.jpg', $item['filename']);
                if (is_file('files/catalog/cert/'.$filename) ) {
                    unlink('files/catalog/cert/'.$filename);
                }
                $page++;
            }
        }
    }
    $query = "DELETE FROM `temp_certs` WHERE `code` NOT IN (SELECT `code` FROM `tmp_certs` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_certs` WHERE `code` NOT IN (SELECT `code` FROM `temp_certs` WHERE 1)";
    $certs = $register->database->fetchAll($query);
    foreach ($certs as $cert) {
        $src = 'files/catalog/src/cert/'.$cert['filename'];
        if (is_file($src)) {
            $dst = 'files/catalog/cert/' . strtolower($cert['filename']);
            copy($src, $dst);
            if ($cert['count'] > 1) {
                $page = 1;
                while ($page < $cert['count']) {
                    $filename = str_replace('.jpg', $page.'.jpg', $cert['filename']);
                    $src = 'files/catalog/src/cert/'.$filename;
                    $dst = 'files/catalog/cert/' . strtolower($filename);
                    if (is_file($src)) {
                        copy($src, $dst);
                    } else {
                        file_put_contents('temp/errors.txt', 'Файл '.$filename.' сертификата '.$cert['code'].' не существует (№'.$page.')'.PHP_EOL, FILE_APPEND);
                    }
                    $page++;
                }
            }
            $query = "INSERT INTO `temp_certs`
                      (
                          `title`,
                          `filename`,
                          `count`,
                          `code`
                      )
                      VALUES
                      (
                          :title,
                          :filename,
                          :count,
                          :code
                      )";
            $register->database->execute(
                $query,
                array(
                    'title' => $cert['title'],
                    'filename' => strtolower($cert['filename']),
                    'count' => $cert['count'],
                    'code' => $cert['code']
                )
            );
        } else {
            $register->database->execute("DELETE FROM `tmp_certs` WHERE `code` = :code", array('code' => $cert['code']));
            $register->database->execute("DELETE FROM `tmp_cert_prod` WHERE `cert_code` = :code", array('code' => $cert['code']));
            file_put_contents('temp/errors.txt', 'Файл '.$cert['filename'].' сертификата '.$cert['code'].' не существует'.PHP_EOL, FILE_APPEND);
            continue;
        }
    }
    // теперь таблицы tmp_certs и tmp_certs содержат одинаковое количество записей
    $query = "SELECT * FROM `tmp_certs` WHERE 1";
    $certs = $register->database->fetchAll($query);
    foreach($certs as $cert) {
         $query = "UPDATE
                       `temp_certs`
                   SET
                       `title` = :title
                   WHERE
                       `code` = :code";
         $register->database->execute($query, array('title' => $cert['title'], 'code' => $cert['code'])); 
    }
    
    /*
     * ПРИВЯЗКА ФАЙЛОВ ДОКУМЕНТАЦИИ К ТОВАРАМ
     */
    echo 'update temp table doc-prd'. PHP_EOL;
    // удаляем записи, которых уже нет в 1С
    $query = "DELETE FROM `temp_doc_prd` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `tmp_doc_prd` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_doc_prd` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `temp_doc_prd` WHERE 1)";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        // уникальный идентификатор документа
        $query = "SELECT `id` FROM `temp_docs` WHERE `code` = :doc_code";
        $doc_id = $register->database->fetchOne($query, array('doc_code' => $row['doc_code']));
        if (false === $doc_id) {
            file_put_contents('temp/errors.txt', 'Попытка привязать не существующий документ '.$row['doc_code'].' к товару '.$row['prd_id'].PHP_EOL, FILE_APPEND);
            continue;
        }
        $query = "INSERT INTO `temp_doc_prd`
                  (
                      `prd_id`,
                      `doc_id`,
                      `concat_code`
                  )
                  VALUES
                  (
                      :prd_id,
                      :doc_id,
                      :concat_code
                  )";
        $register->database->execute(
            $query,
            array(
                'prd_id' => $row['prd_id'],
                'doc_id' => $doc_id,
                'concat_code' => $row['concat_code']
            )
        ); 
    }

    /*
     * ПРИВЯЗКА СЕРТИФИКАТОВ К ТОВАРАМ
     */
    echo 'update temp table cert-prod'. PHP_EOL;
    // удаляем записи, которых уже нет в 1С
    $query = "DELETE FROM `temp_cert_prod` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `tmp_cert_prod` WHERE 1)";
    $register->database->execute($query);
    // добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
    $query = "SELECT * FROM `tmp_cert_prod` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `temp_cert_prod` WHERE 1)";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        // уникальный идентификатор сертификата
        $query = "SELECT `id` FROM `temp_certs` WHERE `code` = :cert_code";
        $cert_id = $register->database->fetchOne($query, array('cert_code' => $row['cert_code']));
        if (false === $cert_id) {
            file_put_contents('temp/errors.txt', 'Попытка привязать не существующий сертификат '.$row['cert_code'].' к товару '.$row['prod_id'].PHP_EOL, FILE_APPEND);
            continue;
        }
        $query = "INSERT INTO `temp_cert_prod`
                  (
                      `prod_id`,
                      `cert_id`,
                      `concat_code`
                  )
                  VALUES
                  (
                      :prod_id,
                      :cert_id,
                      :concat_code
                  )";
        $register->database->execute(
            $query,
            array(
                'prod_id' => $row['prod_id'],
                'cert_id' => $cert_id,
                'concat_code' => $row['concat_code']
            )
        ); 
    }
}

function updateWorkTables($register) {

    echo 'UPDATE WORK TABLES'. PHP_EOL;
    
    $register->database->execute('TRUNCATE TABLE `categories`');
    $register->database->execute('TRUNCATE TABLE `products`');
    $register->database->execute('TRUNCATE TABLE `makers`');
    $register->database->execute('TRUNCATE TABLE `groups`');
    $register->database->execute('TRUNCATE TABLE `params`');
    $register->database->execute('TRUNCATE TABLE `values`');
    $register->database->execute('TRUNCATE TABLE `group_param_value`');
    $register->database->execute('TRUNCATE TABLE `product_param_value`');
    $register->database->execute('TRUNCATE TABLE `docs`');
    $register->database->execute('TRUNCATE TABLE `doc_prd`');
    $register->database->execute('TRUNCATE TABLE `certs`');
    $register->database->execute('TRUNCATE TABLE `cert_prod`');
    
    /*
     * КАТЕГОРИИ
     */
    echo 'update table categories'. PHP_EOL;
    $query = "SELECT * FROM `temp_categories` WHERE 1";
    $categories = $register->database->fetchAll($query);
    foreach($categories as $category) {
        unset($category['code']);
        $query = "INSERT INTO `categories`
                  (
                      `id`,
                      `parent`,
                      `name`,
                      `keywords`,
                      `description`,
                      `sortorder`,
                      `globalsort`
                  )
                  VALUES
                  (
                      :id,
                      :parent,
                      :name,
                      :keywords,
                      :description,
                      :sortorder,
                      :globalsort
                  )";
        $register->database->execute($query, $category);   
    }
    
    /*
     * ПРОИЗВОДИТЕЛИ
     */
    echo 'update table makers'. PHP_EOL;
    $query = "SELECT * FROM `temp_makers` WHERE 1";
    $makers = $register->database->fetchAll($query);
    foreach($makers as $maker) {
        unset($maker['code']);
        $query = "INSERT INTO `makers`
                  (
                      `id`,
                      `name`,
                      `altname`,
                      `keywords`,
                      `description`,
                      `body`
                  )
                  VALUES
                  (
                      :id,
                      :name,
                      :altname,
                      :keywords,
                      :description,
                      :body
                  )";
        $register->database->execute($query, $maker);   
    }
    
    /*
     * ФУНКЦИОНАЛЬНЫЕ ГРУППЫ
     */
    echo 'update table groups'. PHP_EOL;
    $query = "SELECT * FROM `temp_groups` WHERE 1";
    $groups = $register->database->fetchAll($query);
    foreach($groups as $group) {
        unset($group['code']);
        $query = "INSERT INTO `groups`
                  (
                      `id`,
                      `name`
                  )
                  VALUES
                  (
                      :id,
                      :name
                  )";
        $register->database->execute($query, $group);   
    }
    
    /*
     * ПАРАМЕТРЫ ПОДБОРА
     */
    echo 'update table params'. PHP_EOL;
    $query = "SELECT * FROM `temp_params` WHERE 1";
    $params = $register->database->fetchAll($query);
    foreach($params as $param) {
        unset($param['code']);
        $query = "INSERT INTO `params`
                  (
                      `id`,
                      `name`
                  )
                  VALUES
                  (
                      :id,
                      :name
                  )";
        $register->database->execute($query, $param);   
    }
    
    /*
     * ЗНАЧЕНИЯ ПАРАМЕТРОВ ПОДБОРА
     */
    echo 'update table values'. PHP_EOL;
    $query = "SELECT * FROM `temp_values` WHERE 1";
    $values = $register->database->fetchAll($query);
    foreach($values as $value) {
        unset($value['code']);
        $query = "INSERT INTO `values`
                  (
                      `id`,
                      `name`
                  )
                  VALUES
                  (
                      :id,
                      :name
                  )";
        $register->database->execute($query, $value);   
    }
    
    /*
     * ТОВАРЫ
     */
    echo 'update table products'. PHP_EOL;
    $query = "SELECT * FROM `temp_products` WHERE 1";
    $products = $register->database->fetchAll($query);
    foreach ($products as $product) {
        $query = "INSERT INTO `products`
                  (
                      `id`,
                      `category`,
                      `category2`,
                      `group`,
                      `maker`,
                      `hit`,
                      `new`,
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
                      `image`,
                      `sortorder`,
                      `updated`,
                      `visible`
                  )
                  VALUES
                  (
                      :id,
                      :category,
                      :category2,
                      :group,
                      :maker,
                      :hit,
                      :new,
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
                      :image,
                      :sortorder,
                      :updated,
                      :visible
                  )";
        $register->database->execute($query, $product); 
    }

    /*
     * ПРИВЯЗКА ПАРАМЕТРОВ И ДОПУСТИМЫХ ЗНАЧЕНИЙ К ГРУППЕ
     */
    echo 'update table group-param-value'. PHP_EOL;
    $query = "SELECT * FROM `temp_group_param_value` WHERE 1";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        unset($row['concat_code']);
        $query = "INSERT INTO `group_param_value`
                  (
                      `group_id`,
                      `param_id`,
                      `value_id`
                  )
                  VALUES
                  (
                      :group_id,
                      :param_id,
                      :value_id
                  )";
        $register->database->execute($query, $row);
    }
    
    /*
     * ПРИВЯЗКА ПАРАМЕТРОВ И ЗНАЧЕНИЙ К ТОВАРУ
     */
    echo 'update table product-param-value'. PHP_EOL;
    $query = "SELECT * FROM `temp_product_param_value` WHERE 1";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        unset($row['concat_code']);
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
        $register->database->execute($query, $row);
    }
    
    /*
     * ФАЙЛЫ ДОКУМЕНТАЦИИ
     */
    echo 'update table docs'. PHP_EOL;
    $query = "SELECT * FROM `temp_docs` WHERE 1";
    $docs = $register->database->fetchAll($query);
    foreach ($docs as $doc) {
        unset($doc['code']);
        $query = "INSERT INTO `docs`
                  (
                      `id`,
                      `title`,
                      `filename`,
                      `filetype`,
                      `md5`,
                      `uploaded`
                  )
                  VALUES
                  (
                      :id,
                      :title,
                      :filename,
                      :filetype,
                      :md5,
                      :uploaded
                  )";
        $register->database->execute($query, $doc); 
    }
    
    /*
     * ПРИВЯЗКА ФАЙЛОВ ДОКУМЕНТАЦИИ К ТОВАРАМ
     */
    echo 'update table doc-prd'. PHP_EOL;
    $query = "SELECT * FROM `temp_doc_prd` WHERE 1";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        unset($row['concat_code']);
        $query = "INSERT INTO `doc_prd`
                  (
                      `prd_id`,
                      `doc_id`
                  )
                  VALUES
                  (
                      :prd_id,
                      :doc_id
                  )";
        $register->database->execute($query, $row); 
    }
    
    /*
     * СЕРТИФИКАТЫ
     */
    echo 'update table certs'. PHP_EOL;
    $query = "SELECT * FROM `temp_certs` WHERE 1";
    $certs = $register->database->fetchAll($query);
    foreach ($certs as $cert) {
        unset($cert['code']);
        $query = "INSERT INTO `certs`
                  (
                      `id`,
                      `title`,
                      `filename`,
                      `count`
                  )
                  VALUES
                  (
                      :id,
                      :title,
                      :filename,
                      :count
                  )";
        $register->database->execute($query, $cert); 
    }
    
    /*
     * ПРИВЯЗКА СЕРТИФИКАТОВ К ТОВАРАМ
     */
    echo 'update table cert-prod'. PHP_EOL;
    $query = "SELECT * FROM `temp_cert_prod` WHERE 1";
    $rows = $register->database->fetchAll($query);
    foreach ($rows as $row) {
        unset($row['concat_code']);
        $query = "INSERT INTO `cert_prod`
                  (
                      `prod_id`,
                      `cert_id`
                  )
                  VALUES
                  (
                      :prod_id,
                      :cert_id
                  )";
        $register->database->execute($query, $row); 
    }

}

function checkImages($register) {
    $query = "SELECT `id`, `code`, `image` FROM `temp_products` WHERE `image` <> ''";
    $items = $register->database->fetchAll($query);
    foreach ($items as $item) {
        if (is_file('files/catalog/imgs/big/' . $item['image'])) {
            continue;
        }
        file_put_contents('temp/errors.txt', 'Файл '.$item['image'].' для товара '.$item['code'] . ' не существует' . PHP_EOL, FILE_APPEND);
        $query = "UPDATE `temp_products` SET `image` = '' WHERE `id` = :id";
        $register->database->execute($query, array('id' => $item['id']));
    }
}

function updateMeta($register) {
    $query = "SELECT `a`.`id` AS `id`, `a`.`category` AS `category`, `a`.`name` AS `name`, `a`.`title` AS `title`, `b`.`name` AS `maker` FROM `temp_products` `a` INNER JOIN `temp_makers` `b` ON `a`.`maker`=`b`.`id` WHERE `a`.`keywords` = ''";
    $products = $register->database->fetchAll($query);
    foreach ($products as $product) {
        $root = getRootCategory($product['category'], $register);
        $product['name'] = str_replace('"', '', $product['name']);
        $product['title'] = str_replace('"', '', $product['title']);
        $product['maker'] = str_replace('"', '', $product['maker']);
        $description = $product['name'];
        if (!empty($product['title'])) $description = $description.'. '.$product['title'];
        $description = $description.'. '.$root.'.';
        if (strlen($description) < 188) $description = $description.' Каталог оборудования систем безопасности. Торговый Дом ТИНКО.';
        $keywords = $product['name'];
        if (!empty($product['title'])) $keywords = $keywords.' '.lcfirst($prd['title']));
        $keywords = $keywords.' '.$product['maker'];
        $keywords = $keywords.' цена купить';
        $keywords = $keywords.' '.lcfirst($root));
        if (strlen($keywords) < 200) $keywords = $keywords.' каталог оборудование системы безопасности ТД ТИНКО';
        $keywords = str_replace('«', ' ', $keywords);
        $keywords = str_replace('»', ' ', $keywords);
        $keywords = str_replace('(', ' ', $keywords);
        $keywords = str_replace(')', ' ', $keywords);
        $keywords = preg_replace('~\s+~', ' ', $keywords);
        $query = "UPDATE `temp_products` SET `keywords` = :keywords, `description` = :description WHERE `id` = :id";
        $register->database->execute($query, array('id' => $product['id']));
    }
}

function getRootCategory($id, $register) {
    
}

function updateSortOrderAllCategories($id, $sortorder, $level) {
    $register = Register::getInstance();
    // начало и конец строки, задающей сортировку
    $before = substr($sortorder, 0, $level * 2);
    $after = str_repeat('0', 18 - $level * 2);
    // получаем массив дочерних категорий
    $query = "SELECT `id` FROM `temp_categories` WHERE `parent` = ".$id." ORDER BY `sortorder`";
    $childs = $register->database->fetchAll($query);
    $i = 1;
    foreach($childs as $child) {
        $globalsort = $i;
        if (strlen($globalsort) == 1) {
            $globalsort = '0' . $globalsort;
        }
        $globalsort = $before . $globalsort . $after;
        $query = "UPDATE `temp_categories` SET `globalsort` = '".$globalsort."' WHERE `id` = ".$child['id'];
        // echo $query . PHP_EOL;
        $register->database->execute($query);
        // рекурсивно вызываем updateSortOrderAllCategories()
        updateSortOrderAllCategories($child['id'], $globalsort, $level + 1);
        $i++;
    }
}

/**
 * Функция для изменения размеров изображения
 * Параметры:
 * $src - имя исходного файла
 * $dst - имя генерируемого файла
 * $width, $height - ширина и высота генерируемого изображения, в пикселях
 * Необязательные параметры:
 * $res - формат выходного файла (jpg, gif, png), по умолчанию - формат входного файла
 * $rgb - цвет фона, по умолчанию - белый
 */
function resizeImage($src, $dst, $width, $height, $res = '', $rgb = array(255,255,255)) {
    if ( ! in_array($res, array('', 'jpg', 'jpeg', 'gif', 'png'))) return false;
    if ('jpg' == $res) $res = 'jpeg';

    if ( ! file_exists($src)) return false;
    $size = getimagesize($src);
    if (false === $size) return false;

    // определяем исходный формат по MIME-информации, предоставленной функцией
    // getimagesize, и выбираем соответствующую формату imagecreatefrom-функцию
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    // если ширина и высота изображения уже имеют нужное значение
    if ($size[0] == $width && $size[1] == $height) {
        if (empty($res) || $res == $format) {
            copy($src, $dst);
            return true;
        }
    }
    $func = "imagecreatefrom" . $format;
    if ( ! function_exists($func)) return false;

    if (0 == $height) {
        $height = floor(($size[1]/$size[0])*$width);
    }

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width = $use_x_ratio ? $width : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
    $new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    // читаем в память файл изображения с помощью функции imagecreatefrom...
    $isrc = $func($src);
    if (!is_resource($isrc)) {
        return false;
    }
    // создаем новое изображение
    $idst = imagecreatetruecolor($width, $height);

    // заливка цветом фона
    if($format == 'png') { // прозрачность для png-изображений
        imagesavealpha($idst, true); // сохранение альфа канала
        $background = imagecolorallocatealpha($idst, $rgb[0], $rgb[1], $rgb[2], 127); // 127 - полная прозрачность
    } else {
        $background = imagecolorallocate($idst, $rgb[0], $rgb[1], $rgb[2]);
    }
    imagefill($idst, 0, 0, $background);

    // копируем существующее изображение в новое с изменением размера
    $resize = imagecopyresampled(
        $idst, // идентификатор нового изображения
        $isrc, // идентификатор исходного изображения
        $new_left, $new_top, // координаты (x,y) верхнего левого угла в новом изображении
        0, 0, // координаты (x,y) верхнего левого угла копируемого блока существующего изображения
        $new_width, // новая ширина копируемого блока
        $new_height, // новая высота копируемого блока
        $size[0], // ширина исходного копируемого блока
        $size[1] // высота исходного копируемого блока
    );

    // сохраняем результат
    if (empty($res)) $res = $format;
    $func = 'image' . $res;
    if ( ! function_exists($func)) return false;

    $func($idst, $dst);

    imagedestroy($isrc);
    imagedestroy($idst);

    return true;
}