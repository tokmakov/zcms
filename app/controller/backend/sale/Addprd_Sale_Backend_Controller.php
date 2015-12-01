<?php
/**
 * Класс Addprd_Sale_Backend_Controller для добавления нового товара со скидкой,
 * формирует страницу с формой для добавления товара, добавляет запись в таблицу
 * БД sale_products, работает с моделью Sale_Backend_Model
 */
class Addprd_Sale_Backend_Controller extends Sale_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы с формой для добавления товара
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

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора сайта обратно
                // на страницу с формой для исправления ошибок
                $this->redirect($this->saleBackendModel->getURL('backend/sale/addprd'));
            } else { // ошибок не было, добавление товара прошло успешно
                $this->redirect($this->saleBackendModel->getURL('backend/sale/index'));
            }
        }

        $this->title = 'Новый товар. ' . $this->title;

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

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->saleBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action' => $this->saleBackendModel->getURL('backend/sale/addprd'),
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addSaleProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addSaleProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addSaleProductForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет категорию и возвращает true
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
            $this->setSessionData('addSaleProductForm', $data);
            return false;
        }

        // обращаемся к модели для добавления нового товара
        $this->saleBackendModel->addProduct($data);

        return true;

    }

}