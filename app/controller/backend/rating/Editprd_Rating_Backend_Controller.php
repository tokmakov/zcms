<?php
/**
 * Класс Editprd_Rating_Backend_Controller для редактирования товара рейтинга,
 * формирует страницу с формой для редактирования товара, обновляет запись в
 * таблице БД rating_products, работает с моделью Rating_Backend_Model
 */
class Editprd_Rating_Backend_Controller extends Rating_Backend_Controller {
    
    /**
     * идентификатор категории верхнего уровня, в которую вернется администратор
     * после успешного обновления товара и редиректа
     */
    private $return = 0;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы с формой для редактирования товара
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Rating_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Editprd_Rating_Backend_Controller
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
                if ($this->return) { // возвращаемся в родительскую категорию верхнего уровня отредактированного товара
                    $this->redirect($this->ratingBackendModel->getURL('backend/rating/root/id/' . $this->return));
                } else { // возвращаемся на главную страницу рейтинга продаж
                    $this->redirect($this->ratingBackendModel->getURL('backend/rating/index'));
                }
            } else { // если при заполнении формы были допущены ошибки
                $this->redirect($this->ratingBackendModel->getURL('backend/rating/editprd/id/' . $this->params['id']));
            }
        }

        $this->title = 'Редактирование товара. ' . $this->title;

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
        
        // получаем от модели информацию о товаре
        $product = $this->ratingBackendModel->getProduct($this->params['id']);
        // если запрошенный товар не найден в БД
        if (empty($product)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели массив всех категорий, для возможности выбора родителя
        $categories = $this->ratingBackendModel->getCategories();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->ratingBackendModel->getURL('backend/rating/editprd/id/' . $this->params['id']),
            // код (артикул) товара
            'code'        => $product['code'],
            // торговое наименование изделия
            'name'        => $product['name'],
            // функциональное наименование изделия
            'title'       => $product['title'],
            // родительская категория
            'category'    => $product['category'],
            // массив всех категорий, для возможности выбора родителя
            'categories'  => $categories,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editRatingProductForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editRatingProductForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editRatingProductForm');
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
            $this->setSessionData('editRatingProductForm', $data);
            return false;
        }
        
        // идентификатор категории верхнего уровня, в которую
        // вернется администратор после редиректа
        $this->return = $this->ratingBackendModel->getCategoryParent($data['category']);
        
        $data['id'] = $this->params['id']; // уникальный идентификатор товара

        // обращаемся к модели для обновления товара
        $this->ratingBackendModel->updateProduct($data);

        return true;

    }

}