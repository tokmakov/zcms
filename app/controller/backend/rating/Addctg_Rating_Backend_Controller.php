<?php
/**
 * Класс Addctg_Rating_Backend_Controller для добавления новой категории товаров 
 * рейтинга, формирует страницу с формой для добавления категории, добавляет запись
 * в таблицу БД rating_categories, работает с моделью Rating_Backend_Model
 */
class Addctg_Rating_Backend_Controller extends Rating_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления категории
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Ratung_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Addctg_Rating_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора сайта обратно на страницу
                // с формой для исправления ошибок
                $this->redirect($this->ratingBackendModel->getURL('backend/rating/addctg'));
            } else {
                $this->redirect($this->ratingBackendModel->getURL('backend/rating/index'));
            }
        }

        $this->title = 'Новая категория. ' . $this->title;

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
        
        // получаем от модели массив категорий верхнего уровня, для возможности выбора родителя
        $categories = $this->ratingBackendModel->getRootCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->saleBackendModel->getURL('backend/rating/addctg'),
            // массив категорий верхнего уровня
            'categories'  => $categories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('addRatingCategoryForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addRatingCategoryForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addRatingCategoryForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция добавляет категорию и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
         
        // наименование категории
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 250));
        // родительская категория
        $data['parent'] = 0;
        if (ctype_digit($_POST['parent'])) {
            $data['parent'] = (int)$_POST['parent'];
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('addRatingCategoryForm', $data);
            return false;
        }

        // обращаемся к модели для добавления новой категории
        $this->ratingBackendModel->addCategory($data);

        return true;

    }

}