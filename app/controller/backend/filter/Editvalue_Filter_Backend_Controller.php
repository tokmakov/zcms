<?php
/**
 * Класс Editvalue_Filter_Backend_Controller формирует страницу с формой для
 * редактирования значения параметра подбора. Получает данные от модели
 * Filter_Backend_Model, административная часть сайта
 */
class Editvalue_Filter_Backend_Controller extends Filter_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования значения параметра подбора
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Filter_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Editvalue_Filter_Backend_Controller
         */
        parent::input();

        // если не передан id параметра или id параметра не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->filterBackendModel->getURL('backend/filter/editvalue/id/' . $this->params['id']));
            } else {
                $this->redirect($this->filterBackendModel->getURL('backend/filter/allvalues'));
            }
        }

        $this->title = 'Редактирование значения параметра. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->filterBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->filterBackendModel->getURL('backend/filter/index'), 'name' => 'Фильтр'),
            array('url' => $this->filterBackendModel->getURL('backend/filter/allvalues'), 'name' => 'Значения'),
        );

        // получаем от модели информацию о редактируемом значении параметра
        $name = $this->filterBackendModel->getValue($this->params['id']);

        // если запрошенное значение не найдено в БД
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
            'action'      => $this->filterBackendModel->getURL('backend/filter/editvalue/id/' . $this->params['id']),
            // уникальный идентификатор значения параметра
            'id'          => $this->params['id'],
            // наименование значения параметра
            'name'        => $name,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные администратором данные
        if ($this->issetSessionData('editFilterValueForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editFilterValueForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editFilterValueForm');
        }

    }

    /**
     * Функция проверяет корректность введенных администратором данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция обновляет
     * значение параметра и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // наименование параметра
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 100));

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
            $this->setSessionData('editFilterValueForm', $data);
            return false;
        }

        // уникальный идентификатор значения параметра
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления значения параметра
        $this->filterBackendModel->updateValue($data);

        return true;

    }

}