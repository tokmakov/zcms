<?php
/**
 * Класс Addgroup_Filter_Backend_Controller формирует страницу с формой для
 * добавления функциональной группы. Получает данные от модели
 * Filter_Backend_Model, административная часть сайта
 */
class Addgroup_Filter_Backend_Controller extends Filter_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для добавления функциональной группы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Filter_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Addgroup_Filter_Backend_Controller
         */
        parent::input();

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                $this->redirect($this->filterBackendModel->getURL('backend/filter/addgroup'));
            } else {
                $this->redirect($this->filterBackendModel->getURL('backend/filter/allgroups'));
            }
        }

        $this->title = 'Новая группа. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array('url' => $this->filterBackendModel->getURL('backend/index/index'), 'name' => 'Главная'),
            array('url' => $this->filterBackendModel->getURL('backend/filter/index'), 'name' => 'Фильтр'),
            array('url' => $this->filterBackendModel->getURL('backend/filter/allgroups'), 'name' => 'Группы'),
        );

        // получаем от модели массив параметров подбора для возможности привязки
        // параметров к новой функциональной группе
        $allParams = $this->filterBackendModel->getParams();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->filterBackendModel->getURL('backend/filter/addgroup'),
            // массив параметров подбора
            'allParams'   => $allParams,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные администратором данные
        if ($this->issetSessionData('addFilterGroupForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('addFilterGroupForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('addFilterGroupForm');
        }

    }

    /**
     * Функция проверяет корректность введенных администратором данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция добавляет
     * функциональную группу и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        // наименование группы
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 100));
        // параметры, привязанные к группе
        $data['params'] = array();
        if (isset($_POST['params']) && is_array($_POST['params'])) {
            foreach ($_POST['params'] as $key => $value) {
                $data['params'][] = $key;
            }
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
            $this->setSessionData('addFilterGroupForm', $data);
            return false;
        }

        // обращаемся к модели для добавления новой группы
        $this->filterBackendModel->addGroup($data);

        return true;

    }

}