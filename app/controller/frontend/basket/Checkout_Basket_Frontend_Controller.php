<?php
/**
 * Класс Checkout_Basket_Frontend_Controller формирует страницу с формой
 * для оформления заказа, получает данные от модели Basket_Frontend_Model,
 * общедоступная часть сайта
 */
class Checkout_Basket_Frontend_Controller extends Basket_Frontend_Controller {

    public function __construct($params = null) {
        parent::__construct($params);
    }

    /**
     * Функция получает от модели данные, необходимые для формирования
     * страницы с формой для оформления заказа
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Basket_Frontend_Controller,
         * чтобы установить значения переменных, которые нужны для работы всех
         * его потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы только
         * Checkout_Basket_Frontend_Controller
         */
        parent::input();

        // если корзина пуста, здесь делать нечего, за исключением того
        // случая, когда заказ только что был размещен
        if ($this->basketFrontendModel->isEmptyBasket() && (!$this->issetSessionData('successCheckoutOrder'))) {
            // перенаправляем пользователя на страницу корзины
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/index'));
        }

        // если данные формы были отправлены
        if ($this->isPostMethod()) {
            if ($this->validateForm()) { // заказ был создан, отмечаем этот факт
                $this->setSessionData('successCheckoutOrder', true);
            }
            // перенаправляем пользователя опять на страницу оформления заказа,
            // где он увидит либо форму с сообщениями об ошибках, допущенных при
            // заполнении формы, либо сообщение «Ваш заказ успешно создан»
            $this->redirect($this->basketFrontendModel->getURL('frontend/basket/checkout'));
        }

        $this->title = 'Оформление заказа. ' . $this->title;

        // формируем хлебные крошки
        $breadcrumbs = array(
            array(
                'name' => 'Главная',
                'url'  => $this->basketFrontendModel->getURL('frontend/index/index')
            ),
            array(
                'name' => 'Каталог',
                'url'  => $this->basketFrontendModel->getURL('frontend/catalog/index')
            ),
            array(
                'name' => 'Корзина',
                'url'  => $this->basketFrontendModel->getURL('frontend/basket/index')
            ),
        );

        // получаем от модели массив профилей пользователя
        $profiles = array();
        if ($this->authUser) {
            $profiles = $this->userFrontendModel->getUserProfiles();
        }

        // получаем от модели список офисов для самовывоза товара со склада
        $offices = $this->userFrontendModel->getOffices();

        // если true, данные формы успешно отправлены, заказ размещен
        $success  = false;
        if ($this->issetSessionData('successCheckoutOrder')) {
            $success = true;
            $this->unsetSessionData('successCheckoutOrder');
        }

