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
// сохраняем в реестре настройки, чтобы везде иметь к ним доступ; доступ к
// настройкам возможен через реестр или напрямую через Config::getInstance()
$register->config = Config::getInstance();
// кэширование данных
$register->cache = Cache::getInstance();
// база данных
$register->database = Database::getInstance();

parseXML($register);

function parseXML($register) {
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
    
    $register->database->execute('TRUNCATE TABLE `temp_docs`');
    $register->database->execute('TRUNCATE TABLE `temp_doc_prd`');
    $register->database->execute('TRUNCATE TABLE `temp_certs`');
    $register->database->execute('TRUNCATE TABLE `temp_cert_prod`');
    $register->database->execute('TRUNCATE TABLE `temp_related`');

    $reader = new XMLReader();
    $reader->open('temp/example.xml');
    $item = array();
    while ($reader->read()) {
        // КАТЕГОРИИ
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'categories' && !$reader->isEmptyElement) {
            // проходим в цикле все дочерние элементы элемента <categories>
            while ($reader->read()) {
                // отдельный элемент <category>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'category') {
                    $data = array();
                    $data['code'] = $reader->getAttribute('id');
                    echo 'category code=' . $data['code'] . PHP_EOL;
                    $data['parent'] = $reader->getAttribute('parent');
                    if ($data['parent'] == '00000000-0000-0000-0000-000000000000') {
                        $data['parent'] = '';
                    }
                    $data['sortorder'] = (int)$reader->getAttribute('sortorder');
                    // читаем дальше для получения текстового элемента
                    $reader->read();
                    $data['name'] = $reader->value;
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
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'categories') {
                    break;
                }
            }
        }

        // ПРОИЗВОДИТЕЛИ
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'makers' && !$reader->isEmptyElement) { // элемент <makers>
            // проходим в цикле все дочерние элементы элемента <makers>
            while ($reader->read()) {
                // отдельный элемент <maker>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'maker') {
                    $data = array();
                    $data['code'] = $reader->getAttribute('id');
                    echo 'maker code=' . $data['code'] . PHP_EOL;
                    // читаем дальше для получения текстового элемента
                    $reader->read();
                    $data['name'] = $reader->value;
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
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'makers') {
                    break;
                }
            }
        }
        
        // ПАРАМЕТРЫ
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'params' && !$reader->isEmptyElement) { // элемент <params>
            // читаем дальше для получения элемента <names>
            $reader->read();
            // проходим в цикле все дочерние элементы элемента <names>
            while ($reader->read()) {
                // отдельный элемент <name>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                    $data = array();
                    $data['code'] = $reader->getAttribute('id');
                    echo 'params name code=' . $data['code'] . PHP_EOL;
                    // читаем дальше для получения текстового элемента
                    $reader->read();
                    $data['name'] = $reader->value;
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
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'names') {
                    break;
                }
            }
            // читаем дальше для получения элемента <values>
            $reader->read();
            // проходим в цикле все дочерние элементы элемента <values>
            while ($reader->read()) {
                // отдельный элемент <value>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'value') {
                    $data = array();
                    $data['code'] = $reader->getAttribute('id');
                    echo 'params value code=' . $data['code'] . PHP_EOL;
                    // читаем дальше для получения текстового элемента
                    $reader->read();
                    $data['name'] = $reader->value;
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
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'values') {
                    break;
                }
            }
        }

        // ФУНКЦИОНАЛЬНЫЕ ГРУППЫ
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'groups' && !$reader->isEmptyElement) { // элемент <groups>
            // проходим в цикле все дочерние элементы элемента <groups>
            while ($reader->read()) {
                // отдельный элемент <group>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'group' && !$reader->isEmptyElement) {
                    // атрибут элемента <group>
                    $group_code = $reader->getAttribute('id');
                    echo 'group code=' . $group_code . PHP_EOL;
                    // дочерние элементы элемента <group>
                    while ($reader->read()) {
                        // наименование группы
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                            $data = array();
                            $data['code'] = $group_code;
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['name'] = $reader->value;
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
                        }
                        // информация о параметрах подбора для группы
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'params' && !$reader->isEmptyElement) {
                            // дочерние элементы элемента <params>
                            while ($reader->read()) {
                                // отдельный элемент <param>
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'param') {
                                    // атрибуты элемента <param>
                                    $data = array();
                                    $data['group_code'] = $group_code;
                                    $data['param_code'] = $reader->getAttribute('name');
                                    $data['value_code'] = $reader->getAttribute('value');
                                    $data['concat_code'] = md5($data['group_code'].$data['param_code'].$data['value_code']);
                                    echo 'group=' . $data['group_code'] . ' param=' . $data['param_code'] . ' value=' . $data['value_code'] . PHP_EOL;
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
                                      $register->database->execute($query, $data);
                                }
                                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'params') {
                                    break;
                                }
                            }
                        }
                        if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'group') {
                            break;
                        }
                    }
                }
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'groups') {
                    break;
                }
            }
        }
        
        // ФАЙЛЫ ДОКУМЕНТАЦИИ
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'docs' && !$reader->isEmptyElement) { // элемент <docs>
            // проходим в цикле все дочерние элементы элемента <docs>
            while ($reader->read()) {
                // отдельный элемент <doc>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'doc' && !$reader->isEmptyElement) {
                    // атрибуты элемента <doc>
                    $code = $reader->getAttribute('id');
                    echo 'doc code=' . $code . PHP_EOL;
                    // дочерние элементы элемента <doc>
                    while ($reader->read()) {
                        // наименование документа
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'title') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $title = $reader->value;
                        }
                        // имя файла
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'file') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $file = $reader->value;
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                        }
                        // сумма md5 файла
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'md5') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $md5 = $reader->value;
                        }
                        if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'doc') {
                            break;
                        }
                    }
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
                                  :md5,
                                  NOW()
                              )";
                    $data = array(
                        'code' => $code,
                        'title' => $title,
                        'filename' => $file,
                        'filetype' => $ext,
                        'md5' => $md5
                    );
                    $register->database->execute($query, $data);
                }
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'docs') {
                    break;
                }
            }
        }

        // СЕРТИФИКАТЫ
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'certs' && !$reader->isEmptyElement) { // элемент <certs>
            // проходим в цикле все дочерние элементы элемента <certs>
            while ($reader->read()) {
                // отдельный элемент <cert>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'cert' && !$reader->isEmptyElement) {
                    // атрибуты элемента <cert>
                    $code = $reader->getAttribute('id');
                    echo 'cert code=' . $code . PHP_EOL;
                    // дочерние элементы элемента <cert>
                    while ($reader->read()) {
                        // наименование сертификата
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'title') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $title = $reader->value;
                        }
                        // информация о файлах
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'files') {
                            // дочерние элементы элемента <files>
                            while ($reader->read()) {
                                // имя файла
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                                    // читаем дальше для получения текстового элемента
                                    $reader->read();
                                    $name = $reader->value;
                                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                                }
                                // количество страниц
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'count') {
                                    // читаем дальше для получения текстового элемента
                                    $reader->read();
                                    $count = (int)$reader->value;
                                }
                                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'files') {
                                    break;
                                }
                            }
                        }
                        if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'cert') {
                            break;
                        }
                    }
                    // TODO: вылезает некорректное значение $count
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
                    $data = array(
                        'code' => $code,
                        'title' => $title,
                        'filename' => $file,
                        'count' => $count
                    );
                    $register->database->execute($query, $data);
                }
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'certs') {
                    break;
                }
            }
        }

        // ТОВАРЫ
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'products') { // элемент <products>
            // проходим в цикле все дочерние элементы элемента <products>
            while ($reader->read()) {
                // отдельный элемент <product>
                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'product') {
                    $data = array();
                    $data['code'] = $reader->getAttribute('code');
                    $data['id'] = (int)$data['code'];
                    echo 'product id=' . $data['id'] . PHP_EOL;
                    
                    $parents = explode(',', $reader->getAttribute('category'));
                    $data['category'] = $parents[0];
                    $data['category2'] = '';
                    if (isset($parents[1])) {
                        $data['category2'] = $parents[1];
                    }
                    $data['group'] = $reader->getAttribute('group');
                    $data['maker'] = $reader->getAttribute('maker');
                    $data['hit'] = (int)$reader->getAttribute('hit');
                    $data['new'] = (int)$reader->getAttribute('new');
                    $data['sortorder'] = (int)$reader->getAttribute('sortorder');
                    // проходим все дочерние элементы элемента <product>
                    while ($reader->read()) {
                        // торговое наименование
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['name'] = trim($reader->value);
                        }
                        // функциональное наименование
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'title') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['title'] = trim($reader->value);
                        }
                        // цена
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'price') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['price'] = (float)trim($reader->value);
                        }
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'price2') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['price2'] = (float)trim($reader->value);
                        }
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'price3') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['price3'] = (float)trim($reader->value);
                        }
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'price4') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['price4'] = (float)trim($reader->value);
                        }
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'price5') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['price5'] = (float)trim($reader->value);
                        }
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'price6') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['price6'] = (float)trim($reader->value);
                        }
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'price7') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['price7'] = (float)trim($reader->value);
                        }
                        // единица измерения
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'unit') {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['unit'] = (int)trim($reader->value);
                            $data['unit'] = 0;
                        }
                        // краткое описание
                        $data['shortdescr'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'shortdescr' && !$reader->isEmptyElement) {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['shortdescr'] = trim($reader->value);
                        }
                        // назначение
                        $data['purpose'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'purpose' && !$reader->isEmptyElement) {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['purpose'] = $reader->value;
                        }
                        // технические характеристики
                        $techdata = array();
                        $data['techdata'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'techdata' && !$reader->isEmptyElement) {
                            // проходим в цикле все дочерние элементы элемента <techdata>
                            $name = array();
                            $value = array();
                            while ($reader->read()) {
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'item' && !$reader->isEmptyElement) {
                                    // проходим в цикле все дочерние элементы элемента <item>
                                    while ($reader->read()) {
                                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                                            $reader->read();
                                            $name[] = trim($reader->value);
                                        }
                                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'value') {
                                            $reader->read();
                                            $value[] = $reader->value;
                                        }
                                        if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'item') {
                                            break;
                                        }
                                    }
                                }
                                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'techdata') {
                                    break;
                                }
                            }
                            foreach ($name as $k => $v) {
                                $techdata[] = array($v, $value[$k]);
                            }
                            if (!empty($techdata)) {
                                $data['techdata'] = serialize($techdata);
                            }
                        }

                        // особенности
                        $data['features'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'features' && !$reader->isEmptyElement) {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['features'] = trim($reader->value);
                        }
                        // комплектация
                        $data['complect'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'complect' && !$reader->isEmptyElement) {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['complect'] = trim($reader->value);
                        }
                        // доп.оборудование
                        $data['equipment'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'equipment' && !$reader->isEmptyElement) {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['equipment'] = trim($reader->value);
                        }
                        // доп.информация
                        $data['padding'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'padding' && !$reader->isEmptyElement) {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['padding'] = trim($reader->value);
                        }
                        // фото
                        $data['image'] = '';
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'image' && !$reader->isEmptyElement) {
                            // читаем дальше для получения текстового элемента
                            $reader->read();
                            $data['image'] = trim($reader->value);
                        }
                        // параметры подбора
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'params' && !$reader->isEmptyElement) {
                            // проходим в цикле все дочерние элементы элемента <params>
                            $params = array();
                            while ($reader->read()) {
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'param') {
                                    // атрибуты элемента <param>
                                    $name = $reader->getAttribute('name');
                                    $value = $reader->getAttribute('value');
                                    $params[] = array($name, $value);
                                }
                                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'params') {
                                    break;
                                }
                            }
                            foreach ($params as $value) {
                                // TODO: возникают дубли
                                $query = "INSERT IGNORE INTO `tmp_product_param_value`
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
                                        'param_code' => $value[0],
                                        'value_code' => $value[1],
                                        'concat_code' => md5($data['id'].$value[0].$value[1])
                                    )
                                );
                            }
                        }
                        
                        // файлы документации
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'docs' && !$reader->isEmptyElement) {
                            // проходим в цикле все дочерние элементы элемента <docs>
                            $doc_codes = array();
                            while ($reader->read()) {
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'doc') {
                                    // атрибуты элемента <doc>
                                    $doc_codes[] = $reader->getAttribute('id');
                                }
                                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'docs') {
                                    break;
                                }
                            }
                            foreach ($doc_codes as $doc_code) {
                                // TODO: возникают дубли
                                $query = "INSERT IGNORE INTO `tmp_doc_prd`
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
                                        'doc_code' => $doc_code,
                                        'concat_code' => md5($data['id'] . $doc_code)
                                    )
                                );
                            }
                        }
                        // сертификаты
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'certs' && !$reader->isEmptyElement) {
                            // проходим в цикле все дочерние элементы элемента <certs>
                            $cert_codes = array();
                            while ($reader->read()) {
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'cert') {
                                    // атрибуты элемента <cert>
                                    $cert_codes[] = $reader->getAttribute('id');
                                }
                                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'certs') {
                                    break;
                                }
                            }
                            foreach ($cert_codes as $cert_code) {
                                $query = "INSERT IGNORE INTO `tmp_cert_prod`
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
                                        'cert_code' => $cert_code,
                                        'concat_code' => md5($data['id'] . $cert_code)
                                    )
                                );
                            }
                        }
                        // связанные товары
                        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'linked' && !$reader->isEmptyElement) {
                            // проходим в цикле все дочерние элементы элемента <docs>
                            $rel_ids_cnts = array();
                            while ($reader->read()) {
                                if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'prd') {
                                    // атрибуты элемента <prd>
                                    $id = (int)$reader->getAttribute('code');
                                    $count = (int)$reader->getAttribute('count');
                                    $rel_ids_cnts[] = array($id, $count);
                                }
                                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'linked') {
                                    break;
                                }
                            }
                            foreach ($rel_ids_cnts as $item) {
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
                                    array('id1' => $data['id'], 'id2' => $item[0], 'count' => $item[1])
                                );
                            }
                        }

                        if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'product') {
                            break;
                        }
                    }
                    $data['keywords'] = '';
                    $data['description'] = '';
                    // TODO: возникают дубли
                    $query = "INSERT IGNORE INTO `tmp_products`
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
                if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'products') {
                    break;
                }
            }
        }

    }
    $reader->close();
}

