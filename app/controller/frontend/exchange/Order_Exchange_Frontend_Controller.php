<?php
/**
 * Класс Order_Exchange_Frontend_Controller формирует XML с информацией об отдельном заказе
 * в магазине, получает данные от модели Exchange_Frontend_Model, общедоступная часть сайта
 */
class Order_Exchange_Frontend_Controller extends Exchange_Frontend_Controller {

    /**
     * информация об отдельном заказе в магазине в формате XML
     */
    private $output;


    public function __construct($params = null) {
        parent::__construct($params);
    }

    public function request() {

        // если не передан id заказа или id заказа не число
        if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        } else {
            $this->params['id'] = (int)$this->params['id'];
        }

        // получаем от модели информацию о заказе
        $order = $this->exchangeFrontendModel->getOrder($this->params['id']);

        // если запрошенный заказ не найден в БД
        if (empty($order)) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            die();
        }

        /*
         * <order status="1">
         *   <user id="15">
         *     <name>...</name>
         *     <surname>...</surname>
         *     <email>...</email>
         *   </user>
         *   <getter>
         *     <name>...</name>
         *     <surname>...</surname>
         *     <email>...</email>
         *     <phone>...</phone>
         *     <shipping office="0">
         *       <address>...</address>
         *       <city/>
         *       <index>...<index>
         *     </shipping>
         *     <company>
         *       <name>...</name>
         *       <ceo>...</ceo>
         *       <address>...</address>
         *       <inn>...</inn>
         *       <bank>...</bank>
         *       <bik>...</bik>
         *       <settl>...</settl>
         *       <corr>...</corr>
         *     </company>
         *   </getter>
         *   <payer>
         *     <name>...</name>
         *     <surname>...</surname>
         *     <email>...</email>
         *     <phone/>
         *     <company>
         *       <name>...</name>
         *       <ceo>...</ceo>
         *       <address>...</address>
         *       <inn>...</inn>
         *       <kpp>...</kpp>
         *       <bank>...</bank>
         *       <bik>...</bik>
         *       <settl>...</settl>
         *       <corr>...</corr>
         *     </company>
         *   </payer>
         *   <products>
         *     <product code="020151" quantity="3"/>
         *     <product code="020259" quantity="2"/>
         *     <product code="023002" quantity="1"/>
         *   </products>
         *   <comment><![CDATA[ ... ]]></comment>
         * </order>
         */

        /*
         * Если заказ сделал не зарегистрированный пользователь, атрибут id
         * элемента <user> равен нулю, а сам элемент <user> не содержит
         * дочерних элементов. В противном случае у элемента <user> три
         * дочерних элемента: <name>, <surname> и <email>, а атрибут id
         * содержит уникальный идентификатор зарегистрированного пользователя.
         *
         * Если выбран способ доставки - самовывоз со склада, атрибут office
         * элемента <shipping> равен 1, 2, 3 или 4:
         * 1 - центральный офис
         * 2 - офис продаж «Сокол»
         * 3 - офис продаж «Мещанский»
         * 4 - офис продаж «Нагорный»
         * а сам элемент <shipping> не содержит дочерних элементов. Если
         * доставка по адресу, у элемента <shipping> три дочерних элемента:
         * <address>, <city> и <index>, а атрибут office равен нулю.
         *
         * Если получатель - физическое лицо, элемент <company> не содержит
         * дочерних элементов. Если получатель - юридическое лицо, у элемента
         * <company> девять дочерних элементов: <name>, <ceo>, <address>,
         * <inn>, <kpp>, <bank>, <bik>, <settl> и <corr>.
         *
         * Если получатель и плательщик различаются, у элемента <payer> пять
         * дочерних элементов: <name>, <surname>, <email>, <phone> и <company>.
         * В противном случае элемент <payer> не содержит дочерних элементов.
         *
         * Если плательщик - физическое лицо, элемент <company> не содержит
         * дочерних элементов. Если плательщик - юридическое лицо, у элемента
         * <company> девять дочерних элементов: <name>, <ceo>, <address>, <inn>,
         * <kpp>, <bank>, <bik>, <settl> и <corr>.
         */

        // создаём XML-документ
        $dom = new DOMDocument('1.0', 'utf-8');
        // создаём корневой элемент <order>
        $root = $dom->createElement('order');
        $dom->appendChild($root);
        // устанавливаем атрибут status для узла <order>
        $root->setAttribute('status', $order['status']);
        /*
         * ПОЛЬЗОВАТЕЛЬ
         */
        // создаем элемент <user>
        $user = $dom->createElement('user');
        $root->appendChild($user);
        // устанавливаем атрибут id для узла <user>
        $user->setAttribute('id', $order['user_id']);
        // если это зарегистрированный пользователь
        if ( ! empty($order['user_id'])) {
            // создаем узел <name>
            $name = $dom->createElement('name', $order['user_name']);
            $user->appendChild($name);
            // создаем узел <surname>
            $surname = $dom->createElement('surname', $order['user_surname']);
            $user->appendChild($surname);
            // создаем узел <email>
            $email = $dom->createElement('email', $order['user_email']);
            $user->appendChild($email);
        }
        /*
         * ПОЛУЧАТЕЛЬ
         */
        // создаем элемент <getter>, получатель заказа
        $getter = $dom->createElement('getter');
        $root->appendChild($getter);
        // создаем узел <name>, имя контактного лица получателя
        $order['getter_name'] = isset($order['getter_name']) ? $order['getter_name'] : '';
        $name = $dom->createElement('name', $order['getter_name']);
        $getter->appendChild($name);
        // создаем узел <surname>, фамилия контактного лица получателя
        $order['getter_surname'] = isset($order['getter_surname']) ? $order['getter_surname'] : '';
        $surname = $dom->createElement('surname', $order['getter_surname']);
        $getter->appendChild($surname);
        // создаем узел <email>, e-mail контактного лица получателя
        $order['getter_email'] = isset($order['getter_email']) ? $order['getter_email'] : '';
        $email = $dom->createElement('email', $order['getter_email']);
        $getter->appendChild($email);
        // создаем узел <phone>, телефон контактного лица получателя
        $order['getter_phone'] = isset($order['getter_phone']) ? $order['getter_phone'] : '';
        $phone = $dom->createElement('phone', $order['getter_phone']);
        $getter->appendChild($phone);
        // создаем узел <shipping>, способ доставки
        $shipping = $dom->createElement('shipping');
        $getter->appendChild($shipping);
        // устанавливаем атрибут office для узла <shipping>
        $order['shipping'] = isset($order['shipping']) ? $order['shipping'] : 1;
        $shipping->setAttribute('office', $order['shipping']);
        $getter->appendChild($shipping);
        if ( ! $order['shipping']) { // доставка по адресу
            // создаем узел <address>, адрес доставки
            $address = $dom->createElement('address', $order['getter_shipping_address']);
            $shipping->appendChild($address);
            // создаем узел <city>, город доставки
            $city = $dom->createElement('city', $order['getter_shipping_city']);
            $shipping->appendChild($city);
            // создаем узел <index>, почтовый индекс
            $index = $dom->createElement('index', $order['getter_shipping_index']);
            $shipping->appendChild($index);
        }
        // создаем узел <company>
        $company = $dom->createElement('company');
        $getter->appendChild($company);
        $order['getter_company'] = isset($order['getter_company']) ? $order['getter_company'] : 0;
        if ($order['getter_company']) { // если получатель - юридическое лицо
            // создаем узел <name>, название компании-получателя
            $name = $dom->createElement('name', $order['getter_company_name']);
            $company->appendChild($name);
            // создаем узел <ceo>, имя генерального директора компании-получателя
            $ceo = $dom->createElement('ceo', $order['getter_company_ceo']);
            $company->appendChild($ceo);
            // создаем узел <address>, юридический адрес компании-получателя
            $address = $dom->createElement('address', $order['getter_company_address']);
            $company->appendChild($address);
            // создаем узел <inn>, ИНН компании-получателя
            $inn = $dom->createElement('inn', $order['getter_company_inn']);
            $company->appendChild($inn);
            // создаем узел <kpp>, КПП компании-получателя
            $kpp = $dom->createElement('kpp', $order['getter_company_kpp']);
            $company->appendChild($kpp);
            // создаем узел <bank>, название банка компании-получателя
            $bank = $dom->createElement('bank', $order['getter_bank_name']);
            $company->appendChild($bank);
            // создаем узел <bik>, БИК банка компании-получателя
            $bik = $dom->createElement('bik', $order['getter_bank_bik']);
            $company->appendChild($bik);
            // создаем узел <settl>, расчетный счет компании-получателя
            $settl = $dom->createElement('settl', $order['getter_settl_acc']);
            $company->appendChild($settl);
            // создаем узел <corr>, корреспондентский счет компании-получателя
            $corr = $dom->createElement('corr', $order['getter_corr_acc']);
            $company->appendChild($corr);
        }
        /*
         * ПЛАТЕЛЬЩИК
         */
        // создаем узел <payer>
        $payer = $dom->createElement('payer');
        $root->appendChild($payer);
        $order['getter_payer_different']
            = isset($order['getter_payer_different']) ? $order['getter_payer_different'] : 0;
        if ($order['getter_payer_different']) { // получатель и плательщик различаются
            // создаем узел <name>, имя контактного лица плательщика
            $name = $dom->createElement('name', $order['payer_name']);
            $payer->appendChild($name);
            // создаем узел <surname>, фамилия контактного лица плательщика
            $surname = $dom->createElement('surname', $order['payer_surname']);
            $payer->appendChild($surname);
            // создаем узел <email>, e-mail контактного лица плательщика
            $email = $dom->createElement('email', $order['payer_email']);
            $payer->appendChild($email);
            // создаем узел <phone>, телефон контактного лица плательщика
            $phone = $dom->createElement('phone', $order['payer_phone']);
            $payer->appendChild($phone);
            // создаем узел <company>
            $company = $dom->createElement('company');
            $payer->appendChild($company);
            if ($order['payer_company']) { // если плательщик - юридическое лицо
                // создаем узел <name>, название компании-плательщика
                $name = $dom->createElement('name', $order['payer_company_name']);
                $company->appendChild($name);
                // создаем узел <ceo>, имя генерального директора компании-плательщика
                $ceo = $dom->createElement('ceo', $order['payer_company_ceo']);
                $company->appendChild($ceo);
                // создаем узел <address>, юридический адрес компании-плательщика
                $address = $dom->createElement('address', $order['payer_company_address']);
                $company->appendChild($address);
                // создаем узел <inn>, ИНН компании-плательщика
                $inn = $dom->createElement('inn', $order['payer_company_inn']);
                $company->appendChild($inn);
                // создаем узел <kpp>, КПП компании-плательщика
                $kpp = $dom->createElement('kpp', $order['payer_company_kpp']);
                $company->appendChild($kpp);
                // создаем узел <bank>, название банка компании-плательщика
                $bank = $dom->createElement('bank', $order['payer_bank_name']);
                $company->appendChild($bank);
                // создаем узел <bik>, БИК банка компании-плательщика
                $bik = $dom->createElement('bik', $order['payer_bank_bik']);
                $company->appendChild($bik);
                // создаем узел <settl>, расчетный счет компании-плательщика
                $settl = $dom->createElement('settl', $order['payer_settl_acc']);
                $company->appendChild($settl);
                // создаем узел <corr>, корреспондентский счет компании-плательщика
                $corr = $dom->createElement('corr', $order['payer_corr_acc']);
                $company->appendChild($corr);
            }
        }
        /*
         * ТОВАРЫ
         */
        // создаем элемент <products>, заказанные товары
        $products = $dom->createElement('products');
        $root->appendChild($products);
        foreach ($order['products'] as $value) {
            // создаём узел <product>
            $product = $dom->createElement('product');
            // устанавливаем атрибут code для узла <product>
            $product->setAttribute('code', $value['code']);
            // устанавливаем атрибут quantity для узла <product>
            $product->setAttribute('quantity', $value['quantity']);
            // добавляем дочерний элемент для <products>
            $products->appendChild($product);
        }
        /*
         * КОММЕНТАРИЙ
         */
        // создаем элемент <comment>, комментарий к заказу
        $comment = $dom->createElement('comment');
        if ( ! empty($order['comment'])) {
            // создаем текстовой узел внутри конструкции <![CDATA[ ... ]]>
            $text = $dom->createCDATASection($order['comment']);
            // добавляем текстовой узел для <comment>
            $comment->appendChild($text);
        }
        $root->appendChild($comment);

        $this->output = $dom->saveXML();

    }

    public function getContentLength() {
        return strlen($this->output);
    }

    public function sendHeaders() {
        header('Content-Type: text/xml; charset=utf-8');
        header('Content-Length: ' . $this->getContentLength());
    }

    public function getPageContent() {
        return $this->output;
    }

}
