<?php
/**
 * Класс Editctg_Sale_Backend_Controller для редактирования категории товаров со
 * скидкой, формирует страницу с формой для редактирования категории, обновляет
 * запись в таблице БД sale_categories, работает с моделью Sale_Backend_Model
 */
class Editctg_Sale_Backend_Controller extends Sale_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Sale_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Editctg_Sale_Backend_Controller
         */
        parent::input();

        // если не передан id категории или id категории не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                // перенаправляем администратора сайта обратно
                // на страницу с формой для исправления ошибок
                $this->redirect($this->saleBackendModel->getURL('backend/sale/editctg/id/' . $this->params['id']));
            } else {
                $this->redirect($this->saleBackendModel->getURL('backend/sale/index'));
            }
        }

        $this->title = 'Редактирование категории. ' . $this->title;

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

        // получаем от модели информацию о категории
        $name = $this->saleBackendModel->getCategoryName($this->params['id']);
        // если запрошенная категория не найдена в БД
        if (empty($name)) {
            $this->notFoundRecord = true;
            return;
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->saleBackendModel->getURL('backend/sale/editctg/id/' . $this->params['id']),
            // наименование категории
            'name'        => $name,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений об ошибках
        if ($this->issetSessionData('editSaleCategoryForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editSaleCategoryForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editSaleCategoryForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет категорию и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 250)); // наименование категории

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Наименование»';
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editSaleCategoryForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор категории

        // обращаемся к модели для обновления категории
        $this->saleBackendModel->updateCategory($data);

        return true;

    }

}