die();

/*
 * Из первичных промежуточных таблиц во вторичные промежуточные таблицы
 */

// КАТЕГОРИИ
echo 'TABLE->TABLE->CATEGORIES'. PHP_EOL;
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
// теперь таблицы tmp_categories и temp_categories содержат одинаковое количество записей
$query = "SELECT * FROM `tmp_categories` WHERE 1";
$categories = $register->database->fetchAll($query);
foreach($categories as $category) {
    // получаем идентификатор родителя текущей категории (целое положительное число)
    $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :parent_code";
    $parent_id = 0; // для корневой категории запрос вернет false
    $temp = $register->database->fetchOne($query, array('parent_code' => $category['parent']));
    if (false !== $temp) {
        $parent_id = $temp;
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
    $data['parent'] = $parent_id;
    // $data['name'] = trim($category['name']);
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
    // echo $query . PHP_EOL;
    $register->database->execute($query);
    updateSortOrderAllCategories($root['id'], $sort . '000000000000000000', 1);
    $i++;
}

// ПРОИЗВОДИТЕЛИ
echo 'TABLE->TABLE->MAKERS'. PHP_EOL;
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
                  '',
                  '',
                  '',
                  '',
                  :code
              )";
    $register->database->execute($query, array('name' => trim($maker['name']), 'code' => $maker['code'])); 
}
// теперь таблицы tmp_makers и temp_makers содержат одинаковое количество записей
$query = "SELECT * FROM `tmp_makers` WHERE 1";
$makers = $register->database->fetchAll($query);
foreach($makers as $maker) {
    $query = "UPDATE
                  `temp_makers`
              SET
                  `name` = :name
              WHERE
                  `code` = :code";
    // $maker['name'] = trim($maker['name']);
    $register->database->execute($query, array('name' => $maker['name'], 'code' => $maker['code'])); 
}

