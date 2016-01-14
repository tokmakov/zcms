<?php
/**
 * Класс Editgroup_Filter_Backend_Controller формирует страницу с формой для
 * редактирования функциональной группы. Получает данные от модели
 * Filter_Backend_Model, административная часть сайта
 */
class Editgroup_Filter_Backend_Controller extends Filter_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования функциональной группы
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Filter_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Editgroup_Filter_Backend_Controller
         */
        parent::input();

        // если не передан id группы или id группы не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            /*
             * Форма имеет две кнопки отправки данных:
             * 1. Кнопка <input type="submit" name="submit" value="Сохранить" />
             * 2. Кнопка <input type="submit" name="params" value="Добавить" />
             *
             * При нажатии первой кнопки, вызывается метод validateForm(), который
             * проверяет введенные данные, и, если все в порядке, вызывает метод
             * модели updateGroup() для обновления функциональной группы. Если были
             * допущены ошибки при заполнении формы, введенные данные сохраняются
             * в сессии, чтобы после редиректа опять показать форму, заполненную
             * введенными ранее данными и сообщения об ошибках.
             *
             * При нажатии второй кнопки, вызывается метод addGroupParams(), который
             * временно «привязывает» к функциональной группе выбранные параметры.
             * Введенные данные сохраняются в сессии, чтобы администратору не пришлось
             * заполнять поля формы повторно.
             */
            if (isset($_POST['submit'])) { // нажата первая кнопка
                if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки
                    $this->redirect(
                        $this->filterBackendModel->getURL('backend/filter/editgroup/id/' . $this->params['id'])
                    );
                } else {
                    $this->redirect($this->filterBackendModel->getURL('backend/filter/allgroups'));
                }
            } else { // нажата вторая кнопка
                $this->addGroupParams();
                $this->redirect(
                    $this->filterBackendModel->getURL('backend/filter/editgroup/id/' . $this->params['id'])
                );
            }
        }

        $this->title = 'Редактирование группы. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->filterBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Фильтр',
                'url'  => $this->filterBackendModel->getURL('backend/filter/index')
            ),
            array(
                'name' => 'Группы',
                'url'  => $this->filterBackendModel->getURL('backend/filter/allgroups')
            ),
        );

        // получаем от модели массив параметров подбора для возможности привязки
        // параметров к функциональной группе
        $allParams = $this->filterBackendModel->getParams();

        // получаем от модели массив значений параметров подбора для возможности
        // привязки допустимых значений параметра к параметру
        $allValues = $this->filterBackendModel->getValues();

        // получаем от модели наименование редактируемой группы
        $name = $this->filterBackendModel->getGroupName($this->params['id']);
        // если запрошенная группа не найдена в БД
        if (empty($name)) {
            $this->notFoundRecord = true;
            return;
        }

        // массив (уже) привязанных к группе параметров подбора и массивы (уже)
        // привязанных к этим параметрам значений
        $linked_params = $this->filterBackendModel->getGroupParams($this->params['id']);

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'   => $breadcrumbs,
            // атрибут action тега form
            'action'        =>
                $this->filterBackendModel->getURL('backend/filter/editgroup/id/' . $this->params['id']),
            // уникальный идентификатор группы
            'id'            => $this->params['id'],
            // наименование группы
            'name'          => $name,
            // массив всех параметров подбора
            'allParams'     => $allParams,
            // массив значений параметров подбора
            'allValues'     => $allValues,
            // массив привязанных к группе параметров подбора
            // и привязанных к этим параметрам значений
            'linked_params' => $linked_params,
        );
        // если на предыдущем этапе администратор добавлял новые параметры для группы,
        // передаем в шаблон сохраненные в сессии данные формы
        if ($this->issetSessionData('newFilterGroupParams')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('newFilterGroupParams');
            $this->unsetSessionData('newFilterGroupParams');
        }
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные администратором данные
        if ($this->issetSessionData('editFilterGroupForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editFilterGroupForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editFilterGroupForm');
        }

    }

    /**
     * Функция проверяет корректность введенных администратором данных; если
     * были допущены ошибки, функция возвращает false; если ошибок нет, функция
     * обновляет функциональную группу и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // наименование группы
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 100));
        // параметры, привязанные к группе
        $data['linked_params'] = array();
        if (isset($_POST['params_values']) && is_array($_POST['params_values'])) {
            foreach ($_POST['params_values'] as $key => $value) {
                $name = $this->filterBackendModel->getParam($key);
                $ids = array();
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $ids[] = $v;
                    }
                }
                $data['linked_params'][] = array('id' => $key, 'name' => $name, 'ids' => $ids);
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
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editFilterGroupForm', $data);
            return false;
        }

        // параметры, привязанные к группе
        unset($data['linked_params']);
        $data['params_values'] = array();
        if (isset($_POST['params_values']) && is_array($_POST['params_values'])) {
            foreach ($_POST['params_values'] as $key => $value) {
                $data['params_values'][$key] = array();
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $data['params_values'][$key][] = $v;
                    }
                }
            }
        }

        // уникальный идентификатор группы
        $data['id'] = $this->params['id'];

        // обращаемся к модели для обновления группы
        $this->filterBackendModel->updateGroup($data);

        return true;

    }

    /**
     * Функция временно «привязывает» к функциональной группе выбранные параметры
     * и сохраняет в сессии все введенные данные, чтобы администратору не пришлось
     * заполнять поля формы повторно
     */
    private function addGroupParams() {

        /*
         * сохраняем введенные данные в сессии
         */

        // наименование группы
        $data['name'] = trim(utf8_substr($_POST['name'], 0, 100));
        // параметры, привязанные к группе
        $data['linked_params'] = array();
        // идентификаторы параметров, уже привязанных к группе: чтобы не привязывать их повторно
        $temp = array();
        if (isset($_POST['params_values']) && is_array($_POST['params_values'])) {
            foreach ($_POST['params_values'] as $key => $value) {
                $temp[] = $key;
                $name = $this->filterBackendModel->getParam($key);
                $ids = array();
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $ids[] = $v;
                    }
                }
                $data['linked_params'][] = array('id' => $key, 'name' => $name, 'ids' => $ids);
            }
        }
        if (isset($_POST['new_params']) && is_array($_POST['new_params'])) {
            foreach ($_POST['new_params'] as $id) {
                $id = (int)$id;
                if (in_array($id, $temp)) { // такой параметр уже есть
                    continue;
                }
                $name = $this->filterBackendModel->getParam($id);
                $data['linked_params'][] = array('id' => $id, 'name' => $name, 'ids' => array());
            }
        }

        $this->setSessionData('newFilterGroupParams', $data);

    }

}