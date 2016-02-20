<?php
/**
 * Класс Editprof_User_Frontend_Controller формирует страницу с формой для редактирования
 * профиля пользователя, обновляет запись в таблице БД profiles, получает данные от модели
 * User_Frontend_Model, общедоступная часть сайта
 */
class Editprof_User_Frontend_Controller extends User_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования страницы
     * с формой для редактирования профиля пользователя
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу User_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо)
         * и устанавливаем значения перменных, которые нужны для работы только
         * Editprof_User_Frontend_Controller
         */
        parent::input();

        // если пользователь не авторизован, перенаправляем его на страницу авторизации
        if ( ! $this->authUser) {
            $this->redirect($this->userFrontendModel->getURL('frontend/user/login'));
        }

        // если не передан id профиля или id профиля не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            $this->notFoundRecord = true;
            return;
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ( ! $this->validateForm()) { // если при заполнении формы были допущены ошибки, опять показываем форму
                $this->redirect($this->userFrontendModel->getURL('frontend/user/editprof/id/' . $this->params['id']));
            } else { // ошибок не было, профиль обновлён, перенаправляем пользователя на страницу со списком профилей
                $this->redirect($this->userFrontendModel->getURL('frontend/user/allprof'));
            }
        }

        $this->title = 'Редактирование профиля. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->userFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Личный кабинет',
                'url'  => $this->userFrontendModel->getURL('frontend/user/index')
            ),
            array(
                'name' => 'Ваши профили',
                'url'  => $this->userFrontendModel->getURL('frontend/user/allprof')
            ),
        );

        // получаем от модели информацию о профиле
        $profile = $this->userFrontendModel->getProfile($this->params['id']);
        // если запрошенный профиль не найден в БД
        if (empty($profile)) {
            $this->notFoundRecord = true;
            return;
        }

        // получаем от модели список офисов для самовывоза товара со склада
        $offices = $this->userFrontendModel->getOffices();

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs'      => $breadcrumbs,
            // атрибут action тега form
            'action'           => $this->userFrontendModel->getURL('frontend/user/editprof/id/' . $this->params['id']),
            // уникальный идентификатор профиля
            'id'               => $this->params['id'],
            // название профиля
            'title'            => $profile['title'],
            // имя контактного лица
            'name'             => $profile['name'],
            // фамилия контактного лица
            'surname'          => $profile['surname'],
            // e-mail контактного лица
            'email'            => $profile['email'],
            // телефон контактного лица
            'phone'            => $profile['phone'],
            // самовывоз со склада или доставка по адресу?
            'shipping'         => $profile['shipping'],
            // массив офисов для самовывоза
            'offices'          => $offices,
            // адрес доставки
            'shipping_address' => $profile['shipping_address'],
            // почтовый индекс
            'shipping_index'   => $profile['shipping_index'],
            // юридическое лицо?
            'company'          => $profile['company'],
            // название компании
            'company_name'     => $profile['company_name'],
            // генеральный директор
            'company_ceo'      => $profile['company_ceo'],
            // юридический адрес
            'company_address'  => $profile['company_address'],
            // ИНН
            'company_inn'      => $profile['company_inn'],
            // название банка
            'bank_name'        => $profile['bank_name'],
            // БИК
            'bik'              => $profile['bik'],
            // номер расчетного счета в банке
            'settl_acc'        => $profile['settl_acc'],
            // номер корреспондентского счета
            'corr_acc'         => $profile['corr_acc'],
        );
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('editUserProfileForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('editUserProfileForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('editUserProfileForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были допущены ошибки,
     * функция возвращает false; если ошибок нет, функция обновляет профиль и возвращает true
     */
    private function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */
        $data['title']   = trim(utf8_substr(strip_tags($_POST['title']), 0, 32));   // название профиля
        $data['surname'] = trim(utf8_substr(strip_tags($_POST['surname']), 0, 32)); // фамилия контактного лица
        $data['name']    = trim(utf8_substr(strip_tags($_POST['name']), 0, 32));    // имя контактного лица
        $data['email']   = trim(utf8_substr(strip_tags($_POST['email']), 0, 32));   // e-mail контактного лица
        $data['phone']   = trim(utf8_substr(strip_tags($_POST['phone']), 0, 32));   // телефон контактного лица

        if (isset($_POST['shipping'])) { // самовывоз со склада
            $data['shipping']         = 1;
            if (isset($_POST['office']) && in_array($_POST['office'], array(1,2,3,4))) {
                $data['shipping'] = $_POST['office'];
            }
            $data['shipping_address'] = '';
            $data['shipping_index']   = '';
        } else { // доставка по адресу
            $data['shipping']         = 0;
            $data['shipping_address'] = trim(utf8_substr(strip_tags($_POST['shipping_address']), 0, 250)); // адрес доставки
            $data['shipping_index']   = trim(utf8_substr(strip_tags($_POST['shipping_index']), 0, 32));    // почтовый индекс
        }
        
        $data['company']         = 0;
        $data['company_name']    = '';
        $data['company_ceo']     = '';
        $data['company_address'] = '';
        $data['company_inn']     = '';
        $data['bank_name']       = '';
        $data['bik']             = '';
        $data['settl_acc']       = '';
        $data['corr_acc']        = '';

        if (isset($_POST['company'])) { // юридическое лицо?
            $data['company']       = 1;
            $data['company_name']    = trim(utf8_substr(strip_tags($_POST['company_name']), 0, 64));     // название компании
            $data['company_ceo']     = trim(utf8_substr(strip_tags($_POST['ceo_name']), 0, 64));         // генеральный директор
            $data['company_address'] = trim(utf8_substr(strip_tags($_POST['company_address']), 0, 250)); // юридический адрес
            $data['company_inn']     = trim(utf8_substr(strip_tags($_POST['company_inn']), 0, 32));      // ИНН
            $data['bank_name']       = trim(utf8_substr(strip_tags($_POST['bank_name']), 0, 64));        // название банка
            $data['bik']             = trim(utf8_substr(strip_tags($_POST['bik']), 0, 32));              // БИК
            $data['settl_acc']       = trim(utf8_substr(strip_tags($_POST['settl_acc']), 0, 32));        // номер расчетного счета в банке
            $data['corr_acc']        = trim(utf8_substr(strip_tags($_POST['corr_acc']), 0, 32));         // номер корреспондентского счета
        }

        // были допущены ошибки при заполнении формы?
        if (empty($data['title'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Название профиля»';
        }
        if ($data['company']) { // для юридического лица
            if (empty($data['company_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название компании»';
            }
            if (empty($data['company_ceo'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор»';
            }
            if (empty($data['company_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес»';
            }
            if (empty($data['company_inn'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «ИНН»';
            }
            if (empty($data['bank_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название банка»';
            }
            if (empty($data['bik'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «БИК»';
            }
            if (empty($data['settl_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет»';
            }
            if (empty($data['corr_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Корреспондентский счет»';
            }
        }
        if (empty($data['surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица»';
        }
        if (empty($data['name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица»';
        }
        if (empty($data['email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица»';
        } elseif ( ! preg_match('#^[0-9a-z][-_.0-9a-z]*@[0-9a-z][-.0-9a-z]*\.[a-z]{2,6}$#i', $data['email'])) {
            $errorMessage[] = 'Поле «E-mail» должно соответствовать формату somebody@mail.ru';
        }
        if ( ! $data['shipping']) {
            if (empty($data['shipping_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Адрес доставки»';
            }
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if ( ! empty($errorMessage)) {
            $data['errorMessage'] = $errorMessage;
            $this->setSessionData('editUserProfileForm', $data);
            return false;
        }

        $data['id'] = $this->params['id']; // уникальный идентификатор профиля

        // обращаемся к модели для  профиля
        $this->userFrontendModel->updateProfile($data);

        return true;

    }

}