// ФУНКЦИОНАЛЬНЫЕ ГРУППЫ
echo 'TABLE->TABLE->GROUPS'. PHP_EOL;
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
    // $group['name'] = trim($group['name']);
    $register->database->execute($query, array('name' => $group['name'], 'code' => $group['code'])); 
}
// теперь таблицы tmp_groups и temp_groups содержат одинаковое количество записей
$query = "SELECT * FROM `tmp_groups` WHERE 1";
$groups = $register->database->fetchAll($query);
foreach($groups as $group) {
     $query = "UPDATE
                   `temp_groups`
               SET
                   `name` = :name
               WHERE
                   `code` = :code";
     // $group['name'] = trim($group['name']);
     $register->database->execute($query, array('name' => $group['name'], 'code' => $group['code'])); 
}

// ТОВАРЫ
echo 'TABLE->TABLE->PRODUCTS'. PHP_EOL;
// удаляем те товары, которых уже нет в 1С
$query = "DELETE FROM `temp_products` WHERE `id` NOT IN (SELECT `id` FROM `tmp_products` WHERE 1)";
$register->database->execute($query);
// добавляем новые товары: которые уже есть в 1С, но еще нет на сайте
$query = "SELECT * FROM `tmp_products` WHERE `id` NOT IN (SELECT `id` FROM `temp_products` WHERE 1)";
$products = $register->database->fetchAll($query);
foreach ($products as $product) {
    $data = array();
    $data['id'] = $product['id'];
    // уникальный идентификатор родительской категории (целое положительное число)
    $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :code";
    $data['category'] = $register->database->fetchOne($query, array('code' => $product['category']));
    // уникальный идентификатор дополнительной категории (целое положительное число)
    $data['category2'] = 0;
    $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :code";
    $temp = $register->database->fetchOne($query, array('code' => $product['category2']));
    if (false !== $temp) {
        $data['category2'] = $temp;
    }
    // уникальный идентификатор функциональной группы (целое положительное число)
    $data['group'] = 0;
    $query = "SELECT `id` FROM `temp_groups` WHERE `code` = :code";
    $temp = $register->database->fetchOne($query, array('code' => $product['group']));
    if (false !== $temp) {
        $data['group'] = $temp;
    }
    // уникальный идентификатор производителя (целое положительное число)
    $query = "SELECT `id` FROM `temp_makers` WHERE `code` = :code";
    $data['maker'] = $register->database->fetchOne($query, array('code' => $product['maker']));
    /*    
    $data['hit'] = $product['hit'];
    $data['new'] = $product['new'];
    $data['code'] = $product['code'];
    $data['name'] = trim($product['name']);
    $data['title'] = trim($product['title']);
    $data['keywords'] = trim($product['keywords']);
    $data['description'] = trim($product['description']);
    $data['shortdescr'] = trim($product['shortdescr']);
    $data['purpose'] = trim($product['purpose']);
    $data['techdata'] = trim($product['techdata']);
    $data['features'] = trim($product['features']);
    $data['complect'] = trim($product['complect']);
    $data['equipment'] = trim($product['equipment']);
    $data['padding'] = trim($product['padding']);
    $data['price'] = $product['price'];
    $data['price2'] = $product['price2'];
    $data['price3'] = $product['price3'];
    $data['price4'] = $product['price4'];
    $data['price5'] = $product['price5'];
    $data['price6'] = $product['price6'];
    $data['price7'] = $product['price7'];
    $data['unit'] = $product['unit'];
    $data['image'] = $product['image'];
    $data['sortorder'] = $product['sortorder'];
    */
    $data['hit'] = $product['hit'];
    $data['new'] = $product['new'];
    $data['code'] = $product['code'];
    $data['name'] = trim($product['name']);
    $data['title'] = trim($product['title']);
    $data['keywords'] = trim($product['keywords']);
    $data['description'] = trim($product['description']);
    $data['shortdescr'] = trim($product['shortdescr']);
    $data['purpose'] = trim($product['purpose']);
    $data['techdata'] = trim($product['techdata']);
    $data['features'] = trim($product['features']);
    $data['complect'] = trim($product['complect']);
    $data['equipment'] = trim($product['equipment']);
    $data['padding'] = trim($product['padding']);
    $data['price'] = $product['price'];
    $data['price2'] = $product['price2'];
    $data['price3'] = $product['price3'];
    $data['price4'] = $product['price4'];
    $data['price5'] = $product['price5'];
    $data['price6'] = $product['price6'];
    $data['price7'] = $product['price7'];
    $data['unit'] = $product['unit'];
    $data['image'] = $product['image'];
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
// теперь таблицы tmp_products и temp_products содержат одинаковое количество записей
$query = "SELECT * FROM `tmp_products` WHERE 1";
$products = $register->database->fetchAll($query);
foreach($products as $product) {
    $data = array();
    $data['id'] = $product['id'];
    // уникальный идентификатор родительской категории (целое положительное число)
    $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :code";
    $data['category'] = $register->database->fetchOne($query, array('code' => $product['category']));
    // TODO: такого быть не должно
    if (false === $data['category']) {
        continue;
    }
    // уникальный идентификатор дополнительной категории (целое положительное число)
    $data['category2'] = 0;
    $query = "SELECT `id` FROM `temp_categories` WHERE `code` = :code";
    $temp = $register->database->fetchOne($query, array('code' => $product['category2']));
    if (false !== $temp) {
        $data['category2'] = $temp;
    }
    // уникальный идентификатор функциональной группы (целое положительное число)
    $data['group'] = 0;
    $query = "SELECT `id` FROM `temp_groups` WHERE `code` = :code";
    $temp = $register->database->fetchOne($query, array('code' => $product['group']));
    if (false !== $temp) {
        $data['group'] = $temp;
    }
    // уникальный идентификатор производителя (целое положительное число)
    $query = "SELECT `id` FROM `temp_makers` WHERE `code` = :code";
    $data['maker'] = $register->database->fetchOne($query, array('code' => $product['maker']));
    
    $data['hit'] = $product['hit'];
    $data['new'] = $product['new'];
    $data['code'] = $product['code'];
    $data['name'] = trim($product['name']);
    $data['title'] = trim($product['title']);
    $data['shortdescr'] = trim($product['shortdescr']);
    $data['purpose'] = trim($product['purpose']);
    $data['techdata'] = trim($product['techdata']);
    $data['features'] = trim($product['features']);
    $data['complect'] = trim($product['complect']);
    $data['equipment'] = trim($product['equipment']);
    $data['padding'] = trim($product['padding']);
    $data['price'] = $product['price'];
    $data['price2'] = $product['price2'];
    $data['price3'] = $product['price3'];
    $data['price4'] = $product['price4'];
    $data['price5'] = $product['price5'];
    $data['price6'] = $product['price6'];
    $data['price7'] = $product['price7'];
    $data['unit'] = $product['unit'];
    $data['image'] = $product['image'];
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
                  `image` = :image,
                  `sortorder` = :sortorder
              WHERE
                  `id` = :id";
    $register->database->execute($query, $data); 
}

// ПАРАМЕТРЫ ПОДБОРА
echo 'TABLE->TABLE->PARAMS'. PHP_EOL;
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
    // $param['name'] = trim($param['name']);
    $register->database->execute($query, array('name' => $param['name'], 'code' => $param['code'])); 
}
// теперь таблицы tmp_params и temp_params содержат одинаковое количество записей
$query = "SELECT * FROM `tmp_params` WHERE 1";
$params = $register->database->fetchAll($query);
foreach($params as $param) {
     $query = "UPDATE `temp_params` SET
                  `name` = :name
              WHERE
                  `code` = :code";
     // $param['name'] = trim($param['name']);
     $register->database->execute($query, array('name' => $param['name'], 'code' => $param['code'])); 
}

