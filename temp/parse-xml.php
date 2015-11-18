<?php
/**
 * Для запуска из командной строки для формирования xml каталога
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ZCMS', true);

chdir('..');

if (is_file('temp/parse-res.html')) unlink('temp/parse-res.html');

ob_start();

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

$reader = new XMLReader();
$reader->open('catalog-temp.xml');
$item = array();
while ($reader->read()) {
    // КАТЕГОРИИ
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'categories') {
        echo '<h1>Категории</h1>';
        // проходим в цикле все дочерние элементы элемента <categories>
        while ($reader->read()) {
            // отдельный элемент <category>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'category') {
                echo '<h2>Категория</h2>';
                // атрибуты элемента <category>
                $id = $reader->getAttribute('id');
                echo '<div>Атрибут id: ' . $id . '</div>';
                $parent = $reader->getAttribute('parent');
                echo '<div>Атрибут parent: ' . $parent . '</div>';
                $sortorder = $reader->getAttribute('sortorder');
                echo '<div>Атрибут sortorder: ' . $sortorder . '</div>';
                // читаем дальше для получения текстового элемента
                $reader->read();
                echo '<div>Наименование категории: ' . $reader->value . '</div>';
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'categories') {
                break;
            }
        }
    }

    // ПРОИЗВОДИТЕЛИ
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'makers') { // элемент <makers>
        echo '<h1>Производители</h1>';
        // проходим в цикле все дочерние элементы элемента <makers>
        while ($reader->read()) {
            // отдельный элемент <maker>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'maker') {
                echo '<h2>Производитель</h2>';
                // атрибуты элемента <maker>
                $id = $reader->getAttribute('id');
                echo '<div>Атрибут id: ' . $id . '</div>';
                // читаем дальше для получения текстового элемента
                $reader->read();
                echo '<div>Наименование производителя: ' . $reader->value . '</div>';
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'makers') {
                break;
            }
        }
    }

    // ФУНКЦИОНАЛЬНЫЕ ГРУППЫ
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'groups') { // элемент <groups>
        echo '<h1>Функциональные группы</h1>';
        // проходим в цикле все дочерние элементы элемента <groups>
        while ($reader->read()) {
            // отдельный элемент <group>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'group') {
                echo '<h2>Группа</h2>';
                // атрибуты элемента <group>
                $id = $reader->getAttribute('id');
                echo '<div>Атрибут id: ' . $id . '</div>';
                // читаем дальше для получения текстового элемента
                $reader->read();
                echo '<div>Наименование группы: ' . $reader->value . '</div>';
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'groups') {
                break;
            }
        }
    }

    // ПАРАМЕТРЫ
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'params') { // элемент <params>
        echo '<h1>Параметры</h1>';
        // читаем дальше для получения элемента <names>
        $reader->read();
        echo '<h2>Наименования параметров</h2>';
        // проходим в цикле все дочерние элементы элемента <names>
        while ($reader->read()) {
            // отдельный элемент <name>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                // атрибуты элемента <name>
                $id = $reader->getAttribute('id');
                echo '<div>Атрибут id: ' . $id . '</div>';
                // читаем дальше для получения текстового элемента
                $reader->read();
                echo '<div>Наименование параметра: ' . $reader->value . '</div>';
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'names') {
                break;
            }
        }
        // читаем дальше для получения элемента <values>
        $reader->read();
        echo '<h2>Значения параметров</h2>';
        // проходим в цикле все дочерние элементы элемента <values>
        while ($reader->read()) {
            // отдельный элемент <value>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'value') {
                // атрибуты элемента <value>
                $id = $reader->getAttribute('id');
                echo '<div>Атрибут id: ' . $id . '</div>';
                // читаем дальше для получения текстового элемента
                $reader->read();
                echo '<div>Значение параметра: ' . $reader->value . '</div>';
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'values') {
                break;
            }
        }
    }

    // ТОВАРЫ
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'products') { // элемент <products>
        echo '<h1>Товары</h1>';
        // проходим в цикле все дочерние элементы элемента <products>
        while ($reader->read()) {
            // отдельный элемент <product>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'product') {
                echo '<h2>Товар</h2>';
                // атрибуты элемента <product>
                $code = $reader->getAttribute('code');
                echo '<div>Атрибут code: ' . $code . '</div>';
                // проходим в цикле все дочерние элементы элемента <products>
                while ($reader->read()) {
                    // торговое наименование
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $name = $reader->value;
                        echo '<div>Торговое наименование: ' . $name . '</div>';
                    }
                    // функциональное наименование
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'title') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $title = $reader->value;
                        echo '<div>Функциональное наименование: ' . $title . '</div>';
                    }
                    // краткое описание
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'shortdescr') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $shortdescr = $reader->value;
                        echo '<div>Краткое описание: ' . $shortdescr . '</div>';
                    }
                    // назначение
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'purpose') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $purpose = $reader->value;
                        echo '<div>Назначение: ' . $purpose . '</div>';
                    }
                    // технические характеристики
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'techdata') {
                        echo '<h4>Технические характеристики</h4>';
                        // проходим в цикле все дочерние элементы элемента <techdata>
                        while ($reader->read()) {
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                                $reader->read();
                                $name = $reader->value;
                                echo '<div>Наименование пареметра: ' . $name . '</div>';
                            }
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'value') {
                                $reader->read();
                                $value = $reader->value;
                                echo '<div>Значение пареметра: ' . $value . '</div>';
                            }
                            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'techdata') {
                                break;
                            }
                        }
                    }
                    // особенности
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'features') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $features = $reader->value;
                        echo '<div>Особенности: ' . $features . '</div>';
                    }
                    // комплектация
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'complect') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $complect = $reader->value;
                        echo '<div>Комплектация: ' . $complect . '</div>';
                    }
                    // доп.оборудование
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'equipment') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $equipment = $reader->value;
                        echo '<div>Доп.оборудование: ' . $equipment . '</div>';
                    }
                    // доп.информация
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'additional') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $additional = $reader->value;
                        echo '<div>Доп.информация: ' . $additional . '</div>';
                    }
                    // фото
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'image') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $image = $reader->value;
                        echo '<div>Фото: ' . $image . '</div>';
                    }
                    // параметры подбора
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'params') {
                        echo '<h4>Параметры подбора</h4>';
                        // проходим в цикле все дочерние элементы элемента <params>
                        while ($reader->read()) {
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'param') {
                                // атрибуты элемента <param>
                                $name = $reader->getAttribute('name');
                                echo '<div>Атрибут name: ' . $name . '</div>';
                                $value = $reader->getAttribute('value');
                                echo '<div>Атрибут value: ' . $value . '</div>';
                            }
                            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'params') {
                                break;
                            }
                        }
                    }
                    // файлы документации
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'docs') {
                        echo '<h4>Файлы документации</h4>';
                        // проходим в цикле все дочерние элементы элемента <docs>
                        while ($reader->read()) {
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'doc') {
                                // атрибуты элемента <doc>
                                $id = $reader->getAttribute('id');
                                echo '<div>Атрибут id: ' . $id . '</div>';
                            }
                            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'docs') {
                                break;
                            }
                        }
                    }
                    // сертификаты
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'certs') {
                        echo '<h4>Сертификаты</h4>';
                        // проходим в цикле все дочерние элементы элемента <certs>
                        while ($reader->read()) {
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'cert') {
                                // атрибуты элемента <cert>
                                $id = $reader->getAttribute('id');
                                echo '<div>Атрибут id: ' . $id . '</div>';
                            }
                            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'certs') {
                                break;
                            }
                        }
                    }
                    // связанные товары
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'linked') {
                        echo '<h4>Связанные товары</h4>';
                        // проходим в цикле все дочерние элементы элемента <docs>
                        while ($reader->read()) {
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'prd') {
                                // атрибуты элемента <prd>
                                $code = $reader->getAttribute('code');
                                echo '<div>Атрибут code: ' . $code . '</div>';
                                $count = $reader->getAttribute('count');
                                echo '<div>Атрибут count: ' . $count . '</div>';
                            }
                            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'linked') {
                                break;
                            }
                        }
                    }

                    if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'product') {
                        break;
                    }
                }
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'products') {
                break;
            }
        }
    }

    // ФАЙЛЫ ДОКУМЕНТАЦИИ
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'docs') { // элемент <docs>
        echo '<h1>Файлы документации</h1>';
        // проходим в цикле все дочерние элементы элемента <docs>
        while ($reader->read()) {
            // отдельный элемент <doc>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'doc') {
                echo '<h2>Документ</h2>';
                // атрибуты элемента <doc>
                $id = $reader->getAttribute('id');
                echo '<div>Атрибут id: ' . $id . '</div>';
                // дочерние элементы элемента <doc>
                while ($reader->read()) {
                    // наименование документа
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'title') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $title = $reader->value;
                        echo '<div>Наименование документа: ' . $title . '</div>';
                    }
                    // имя файла
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'file') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $file = $reader->value;
                        echo '<div>Имя файла: ' . $file . '</div>';
                    }
                    // сумма md5 файла
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'md5') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $md5 = $reader->value;
                        echo '<div>Сумма md5 файла: ' . $md5 . '</div>';
                    }
                    if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'doc') {
                        break;
                    }
                }
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'docs') {
                break;
            }
        }
    }

    // СЕРТИФИКАТЫ
    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'certs') { // элемент <certs>
        echo '<h1>Сертификаты</h1>';
        // проходим в цикле все дочерние элементы элемента <certs>
        while ($reader->read()) {
            // отдельный элемент <cert>
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'cert') {
                echo '<h2>Сертификат</h2>';
                // атрибуты элемента <cert>
                $id = $reader->getAttribute('id');
                echo '<div>Атрибут id: ' . $id . '</div>';
                // дочерние элементы элемента <cert>
                while ($reader->read()) {
                    // наименование сертификата
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'title') {
                        // читаем дальше для получения текстового элемента
                        $reader->read();
                        $title = $reader->value;
                        echo '<div>Наименование документа: ' . $title . '</div>';
                    }
                    // информация о файле
                    if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'file') {
                        // дочерние элементы элемента <file>
                        while ($reader->read()) {
                            // имя файла
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'name') {
                                // читаем дальше для получения текстового элемента
                                $reader->read();
                                $name = $reader->value;
                                echo '<div>Имя файла: ' . $name . '</div>';
                            }
                            // сумма md5
                            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'md5') {
                                // читаем дальше для получения текстового элемента
                                $reader->read();
                                $md5 = $reader->value;
                                echo '<div>Сумма md5: ' . $md5 . '</div>';
                            }
                            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'file') {
                                break;
                            }
                        }
                    }
                    if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'cert') {
                        break;
                    }
                }
            }
            if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->localName == 'certs') {
                break;
            }
        }
    }

}

$res = ob_get_clean();
//echo $res;
file_put_contents('temp/parse-res.html', $res);