        /*
         * массив переменных, которые будут переданы в шаблон center.php
         */
        $this->centerVars = array(
            // хлебные крошки
            'breadcrumbs' => $breadcrumbs,
            // атрибут action тега form
            'action'      => $this->basketFrontendModel->getURL('frontend/basket/checkout'),
            // пользователь авторизован?
            'authUser'    => $this->authUser,
            // массив профилей пользователя
            'profiles'    => $profiles,
            // массив офисов для самовывоза
            'offices'     => $offices,
            // если true, заказ размещен
            'success'     => $success,
        );
        if ($success) { // заказ размещен, большинство переменных в шаблоне не нужны
            unset(
                $this->centerVars['action'],
                $this->centerVars['authUser'],
                $this->centerVars['profiles'],
                $this->centerVars['offices']
            );
        }
        // если были ошибки при заполнении формы, передаем в шаблон массив сообщений
        // об ошибках и введенные пользователем данные, сохраненные в сессии
        if ($this->issetSessionData('checkoutOrderForm')) {
            $this->centerVars['savedFormData'] = $this->getSessionData('checkoutOrderForm');
            $this->centerVars['errorMessage'] = $this->centerVars['savedFormData']['errorMessage'];
            unset($this->centerVars['savedFormData']['errorMessage']);
            $this->unsetSessionData('checkoutOrderForm');
        }

    }

    /**
     * Функция проверяет корректность введенных пользователем данных; если были
     * допущены ошибки, функция возвращает false; если ошибок нет, функция создает
     * заказ и возвращает true
     */
    protected function validateForm() {

        /*
         * обрабатываем данные, полученные из формы
         */

        // имя контактного лица получателя
        $form['buyer_name']    = trim(utf8_substr(strip_tags($_POST['buyer_name']), 0, 32));
        // фамилия контактного лица получателя
        $form['buyer_surname'] = trim(utf8_substr(strip_tags($_POST['buyer_surname']), 0, 32));
        // e-mail контактного лица получателя
        $form['buyer_email']   = trim(utf8_substr(strip_tags($_POST['buyer_email']), 0, 32));
        // телефон контактного лица получателя
        $form['buyer_phone']   = trim(utf8_substr(strip_tags($_POST['buyer_phone']), 0, 32));

        if (isset($_POST['shipping'])) { // самовывоз
            $form['shipping']               = 1;
            if (isset($_POST['office']) && in_array($_POST['office'], array(1,2,3,4))) {
                $form['own_shipping'] = $_POST['office'];
            }
            $form['buyer_shipping_address'] = ''; // адрес доставки
            $form['buyer_shipping_city']    = ''; // город доставки
            $form['buyer_shipping_index']   = ''; // почтовый индекс
        } else { // доставка по адресу
            $form['shipping']               = 0;
            $form['buyer_shipping_address'] = trim(utf8_substr(strip_tags($_POST['buyer_shipping_address']), 0, 250));
            $form['buyer_shipping_city']    = trim(utf8_substr(strip_tags($_POST['buyer_shipping_city']), 0, 32));
            $form['buyer_shipping_index']   = trim(utf8_substr(strip_tags($_POST['buyer_shipping_index']), 0, 32));
        }

        if (isset($_POST['buyer_legal_person'])) { // получатель - юридическое лицо?
            // получатель - юридическое лицо
            $form['buyer_legal_person']  = 1;
            // название компании получателя
            $form['buyer_company']       = trim(utf8_substr(strip_tags($_POST['buyer_company']), 0, 64));
            // генеральный директор компании получателя
            $form['buyer_ceo_name']      = trim(utf8_substr(strip_tags($_POST['buyer_ceo_name']), 0, 64));
            // название компании получателя
            $form['buyer_legal_address'] = trim(utf8_substr(strip_tags($_POST['buyer_legal_address']), 0, 250));
            // название банка компании получателя
            $form['buyer_bank_name']     = trim(utf8_substr(strip_tags($_POST['buyer_bank_name']), 0, 64));
            // ИНН компании получателя
            $form['buyer_inn']           = trim(utf8_substr(strip_tags($_POST['buyer_inn']), 0, 32));
            // БИК компании получателя
            $form['buyer_bik']           = trim(utf8_substr(strip_tags($_POST['buyer_bik']), 0, 32));
            // номер расчетного счета в банке компании получателя
            $form['buyer_settl_acc']     = trim(utf8_substr(strip_tags($_POST['buyer_settl_acc']), 0, 32));
            // номер корреспондентского счета компании получателя
            $form['buyer_corr_acc']      = trim(utf8_substr(strip_tags($_POST['buyer_corr_acc']), 0, 32));
        } else {
            $form['buyer_legal_person']  = 0;  // получатель - не юридическое лицо
            $form['buyer_company']       = ''; // название компании получателя
            $form['buyer_ceo_name']      = ''; // генеральный директор компании получателя
            $form['buyer_legal_address'] = ''; // юридический адрес компании получателя
            $form['buyer_bank_name']     = ''; // название банка компании получателя
            $form['buyer_inn']           = ''; // ИНН компании получателя
            $form['buyer_bik']           = ''; // БИК компании получателя
            $form['buyer_settl_acc']     = ''; // номер расчетного счета в банке компании получателя
            $form['buyer_corr_acc']      = ''; // номер корреспондентского счета компании получателя
        }

        if (isset($_POST['buyer_payer_different'])) { // получатель и плательщик различаются?
            // получатель и плательщик различаются
            $form['buyer_payer_different'] = 1;
            // имя контактного лица плательщика
            $form['payer_name']                = trim(utf8_substr(strip_tags($_POST['payer_name']), 0, 32));
            // фамилия контактного лица плательщика
            $form['payer_surname']             = trim(utf8_substr(strip_tags($_POST['payer_surname']), 0, 32));
            // e-mail контактного лица плательщика
            $form['payer_email']               = trim(utf8_substr(strip_tags($_POST['payer_email']), 0, 32));
            // телефон контактного лица плательщика
            $form['payer_phone']               = trim(utf8_substr(strip_tags($_POST['payer_phone']), 0, 32));

            if (isset($_POST['payer_legal_person'])) { // плательщик - юридическое лицо?
                // плательщик - юридическое лицо
                $form['payer_legal_person']  = 1;
                // название компании плательщика
                $form['payer_company']       = trim(utf8_substr(strip_tags($_POST['payer_company']), 0, 64));
                // генеральный директор компании плательщика
                $form['payer_ceo_name']      = trim(utf8_substr(strip_tags($_POST['payer_ceo_name']), 0, 64));
                // юридический адрес компании плательщика
                $form['payer_legal_address'] = trim(utf8_substr(strip_tags($_POST['payer_legal_address']), 0, 250));
                // название банка компании плательщика
                $form['payer_bank_name']     = trim(utf8_substr(strip_tags($_POST['payer_bank_name']), 0, 64));
                // ИНН компании плательщика
                $form['payer_inn']           = trim(utf8_substr(strip_tags($_POST['payer_inn']), 0, 32));
                // БИК компании плательщика
                $form['payer_bik']           = trim(utf8_substr(strip_tags($_POST['payer_bik']), 0, 32));
                // номер расчетного счета в банке компании плательщика
                $form['payer_settl_acc']     = trim(utf8_substr(strip_tags($_POST['payer_settl_acc']), 0, 32));
                // номер корреспондентского счета компании плательщика
                $form['payer_corr_acc']      = trim(utf8_substr(strip_tags($_POST['payer_corr_acc']), 0, 32));
            } else {
                $form['payer_legal_person']  = 0;  // плательщик - не юридическое лицо
                $form['payer_company']       = ''; // название компании плательщика
                $form['payer_ceo_name']      = ''; // генеральный директор компании плательщика
                $form['payer_legal_address'] = ''; // юридический адрес компании плательщика
                $form['payer_bank_name']     = ''; // название банка компании плательщика
                $form['payer_inn']           = ''; // ИНН компании плательщика
                $form['payer_bik']           = ''; // БИК компании плательщика
                $form['payer_settl_acc']     = ''; // номер расчетного счета в банке компании плательщика
                $form['payer_corr_acc']      = ''; // номер корреспондентского счета компании плательщика
            }
        } else {
            // плательщик и получатель не различаются
            $form['buyer_payer_different'] = 0;
            // контактное лицо
            $form['payer_name']            = $form['buyer_name'];
            $form['payer_surname']         = $form['buyer_surname'];
            $form['payer_email']           = $form['buyer_email'];
            $form['payer_phone']           = $form['buyer_phone'];
            // юридическое лицо
            $form['payer_legal_person']    = $form['buyer_legal_person'];
            $form['payer_company']         = $form['buyer_company'];
            $form['payer_ceo_name']        = $form['buyer_ceo_name'];
            $form['payer_legal_address']   = $form['buyer_legal_address'];
            $form['payer_bank_name']       = $form['buyer_bank_name'];
            $form['payer_inn']             = $form['buyer_inn'];
            $form['payer_bik']             = $form['buyer_bik'];
            $form['payer_settl_acc']       = $form['buyer_settl_acc'];
            $form['payer_corr_acc']        = $form['buyer_corr_acc'];
        }
        // комментарий к заказу
        $form['comment'] = trim(utf8_substr(strip_tags($_POST['comment']), 0, 250));

        /*
         * были допущены ошибки при заполнении формы?
         */
        if (empty($form['buyer_name'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица получателя»';
        }
        if (empty($form['buyer_surname'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица получателя»';
        }
        if (empty($form['buyer_email'])) {
            $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица получателя»';
        }
        if ( ! $form['shipping']) { // если не самовывоз, должно быть заполнено поле «Адрес»
            if (empty($form['buyer_shipping_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Адрес доставки»';
            }
        }
        // если получатель - юридическое лицо
        if ($form['buyer_legal_person']) {
            if (empty($form['buyer_company'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название компании получателя»';
            }
            if (empty($form['buyer_ceo_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор компании получателя»';
            }
            if (empty($form['buyer_legal_address'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес компании получателя»';
            }
            if (empty($form['buyer_inn'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «ИНН компании получателя»';
            }
            if (empty($form['buyer_bank_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Название банка компании получателя»';
            }
            if (empty($form['buyer_bik'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «БИК компании получателя»';
            }
            if (empty($form['buyer_settl_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет компании получателя»';
            }
            if (empty($form['buyer_corr_acc'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Корреспондентский счет компании получателя»';
            }
        }
        // если плательщик и получатель различаются
        if ($form['buyer_payer_different']) {
            if (empty($form['payer_name'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Имя контактного лица плательщика»';
            }
            if (empty($form['payer_surname'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «Фамилия контактного лица плательщика»';
            }
            if (empty($form['payer_email'])) {
                $errorMessage[] = 'Не заполнено обязательное поле «E-mail контактного лица плательщика»';
            }
            // если плательщик - юридическое лицо
            if ($form['payer_legal_person']) {
                if (empty($form['payer_company'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Название компании плательщика»';
                }
                if (empty($form['payer_ceo_name'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Генеральный директор компании плательщика»';
                }
                if (empty($form['payer_legal_address'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Юридический адрес компании плательщика»';
                }
                if (empty($form['payer_inn'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «ИНН компании плательщика»';
                }
                if (empty($form['payer_bank_name'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Название банка компании плательщика»';
                }
                if (empty($form['payer_bik'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «БИК компании плательщика»';
                }
                if (empty($form['payer_settl_acc'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Расчетный счет компании плательщика»';
                }
                if (empty($form['payer_corr_acc'])) {
                    $errorMessage[] = 'Не заполнено обязательное поле «Корреспондентский счет компании плательщика»';
                }
            }
        }

        /*
         * были допущены ошибки при заполнении формы, сохраняем введенные
         * пользователем данные, чтобы после редиректа снова показать форму,
         * заполненную введенными ранее даннными и сообщением об ошибке
         */
        if (!empty($errorMessage)) {
            $form['errorMessage'] = $errorMessage;
            $this->setSessionData('checkoutOrderForm', $form);
            return false;
        }

        // создать профиль получателя?
        if ($this->authUser && isset($_POST['make_buyer_profile'])) {
            $data = array(
                'title'            => 'Профиль получателя',
                'name'             => $form['buyer_name'],
                'surname'          => $form['buyer_surname'],
                'email'            => $form['buyer_email'],
                'phone'            => $form['buyer_phone'],
                'shipping'         => $form['shipping'],
                'shipping_address' => $form['buyer_shipping_address'],
                'shipping_city'    => $form['buyer_shipping_city'],
                'shipping_index'   => $form['buyer_shipping_index'],
                'legal_person'     => $form['buyer_legal_person'],
                'company'          => $form['buyer_company'],
                'ceo_name'         => $form['buyer_ceo_name'],
                'legal_address'    => $form['buyer_legal_address'],
                'bank_name'        => $form['buyer_bank_name'],
                'inn'              => $form['buyer_inn'],
                'bik'              => $form['buyer_bik'],
                'settl_acc'        => $form['buyer_settl_acc'],
                'corr_acc'         => $form['buyer_corr_acc'],
            );
            // создаем профиль получателя
            $this->userFrontendModel->addProfile($data);
        }
        // создать профиль плательщика?
        if ($this->authUser && $form['buyer_payer_different'] && isset($_POST['make_payer_profile'])) {
            $data = array(
                'title'            => 'Профиль плательщика',
                'name'             => $form['payer_name'],
                'surname'          => $form['payer_surname'],
                'email'            => $form['payer_email'],
                'phone'            => $form['payer_phone'],
                'shipping'         => 1,
                'shipping_address' => '',
                'shipping_city'    => '',
                'shipping_index'   => '',
                'legal_person'     => $form['payer_legal_person'],
                'company'          => $form['payer_company'],
                'ceo_name'         => $form['payer_ceo_name'],
                'legal_address'    => $form['payer_legal_address'],
                'bank_name'        => $form['payer_bank_name'],
                'inn'              => $form['payer_inn'],
                'bik'              => $form['payer_bik'],
                'settl_acc'        => $form['payer_settl_acc'],
                'corr_acc'         => $form['payer_corr_acc'],
            );
            // создаем профиль плательщика
            $this->userFrontendModel->addProfile($data);
        }

        // обращаемся к модели корзины для создания заказа
        $result = $this->basketFrontendModel->createOrder($form);

        if ( ! $result) {
            $form['errorMessage'] = array('Произошла ошибка при добавлении заказа, попробуйте еще раз');
            $this->setSessionData('checkoutOrderForm', $form);
            return false;
        }

        return true;

    }

}