// ЗНАЧЕНИЯ ПАРАМЕТРОВ ПОДБОРА
echo 'TABLE->TABLE->VALUES'. PHP_EOL;
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
    // $value['name'] = trim($value['name']);
    $register->database->execute($query, array('name' => $value['name'], 'code' => $value['code'])); 
}
// теперь таблицы tmp_values и temp_values содержат одинаковое количество записей
$query = "SELECT * FROM `tmp_values` WHERE 1";
$values = $register->database->fetchAll($query);
foreach($values as $value) {
     $query = "UPDATE `temp_values` SET
                  `name` = :name
              WHERE
                  `code` = :code";
     // $value['name'] = trim($value['name']);
     $register->database->execute($query, array('name' => $value['name'], 'code' => $value['code'])); 
}

// ПРИВЯЗКА ПАРАМЕТРОВ И ДОПУСТИМЫХ ЗНАЧЕНИЙ К ГРУППЕ
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
        continue;
    }
    // уникальный идентификатор параметра (целое положительное число)
    $query = "SELECT `id` FROM `temp_params` WHERE `code` = :param_code";
    $param_id = $register->database->fetchOne($query, array('param_code' => $row['param_code']));
    // TODO
    if (false === $param_id) {
        continue;
    }
    // уникальный идентификатор значения параметра (целое положительное число)
    $query = "SELECT `id` FROM `temp_values` WHERE `code` = :value_code";
    $value_id = $register->database->fetchOne($query, array('value_code' => $row['value_code']));
    // TODO
    if (false === $value_id) {
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

// ПРИВЯЗКА ПАРАМЕТРОВ И ЗНАЧЕНИЙ К ТОВАРУ
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
    // уникальный идентификатор значения параметра (целое положительное число)
    $query = "SELECT `id` FROM `temp_values` WHERE `code` = :value_code";
    $value_id = $register->database->fetchOne($query, array('value_code' => $row['value_code']));
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

// ФАЙЛЫ ДОКУМЕНТАЦИИ
echo 'TABLE->TABLE->DOCS'. PHP_EOL;
// удаляем те записи о файлах документации, которых уже нет в 1С
$query = "DELETE FROM `temp_docs` WHERE `code` NOT IN (SELECT `code` FROM `tmp_docs` WHERE 1)";
$register->database->execute($query);
// добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
$query = "SELECT * FROM `tmp_docs` WHERE `code` NOT IN (SELECT `code` FROM `temp_docs` WHERE 1)";
$docs = $register->database->fetchAll($query);
$exts = array(
    'jpg', 'jpeg', 'gif', 'png', 'bmp', 'doc', 'docx',
    'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'pdf'
);
foreach ($docs as $doc) {
    $ext = pathinfo($doc['filename'], PATHINFO_EXTENSION);
    if ( ! in_array($ext, $exts)) { // недопустимое расширение файла
        continue;
    }
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
                  '',
                  NOW(),
                  :code
              )";
    // $doc['title'] = trim($doc['title']);
    $register->database->execute(
        $query,
        array(
            'title' => $doc['title'],
            'filename' => $doc['filename'],
            'filetype' => $filetype,
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
     // $doc['title'] = trim($doc['title']);
     $register->database->execute($query, array('title' => $doc['title'], 'code' => $doc['code'])); 
}

// СЕРТИФИКАТЫ
echo 'TABLE->TABLE->CERTS'. PHP_EOL;
// удаляем те записи о сертификатах, которых уже нет в 1С
$query = "DELETE FROM `temp_certs` WHERE `code` NOT IN (SELECT `code` FROM `tmp_certs` WHERE 1)";
$register->database->execute($query);
// добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
$query = "SELECT * FROM `tmp_certs` WHERE `code` NOT IN (SELECT `code` FROM `temp_certs` WHERE 1)";
$certs = $register->database->fetchAll($query);
foreach ($certs as $cert) {
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
    // $cert['title'] = trim($cert['title']);
    $register->database->execute(
        $query,
        array(
            'title' => $cert['title'],
            'filename' => $cert['filename'],
            'count' => $cert['count'],
            'code' => $cert['code']
        )
    ); 
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
     // $cert['title'] = trim($cert['title']);
     $register->database->execute($query, array('title' => $cert['title'], 'code' => $cert['code'])); 
}

// ПРИВЯЗКА ФАЙЛОВ ДОКУМЕНТАЦИИ К ТОВАРАМ
// удаляем записи, которых уже нет в 1С
$query = "DELETE FROM `temp_doc_prd` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `tmp_doc_prd` WHERE 1)";
$register->database->execute($query);
// добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
$query = "SELECT * FROM `tmp_doc_prd` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `temp_doc_prd` WHERE 1)";
$rows = $register->database->fetchAll($query);
foreach ($rows as $row) {
    // уникальный идентификатор докмента
    $query = "SELECT `id` FROM `temp_docs` WHERE `code` = :doc_code";
    $doc_id = $register->database->fetchOne($query, array('doc_code' => $row['doc_code']));
    // TODO
    if (false === $doc_id) {
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

// ПРИВЯЗКА СЕРТИФИКАТОВ К ТОВАРАМ
// удаляем записи, которых уже нет в 1С
$query = "DELETE FROM `temp_cert_prod` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `tmp_cert_prod` WHERE 1)";
$register->database->execute($query);
// добавляем новые записи: которые уже есть в 1С, но еще нет на сайте
$query = "SELECT * FROM `tmp_cert_prod` WHERE `concat_code` NOT IN (SELECT `concat_code` FROM `temp_cert_prod` WHERE 1)";
$rows = $register->database->fetchAll($query);
foreach ($rows as $row) {
    // уникальный идентификатор сернтификата
    $query = "SELECT `id` FROM `temp_certs` WHERE `code` = :cert_code";
    $cert_id = $register->database->fetchOne($query, array('cert_code' => $row['cert_code']));
    // TODO
    if (false === $cert_id) {
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


die();

/*
 * сравниваем таблицы
 */
echo 'COMPARE TABLES' . PHP_EOL;

$query = "SELECT * FROM `products` WHERE 1 ORDER BY `id`";
$products = $register->database->fetchAll($query);
foreach ($products as $row1) {
    unset($row1['updated']);
    unset($row1['visible']);
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_products` WHERE `id` = :id";
    $row2 = $register->database->fetch($query, array('id' => $row1['id']));
    unset($row2['updated']);
    unset($row2['visible']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error products id='.$row1['id']);
    }
}
echo 'products OK' . PHP_EOL;

$query = "SELECT * FROM `categories` WHERE 1 ORDER BY `id`";
$categories = $register->database->fetchAll($query);
foreach ($categories as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_categories` WHERE `id` = :id";
    $row2 = $register->database->fetch($query, array('id' => $row1['id']));
    unset($row2['code']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error categories id='.$row1['id']);
    }
}
echo 'categories OK' . PHP_EOL;

$query = "SELECT * FROM `makers` WHERE 1 ORDER BY `id`";
$makers = $register->database->fetchAll($query);
foreach ($makers as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_makers` WHERE `id` = :id";
    $row2 = $register->database->fetch($query, array('id' => $row1['id']));
    unset($row2['code']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error makers id='.$row1['id']);
    }
}
echo 'makers OK' . PHP_EOL;

$query = "SELECT * FROM `groups` WHERE 1 ORDER BY `id`";
$groups = $register->database->fetchAll($query);
foreach ($groups as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_groups` WHERE `id` = :id";
    $row2 = $register->database->fetch($query, array('id' => $row1['id']));
    unset($row2['code']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error groups id='.$row1['id']);
    }
}
echo 'groups OK' . PHP_EOL;

$query = "SELECT * FROM `params` WHERE 1 ORDER BY `id`";
$params = $register->database->fetchAll($query);
foreach ($params as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_params` WHERE `id` = :id";
    $row2 = $register->database->fetch($query, array('id' => $row1['id']));
    unset($row2['code']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error params id='.$row1['id']);
    }
}
echo 'params OK' . PHP_EOL;

$query = "SELECT * FROM `values` WHERE 1 ORDER BY `id`";
$values = $register->database->fetchAll($query);
foreach ($values as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_values` WHERE `id` = :id";
    $row2 = $register->database->fetch($query, array('id' => $row1['id']));
    unset($row2['code']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error params id='.$row1['id']);
    }
}
echo 'values OK' . PHP_EOL;

$query = "SELECT * FROM `group_param_value` WHERE 1 ORDER BY `group_id`, `param_id`, `value_id`";
$items = $register->database->fetchAll($query);
foreach ($items as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_group_param_value` WHERE `group_id` = :group_id AND `param_id` = :param_id AND `value_id` = :value_id";
    $row2 = $register->database->fetch(
        $query,
        array(
            'group_id' => $row1['group_id'],
            'param_id' => $row1['param_id'],
            'value_id' => $row1['value_id']
        )
    );
    unset($row2['concat_code']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error group_param_value: group_id='.$row1['group_id'].' param_id='.$row1['param_id'].' value_id='.$row1['value_id']);
    }
}
echo 'group_param_value OK' . PHP_EOL;

$query = "SELECT * FROM `product_param_value` WHERE 1 ORDER BY `product_id`, `param_id`, `value_id`";
$items = $register->database->fetchAll($query);
foreach ($items as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_product_param_value` WHERE `product_id` = :product_id AND `param_id` = :param_id AND `value_id` = :value_id";
    $row2 = $register->database->fetch(
        $query,
        array(
            'product_id' => $row1['product_id'],
            'param_id' => $row1['param_id'],
            'value_id' => $row1['value_id']
        )
    );
    unset($row2['concat_code']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error product_param_value');
    }
}
echo 'product_param_value OK' . PHP_EOL;

$query = "SELECT * FROM `docs` WHERE 1 ORDER BY `id`";
$docs = $register->database->fetchAll($query);
foreach ($docs as $row1) {
    unset($row1['uploaded']);
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_docs` WHERE `id` = :id";
    $row2 = $register->database->fetch($query, array('id' => $row1['id']));
    unset($row2['uploaded']);
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error docs id='.$row1['id']);
    }
}
echo 'docs OK' . PHP_EOL;

$query = "SELECT * FROM `doc_prd` WHERE 1 ORDER BY `prd_id`, `doc_id`";
$items = $register->database->fetchAll($query);
foreach ($items as $row1) {
    $str1 = serialize($row1);
    $query = "SELECT * FROM `temp_doc_prd` WHERE `prd_id` = :prd_id AND `doc_id` = :doc_id";
    $row2 = $register->database->fetch(
        $query,
        array(
            'prd_id' => $row1['prd_id'],
            'doc_id' => $row1['doc_id']
        )
    );
    $str2 = serialize($row2);
    if ($str1 !== $str2) {
        die('Error doc_prd');
    }
}
echo 'doc_prd OK' . PHP_EOL;


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
