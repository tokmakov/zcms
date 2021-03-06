<?php
/**
 * Класс Edit_User_Backend_Controller для редактирования личных данных пользователя
 * (имя, пароль, e-mail), формирует страницу с формой для редактирования, обновляет
 * запись в таблице БД users, работает с моделью User_Backend_Model
 */
class Edit_User_Backend_Controller extends User_Backend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования личных данных пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Backend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Edit_User_Backend_Controller
         */
        parent::input();

        // если не передан id пользователя или id пользователя не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->ValidateForm()) { // при заполнении формы были допущены ошибки, возвращаемся на страницу с формой
                $this->redirect($this->userBackendModel->getURL('backend/user/edit/id/' . $this->params['id']));
            } else { // ошибок не было, пользователь был добавлен, возвращаемся на страницу со списком пользователей
                $this->redirect($this->userBackendModel->getURL('backend/user/index'));
            }
        }

        $this->title = 'Редактирование личных данных. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userBackendModel->getURL('backend/index/index')
            ),
            array(
                'name' => 'Пользователи',
                'url'  => $this->userBackendModel->getURL('backend/user/index')
            ),
        );

        // получаем от модели личные данные пользователя
        $user = $this->userBackendModel->getUser($this->params['id']);
        if (empty($user)) {
            $this->notFoundRecord = true;
            return;
        }

        // типы пользователей, для возможности выбора
        $types = $this->userBackendModel->getUserTypes();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->userBackendModel->getURL('backend/user/edit/id/' . $this->params['id']),
            // фамилия пользовтеля
            'surname'     => $user['surname'],
            // имя пользовтеля
            'name'        => $user['name'],
            // отчество пользовтеля
            'patronymic'  => $user['patronymic'],
            // тип пользователя
            'type'        => $user['type'],
            // типы пользователей, для возможности выбора
            'types'       => $types,
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('editUserForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editUserForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editUserForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет пользователя и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['surname']    = trim(iconv_substr($_POST['surname'], 0, 32));    // фамилия пользователя
        $data['name']       = trim(iconv_substr($_POST['name'], 0, 32));       // имя пользователя
        $data['patronymic'] = trim(iconv_substr($_POST['patronymic'], 0, 32)); // отчество пользователя
        $data['change']     = false;
        if (isset($_POST['change'])) { // изменить пароль?
            $data['change']   = true;
            $data['password'] = trim(iconv_substr($_POST['password'], 0, 32)); // пароль
            $confirm          = trim(iconv_substr($_POST['confirm'], 0, 32));  // подтверждение пароля
        }

        $data['type'] = (int)$_POST['type']; // тип пользователя

        // были допущены ошибки при заполнении формы?
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя»';
        }
        if (empty($data['surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия»';
        }
        if ($data['change']) { // изменить пароль?
            if (empty($data['password'])) {
                $errorMessage[] = 'Не заполнено поле «Пароль»';
            }
            if (empty($confirm)) {
                $errorMessage[] = 'Не заполнено поле «Подтвердите пароль»';
            }
            if ( (!empty($data['password'])) && (!empty($confirm)) && ($data['password'] != $confirm) ) {
                $errorMessage[] = 'Не совпадают пароли';
            }
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editUserForm', $data);
            return false;
        }

        if ($data['change']) { // изменить пароль?
            // добавляем к паролю префикс и хэшируем пароль
            $data['password'] = md5($this->config->user->prefix . $data['password']);
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор пользователя

        // обращаемся к модели для обновления личных данных пользователя
        $this->userBackendModel->updateUser($data);

        return true;
    }
}
