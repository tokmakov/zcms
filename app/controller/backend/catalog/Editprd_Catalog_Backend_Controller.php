<?php
/**
 * Класс Editprd_Catalog_Backend_Controller для редактирования товара каталога, формирует
 * страницу с формой для редактирования товара, обновляет запись в таблице БД products,
 * работает с моделью Catalog_Backend_Model, административная часть сайта
 */
class Editprd_Catalog_Backend_Controller extends Catalog_Backend_Controller {

    /**
     * идентификатор категории, в которую вернется администратор после
     * успешного обновления товара и редиректа
     */
    private $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Catalog_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editprd_Catalog_Backend_Controller
         */
        parent::input();

        // если не передан id товара или id товара не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, обновление товара прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию отредактированного товара
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/category/id/' . $this->return));
                } else { // возвращаемся на главную страницу каталога
                    $this->redirect($this->catalogBackendModel->getURL('backend/catalog/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->catalogBackendModel->getURL('backend/catalog/editprd/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование товара. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->catalogBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->catalogBackendModel->getURL('backend/catalog/index'), 'name' => 'Каталог'),
        );

        // получаем от модели информацию о товаре
        $product = $this->catalogBackendModel->getProduct($this->params['id']);
        // если запрошенный товар не найден в БД
        if (empty($product)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->catalogBackendModel->getAllCategories();

        // получаем от модели массив всех функиональных групп, для возможности выбора
        $groups = $this->catalogBackendModel->getAllGroups();

        // получаем от модели массив параметров, привязанных к группе и массивы
        // привязанных к этим параметрам значений
        $allParams = $this->catalogBackendModel->getGroupParams($product['group']);

        // получаем от модели массив всех производителей, для возможности выбора
        $makers = $this->catalogBackendModel->getMakers();

        // единицы измерения для возможности выбора
        $units = $this->catalogBackendModel->getUnits();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->catalogBackendModel->getURL('backend/catalog/editprd/id/' . $this->params['id']),
            // уникальный идентификатор товара
            'id'          => $this->params['id'],
            // родительская категория
            'category'    => $product['category'],
            // дополнительная категория
            'category2'   => $product['category2'],
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
            // функциональная группа
            'group'       => $product['group'],
            // массив всех функциональных групп, для возможности выбора
            'groups'      => $groups,
            // уникальный идентификатор производителя
            'maker'       => $product['maker'],
            // массив всех производителей, для возможности выбора
            'makers'      => $makers,
            // массив параметров, привязанных к товару и массивы привязанных к этим параметрам значений
            'params'      => $product['params'],
            // массив параметров, привязанных к группе и массивы привязанных к этим параметрам значений
            'allParams'   => $allParams,
            // код (артикул) товара
            'code'        => $product['code'],
            // наименование изделия
            'name'        => $product['name'],
            // функциональное наименование изделия
            'title'       => $product['title'],
            // мета-тег keywords
            'keywords'    => $product['keywords'],
            // мета-тег description
            'description' => $product['description'],
            // цена
            'price'       => $product['price'],
            'price2'      => $product['price2'],
            'price3'      => $product['price3'],
            'price4'      => $product['price4'],
            'price5'      => $product['price5'],
            'price6'      => $product['price6'],
            'price7'      => $product['price7'],
            // единица измерения
            'unit'        => $product['unit'],
            // все единицы измерения для возможности выбора
            'units'       => $units,
            // имя файла изображения
            'image'       => $product['image'],
            // краткое описание
            'shortdescr'  => $product['shortdescr'],
            // назначение изделия
            'purpose'     => $product['purpose'],
            // особенности изделия
            'features'    => $product['features'],
            // комплектация изделия
            'complect'    => $product['complect'],
            // дополнительное оборудование
            'equipment'   => $product['equipment'],
            // дополнительная информация
            'padding'     => $product['padding'],
            // идентификаторы товаров, которые покупают с этим
            'related'     => $product['related'],
            // файлы документации
            'docs'        => $product['docs'],
        );
        // технические характеристики
        $this->centerVars['techdata']    = array();
        if ( ! empty($product['techdata'])) {
            $this->centerVars['techdata'] = unserialize($product['techdata']);
        }
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        // и сохраненные данные формы
        if ($this->issetSessionData('editCatalogProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editCatalogProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editCatalogProductForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция
     * обновляет товар и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // наименование изделия
        $data['name']        = trim(iconv_substr($_POST['name'], 0, 250));
        // функциональное наименование изделия
        $data['title']       = trim(iconv_substr($_POST['title'], 0, 250));
        // мета-тег keywords
        $data['keywords']    = trim(iconv_substr($_POST['keywords'], 0, 250));
        $data['keywords']    = str_replace('"', '', $data['keywords']);
        // мета-тег description
        $data['description'] = trim(iconv_substr($_POST['description'], 0, 250));
        $data['description'] = str_replace('"', '', $data['description']);
        // код (артикул) товара
        $data['code']        = trim(iconv_substr($_POST['code'], 0, 16));
        // краткое описание
        $data['shortdescr']  = trim(iconv_substr($_POST['shortdescr'], 0, 2000));
        // назначение изделия
        $data['purpose']     = trim($_POST['purpose']);
        // особенности изделия
        $data['features']    = trim($_POST['features']);
        // комплектация изделия
        $data['complect']    = trim($_POST['complect']);
        // дополнительное оборудование
        $data['equipment']   = trim($_POST['equipment']);
        // дополнительно
        $data['padding']     = trim($_POST['padding']);

        // технические характеристики
        $data['techdata'] = '';
        $techdataName = array();
        foreach ($_POST['techdata_name'] as $name) {
            $tmp = trim(iconv_substr($name, 0, 250));
            $tmp = preg_replace('~\s+~u', ' ', $tmp);
            $techdataName[] = $tmp;
        }
        $techdataValue = array();
        foreach ($_POST['techdata_value'] as $value) {
            $tmp = trim(iconv_substr($value, 0, 250));
            $tmp = preg_replace('~\s+~u', ' ', $tmp);
            $techdataValue[] = $tmp;
        }
        $techdataNameValue = array();
        foreach ($techdataName as $key => $name) {
            if (empty($name) && empty ($techdataValue[$key])) {
                continue;
            }
            $techdataNameValue[] = array($name, $techdataValue[$key]);
        }
        if (count($techdataNameValue) > 0) {
            $data['techdata'] = serialize($techdataNameValue);
        }

        // цена
        $data['price'] = 0.0;
        $temp = trim($_POST['price']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price'] = (float)$temp;
        }
        $data['price2'] = 0.0;
        $temp = trim($_POST['price2']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price2'] = (float)$temp;
        }
        $data['price3'] = 0.0;
        $temp = trim($_POST['price3']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price3'] = (float)$temp;
        }
        $data['price4'] = 0.0;
        $temp = trim($_POST['price4']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price4'] = (float)$temp;
        }
        $data['price5'] = 0.0;
        $temp = trim($_POST['price5']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price5'] = (float)$temp;
        }
        $data['price6'] = 0.0;
        $temp = trim($_POST['price6']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price6'] = (float)$temp;
        }
        $data['price7'] = 0.0;
        $temp = trim($_POST['price7']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price7'] = (float)$temp;
        }

        // единица измерения
        $data['unit'] = 0;
        if (ctype_digit($_POST['unit'])) {
            $data['unit'] = (int)$_POST['unit'];
        }

        // родительская категория
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category']; // новая родительская категория
        }

        // дополнительная категория
        $data['category2'] = 0;
        if (ctype_digit($_POST['category2'])) {
            $data['category2'] = (int)$_POST['category2'];
        }

        // функциональная группа
        $data['group'] = 0;
        if (ctype_digit($_POST['group'])) {
            $data['group'] = (int)$_POST['group'];
        }

        // производитель
        $data['maker'] = 0;
        if (ctype_digit($_POST['maker'])) {
            $data['maker'] = (int)$_POST['maker'];
        }
        
        // идентификаторы товаров, которые покупают с этим
        $data['related'] = '';
        $temp = trim($_POST['related']);
        $temp = trim($temp, ',');
        if (preg_match('~^\d+(,\d+)*$~', $temp)) {
            $data['related'] = $temp;
        }

        // параметры подбора
        $data['params'] = array();
        if (isset($_POST['params']) && is_array($_POST['params'])) {
            foreach ($_POST['params'] as $key => $value) {
                $data['params'][$key] = array();
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $data['params'][$key][] = $k;
                    }
                }
            }
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование товара»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['group'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Функциональная группа»';
        }
        if (empty($data['maker'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Производитель»';
        }
        if (empty($data['code'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Код (артикул)»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее данными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            if ( ! empty($data['techdata'])) {
                $data['techdata'] = unserialize($data['techdata']);
            } else {
                $data['techdata'] = array();
            }
            // если была выбрана новая функциональная группа, мы должны получить от
            // модели массив параметров, привязанных к новой группе, массивы привязанных
            // к этим параметрам значений и передать эти данные в шаблон
            $data['allParams'] = $this->catalogBackendModel->getGroupParams($data['group']);
            // сохраняем введенные администратором данные в сессии
            $this->setSessionData('editCatalogProductForm', $data);
            return false;
        }

        // идентификатор категории, в которую вернется администратор после редиректа
        $this->return = $data['category'];

        $data['id'] = $this->params['id']; // уникальный идентификатор товара

        // обращаемся к модели для обновления товара
        $this->catalogBackendModel->updateProduct($data);

        return true;
    }

}