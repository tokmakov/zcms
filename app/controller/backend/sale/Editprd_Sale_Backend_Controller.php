<?php
/**
 * Класс Addprd_Sale_Backend_Controller для редактирования товара со скидкой,
 * формирует страницу с формой для редактирования товара, обновляет запись в
 * таблице БД sale_products, работает с моделью Sale_Backend_Model
 */
class Editprd_Sale_Backend_Controller extends Sale_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы с формой для редактирования товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sale_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Addprd_Sale_Backend_Controller
         */
        parent::input();

        // если не передан id товара или id товара не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id']))) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора сайта обратно на страницу
                // с формой для исправления ошибок
                $this->redirect($this->saleBackendModel->getURL('backend/sale/editprd/id/' . $this->params['id']));
            } else {
                $this->redirect($this->saleBackendModel->getURL('backend/sale/index'));
            }
        }

        $this->title = 'Редактирование товара. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->saleBackendModel->getURL('backend/index/index'),
            ),
            array(
                'name' => 'Распродажа',
                'url'  => $this->saleBackendModel->getURL('backend/sale/index'),
            )
        );

        // получаем от модели информацию о товаре
        $product = $this->saleBackendModel->getProduct($this->params['id']);
        // если запрошенный товар не найден в БД
        if (empty($product)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->saleBackendModel->getCategories();

        // единицы измерения для возможности выбора
        $units = $this->catalogBackendModel->getUnits();

        // фото товара
        $image = null;
        if (is_file('files/sale/' . $this->params['id'] . '.jpg')) {
            $image = $this->config->site->url . 'files/sale/' . $this->params['id'] . '.jpg';
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->saleBackendModel->getURL('backend/sale/editprd/id/' . $this->params['id']),
            // уникальный идентификатор товара
            'id'          => $this->params['id'],
            // родительская категория
            'category'    => $product['category'],
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
            // код (артикул) товара
            'code'        => $product['code'],
            // наименование изделия
            'name'        => $product['name'],
            // функциональное наименование изделия
            'title'       => $product['title'],
            // краткое описание товара
            'description' => $product['description'],
            // цена
            'price1'      => $product['price1'],
            'price2'      => $product['price2'],
            // единица измерения
            'unit'        => $product['unit'],
            // все единицы измерения для возможности выбора
            'units'       => $units,
            // количество товара
            'count'       => $product['count'],
            // фото товара
            'image'       => $image,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editSaleProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editSaleProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editSaleProductForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет товар и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // наименование изделия
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 100));
        // функциональное наименование изделия
        $data['title']       = trim(utf8_substr($_POST['title'], 0, 200));
        // код (артикул) товара
        $data['code']        = trim(utf8_substr($_POST['code'], 0, 16));
        // краткое описание
        $data['description'] = trim(utf8_substr($_POST['description'], 0, 1000));

        // цена
        $data['price1'] = 0.0;
        $temp = trim($_POST['price1']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price1'] = (float)$temp;
        }
        $data['price2'] = 0.0;
        $temp = trim($_POST['price2']);
        if (preg_match('~^\d+(\.\d{1,5})?$~', $temp)) {
            $data['price2'] = (float)$temp;
        }

        // единица измерения
        $data['unit'] = 0;
        if (ctype_digit($_POST['unit'])) {
            $data['unit'] = (int)$_POST['unit'];
        }

        // количество
        $data['count'] = 1;
        if (isset($_POST['count']) && ctype_digit($_POST['count'])) {
            $data['count'] = (int)$_POST['count'];
        }

        // родительская категория
        $data['category'] = 0;
        if (ctype_digit($_POST['category'])) {
            $data['category'] = (int)$_POST['category'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }
        if (empty($data['category'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Категория»';
        }
        if (empty($data['code'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Код (артикул)»';
        }
        if (empty($data['count'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Количество»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editSaleProductForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор товара

        // обращаемся к модели для обновления товара
        $this->saleBackendModel->updateProduct($data);

        return true;

    }

}