<?php
/**
 * Класс Addprd_Rating_Backend_Controller для добавления нового товара рейтинга,
 * формирует страницу с формой для добавления товара, добавляет запись в таблицу
 * БД rating_products, работает с моделью Rating_Backend_Model
 */
class Addprd_Rating_Backend_Controller extends Rating_Backend_Controller {
    
    /**
     * идентификатор категории верхнего уровня, в которую вернется администратор
     * после успешного добавления товара и редиректа
     */
    private $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы с формой для добавления товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Rating_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Addprd_Rating_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // ошибок не было, добавление товара прошло успешно
                if ($this->return) { // возвращаемся в родительскую категорию верхнего уровня добавленного товара
                    $this->redirect($this->ratingBackendModel->getURL('backend/rating/root/id/' . $this->return));
                } else { // возвращаемся на главную страницу рейтинга продаж
                    $this->redirect($this->ratingBackendModel->getURL('backend/rating/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->ratingBackendModel->getURL('backend/rating/addprd'));
            }
        }

        $this->title = 'Новый товар. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->ratingBackendModel->getURL('backend/index/index'),
            ),
            array(
                'name' => 'Рейтинг',
                'url'  => $this->ratingBackendModel->getURL('backend/rating/index'),
            )
        );

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->ratingBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->ratingBackendModel->getURL('backend/rating/addprd'),
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addRatingProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addRatingProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addRatingProductForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет товар и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // код (артикул) товара
        $data['code']        = trim(utf8_substr($_POST['code'], 0, 16));
        // торговое наименование изделия
        $data['name']        = trim(utf8_substr($_POST['name'], 0, 100));
        // функциональное наименование изделия
        $data['title']       = trim(utf8_substr($_POST['title'], 0, 200));

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

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addRatingProductForm', $data);
            return false;
        }
        
        // идентификатор категории верхнего уровня, в которую
        // вернется администратор после редиректа
        $this->return = $this->ratingBackendModel->getCategoryParent($data['category']);

        // обращаемся к модели для добавления нового товара
        $this->ratingBackendModel->addProduct($data);

        return true;

    }

}