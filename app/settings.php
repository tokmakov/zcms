<?php
defined('ZCMS') or die('Access denied');

$settings = array(
    'site' => array(
        'url'   => 'http://www.host2.ru/',
        'name'  => 'Торговый Дом ТИНКО',
        'phone' => '+7 (495) 708-42-13',
        'email' => 'tinko@tinko.ru',
        'theme' => 'view/tinko', // путь к папке с темой
    ),
    'admin' => array(
        'name'     => 'admin',
        'password' => 'qwerty',
    ),
    'meta' => array(
        'default'   => array( // значения по умолчанию для title, мета-тегов keywords и description
            'title'       => 'Торговый Дом ТИНКО. Технические средства безопасности',
            'keywords'    => 'поставки оборудования, купить, цена, охранно-пожарная сигнализация, охранное телевидение, системы контроля доступа, оповещение, кабель, провод, пожаротушение',
            'description' => 'Торговый Дом ТИНКО. Технические средства безопасности. Комплексные поставки оборудования: охранно-пожарная сигнализация, системы видеонаблюдения, системы контроля доступа, средства пожаротушения.',
        ),
        'article'   => array( // значения по умолчанию для статей
            'title'       => 'Статьи. Журнал «Грани безопасности». Торговый Дом ТИНКО',
            'keywords'    => 'статьи, журнал, ОПС, видеонаблюдение, СКУД, домофон, оповещение, кабель, провод, пожаротушение',
            'description' => 'Статьи. Журнал «Грани безопасности». Торговый Дом ТИНКО. Технические средства безопасности. Комплексные поставки оборудования. ОПС, охранное телевидение, СКУД, средства пожаротушения.',
        ),
        'blog'      => array( // значения по умолчанию для блога
            'title'       => 'Новости, события, выставки. Торговый Дом ТИНКО',
            'keywords'    => 'новости, события, выставка, ОПС, видеонаблюдение, СКУД, домофон, оповещение, кабель, провод, пожаротушение',
            'description' => 'Новости, события, выставки. Торговый Дом ТИНКО. Технические средства безопасности. Комплексные поставки оборудования. ОПС, охранное телевидение, СКУД, средства пожаротушения.',
        ),
        'catalog'   => array( // значения по умолчанию для каталога
            'title'       => 'Каталог оборудования. Торговый Дом ТИНКО',
            'keywords'    => 'каталог, оборудование, купить, цена, доставка, ОПС, видеонаблюдение, СКУД, домофон, оповещение, кабель, провод, пожаротушение',
            'description' => 'Каталог оборудования: охранно-пожарная сигнализация, охранное телевидение, контроль доступа, домофоны, средства оповещения, кабели и провода, системы пожаротушения.',
        ),
        'solutions' => array( // значения по умолчанию для типовых решений
            'title'       => 'Типовые решения. Торговый Дом ТИНКО',
            'keywords'    => 'типовые решения, проект, оборудование, комплект, технические средства безопасности, ОПС, видеонаблюдение, СКУД, оповещение, кабель, провод, пожаротушение',
            'description' => 'Типовые решения. Торговый Дом ТИНКО. Технические средства безопасности. Охранно-пожарная сигнализация, системы видеонаблюдения, системы контроля доступа, средства пожаротушения.',
        ),
        'sale'      => array( // значения по умолчанию для распродажи
            'title'       => 'Распродажа. Торговый Дом ТИНКО',
            'keywords'    => 'распродажа, скидка, оборудование, технические средства безопасности, ОПС, видеонаблюдение, СКУД, оповещение, кабель, провод, пожаротушение',
            'description' => 'Распродажа. Торговый Дом ТИНКО. Технические средства безопасности. Охранно-пожарная сигнализация, системы видеонаблюдения, системы контроля доступа, средства пожаротушения.',
        ),
        'rating'    => array( // значения по умолчанию для рейтинга продаж
            'title'       => 'Рейтинг продаж. Торговый Дом ТИНКО',
            'keywords'    => 'рейтинг, продажи, оборудование, технические средства безопасности, ОПС, видеонаблюдение, СКУД, оповещение, кабель, провод, пожаротушение',
            'description' => 'Рейтинг продаж. Торговый Дом ТИНКО. Технические средства безопасности. Охранно-пожарная сигнализация, системы видеонаблюдения, системы контроля доступа, средства пожаротушения.',
        ),
        'page'      => array( // значения по умолчанию для страниц
            'title'       => 'Средства безопасности. Торговый Дом ТИНКО',
            'keywords'    => 'технические средства безопасности, оборудование, ОПС, видеонаблюдение, СКУД, оповещение, кабель, провод, пожаротушение',
            'description' => 'Торговый Дом ТИНКО. Технические средства безопасности. Комплексные поставки оборудования: охранно-пожарная сигнализация, системы видеонаблюдения, системы контроля доступа, средства пожаротушения.',
        ),
    ),
    'database' => array(                         // соединение с базой данных
        'pcon' => false,                         // постоянное соединение
        'host' => 'localhost',
        'user' => 'root',
        'pass' => 'wbmstr',
        'name' => 'zcms2',
    ),
    'cache' => array(
        'enable' => array(
            'data' => false,                     // кэширование данных разрешено?
            'html' => false,                     // кэширование шаблонов разрешено?
        ),
        'file'   => array(                       // кэширование с использованием файлов
            'time' => 7200,                      // время храниения кэша в секундах
            'lock' => 10,                        // максимальное время блокировки на чтение в секундах
            'dir'  => 'cache',                   // директория для хранения файлов кэша
        ),
        'mem'    => array(                       // кэширование с использованием Memcached
            'time' => 3600,                      // время храниения кэша в секундах
            'lock' => 10,                        // максимальное время блокировки на чтение в секундах
            'host' => 'localhost',
            'port' => 11211,
        ),
    ),
    'sef' => $routing,                           // см. файл routing.php
    'email' => array(
        'admin' => 'tokmakov.e@mail.ru',         // e-mail администратора сайта
        'order' => 'tokmakov-e@yandex.ru',       // на этот адрес будут приходить письма о заказах
        'site'  => 'tinko@tinko.ru',             // с этого адреса будут отправляться письма
    ),
    'error' => array(
        'debug'    => true,                      // должен быть true на этапе разработки
        'write'    => true,                      // записывать сообщения об ошибках в журнал?
        'file'     => 'error.log.txt',           // файл журнала ошибок
        'sendmail' => false,                     // отправлять сообщения об ошибках на почту?
        // общее сообщение об ошибке, которое должно отображаться
        // вместо подробной информации (если debug равно false)
        'message'  => 'Произошла ошибка, сообщение об ошибке отправлено администратору.',
    ),
    'message' => array( // информационные сообщения для пользователей
        'error'    => 'Произошла ошибка, сообщение об ошибке отправлено администратору.',
        'checkout' => 'Ваш заказ успешно создан, наш менеджер свяжется с Вами в ближайшем будущем.',
    ),
    'css' => array(                              // CSS файлы, подключаемые к странице
        'frontend'            => array(          // общедоступная часть сайта
            'base'            => array(          // CSS-файлы, подключаемые ко всем страницам
                'reset.css',
                'common.css',
                'awesome/font-awesome.min.css',
            ),
            'index'           => array(          // главная страница сайта
                'index.css',
                'jquery.bxslider.css',
                'tabs.css',
            ),
            'article'         => 'article.css',  // статьи
            'blog'            => array(          // блог
                'blog.css',
                'fancybox/jquery.fancybox.css',
            ),
            'catalog'         => 'fancybox/jquery.fancybox.css', // каталог товаров
            'compare-index'   => 'compare-index.css', // сравнение товаров
            'compare-table'   => 'compare-table.css', // сравнение товаров
            'rating'          => 'rating.css',   // рейтинг продаж
            'sale'            => 'sale.css',     // распродажа
            'user'            => 'user.css',     // личный кабинет
            'solutions'       => array(          // типовые решения
                'solutions.css',
                'fancybox/jquery.fancybox.css',
            ),
            'basket-index'    => 'basket-index.css', // покупательская корзина
            'basket-checkout' => 'basket-checkout.css', // оформление заказа
            'sitemap'         => 'sitemap.css',  // карта сайта

            'page-40'         => array(          // для страницы «Контакты»
                'tabs.css',
                'page/contacts.css',
                'fancybox/jquery.fancybox.css',
            ),
            'page-39'          => array(         // для страницы «О компании»
                'page/about.css',
                'fancybox/jquery.fancybox.css',
            ),
            'page-41'         => array(          // для страницы «Доставка»
                'tabs.css',
            ),
            'page-49'         => array(          // для страницы «Консультанты»
                'page/consultants.css',
            ),
            'page-51'         => array(          // для страницы «Партнеры»
                'page/partners.css',
                'fancybox/jquery.fancybox.css',
            ),
            'page-52'         => array(          // для страницы «Библиотека»
                'page/library.css',
            ),
            'page-53'         => array(          // для страницы «Госзакупки»
                'page/trading.css',
            ),
            'page-54'         => array(          // для страницы «Грани безопасности»
                'page/journal.css',
            ),

            /*
             * ПРИМЕР ПОДКЛЮЧЕНИЯ ФАЙЛОВ, НЕ УДАЛЯТЬ!
             * 'base' => array(                // css-файлы, подключаемые ко всем страницам сайта
             *     'reset.css',
             *     'common.css',
             * ),
             * 'index' => 'jquery.slider.css', // только для главной страницы, формируемой Index_Index_Frontend_Controller
             * 'page' => 'page.css',           // для страниц, которые формирует Index_Page_Frontend_Controller
             * 'catalog' => 'catalog.css',     // для страниц, которые все формируют дочерние классы Catalog_Frontend_Controller
             * 'catalog-product' => array(     // только для страниц, которые формирует Product_Catalog_Frontend_Controller
             *     'product.css',
             *     'jquery.lightbox.css',
             * ),
             *
             * Здесь важно понимать, что у некоторых абстактных классов есть только один дочерний класс,
             * например: Page_Frontend_Controller и Index_Page_Frontend_Controller. А у других абстрактных
             * классов есть несколько дочерних классов, например: Catalog_Frontend_Controller и
             * 1. Index_Catalog_Frontend_Controller
             * 2. Product_Catalog_Frontend_Controller
             * 3. Category_Catalog_Frontend_Controller
             * 4. Maker_Catalog_Frontend_Controller
             *
             * Запись вида
             * 'catalog' => 'catalog.css', // для всех страниц каталога
             * 'catalog-index' => 'catalog-index.css' // только для главной страницы каталога
             * имеет смысл, а запись вида
             * 'page' => 'page.css'
             * 'page-index' => 'lightbox.css'
             * не будет ошибочной, но сбивает с толку, поэтому лучше так:
             * 'page' => array(
             *     'page.css',
             *     'lightbox.css'
             * )
             */

        ),
        'backend' => array( // административная часть сайта
            'base'      => array(
                'reset.css',
                'common.css',
                'awesome/font-awesome.min.css',
            ),
            'index'     => array(
                'blog.css',
                'order.css',
            ),
            'admin'     => 'admin.css',
            'article'   => 'article.css',
            'banner'    => 'banner.css',
            'blog'      => array (
                'blog.css',
                'tabs.css',
            ),
            'catalog'   => 'catalog.css',
            'filter'    => array(
                'filter.css',
                'tabs.css',
                'multi-select.css',
            ),
            'menu'      => 'menu.css',
            'order'     => 'order.css',
            'page'      => 'page.css',
            'rating'    => 'rating.css',
            'sale'      => 'sale.css',
            'solutions' => array (
                'solutions.css',
                'tabs.css',
            ),
            'start'     => 'start.css',
            'user'      => 'user.css',
        ),
    ),
    'js' => array(                               // js-файлы, подключаемые к странице
        'frontend' => array(                     // общедоступная часть сайта
            'base'            => array(
                'jquery-2.1.1.min.js',
                'jquery.cookie.js',
                'jquery.form.min.js',
                'center.js',
                'common.js',
            ),
            'index'           => array(          // главная страница сайта
                'jquery.bxslider.min.js',
                'index.js',
                'tabs.js',
            ),
            'basket-index' => 'basket-index.js',       // корзина
            'basket-checkout' => 'basket-checkout.js', // оформление заказа
            'blog' => array(                     // блог
                'fancybox/jquery.mousewheel-3.0.6.pack.js',
                'fancybox/jquery.fancybox.pack.js',
                'lightbox.js',
            ),
            'catalog' => array(                  // каталог товаров
                'reload.js',
                'fancybox/jquery.mousewheel-3.0.6.pack.js',
                'fancybox/jquery.fancybox.pack.js',
                'lightbox.js',
            ),
            'compare'    => 'compare.js',        // сравнение товаров
            'rating'     => 'rating.js',         // рейтинг лидеров продаж
            'sale'       => 'sale.js',           // распродажа
            'sitemap'    => 'sitemap.js',        // карта сайта
            'solutions'  => array(               // типовые решения
                'solutions.js',
                'fancybox/jquery.mousewheel-3.0.6.pack.js',
                'fancybox/jquery.fancybox.pack.js',
                'lightbox.js',
            ),
            'user'            => 'user.js',      // личный кабинет
            'wished'          => 'wished.js',    // избранное (отложенные товары)

            'page-40'         => array(          // для страницы «Контакты»
                'tabs.js',
                'http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU',
                'page/offices-map.js',
                'fancybox/jquery.mousewheel-3.0.6.pack.js',
                'fancybox/jquery.fancybox.pack.js',
                'lightbox.js'
            ),
            'page-39'          => array(         // для страницы «О компании»
                'fancybox/jquery.mousewheel-3.0.6.pack.js',
                'fancybox/jquery.fancybox.pack.js',
                'lightbox.js',
            ),
            'page-41'          => array(         // для страницы «Доставка»
                'tabs.js',
                'http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU',
                'page/offices-map-route.js',
                'page/delivery-map.js',
            ),
            'page-51'          => array(         // для страницы «Партнеры»
                'fancybox/jquery.mousewheel-3.0.6.pack.js',
                'fancybox/jquery.fancybox.pack.js',
                'lightbox.js',
            ),
            'page-52'          => array(         // для страницы «Библиотека»
                'page/library.js',
            ),

            /*
             * ПРИМЕР ПОДКЛЮЧЕНИЯ ФАЙЛОВ, НЕ УДАЛЯТЬ!
             * 'base' => array(                // js-файлы, подключаемые ко всем страницам сайта
             *     'jquery.min.js',
             *     'common.js',
             * ),
             * 'index' => 'jquery.slider.js',  // только для главной страницы сайта, формируемой Index_Index_Frontend_Controller
             * 'page' => 'page.js',            // для страниц, которые формирует Index_Page_Frontend_Controller
             * 'catalog' => 'catalog.js',      // для страниц, которые формируют все дочерние классы Catalog_Frontend_Controller
             * 'catalog-product' => array(     // только для страниц, которые формирует Product_Catalog_Frontend_Controller
             *     'product.js',
             *     'jquery.lightbox.js',
             * ),
             */

        ),
        'backend' => array( // административная часть сайта
            'base'      => array(
                'jquery-2.1.1.min.js',
                'common.js',
            ),
            'article'   => array(
                'insert-at-caret.js',
                'article.js',
            ),
            'blog'      => array(
                'insert-at-caret.js',
                'blog.js',
            ),
            'catalog'   => 'catalog.js',
            'filter'    => array(
                'jquery.multi-select.js',
                'filter.js',
            ),
            'menu'      => 'add-edit-menu-item.js',
            'page'      => array(
                'insert-at-caret.js',
                'page.js',
            ),
            'rating'    => 'rating.js',
            'solutions' => 'solutions.js',
            'user'      => 'user.js',
        ),
    ),
    'pager' => array(                            // постраничная навигация
        'frontend' => array(                     // общедоступная часть сайта
            'article'   => array(
                'perpage'   => 6,                // статей на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'news'      => array(
                'perpage'   => 6,                // новостей на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'blog'      => array(
                'perpage'   => 10,               // постов на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'products'  => array(
                'perpage'   => 10,               // товаров на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'orders'    => array(
                'perpage'   => 5,                // заказов на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'solutions' => array(
                'perpage'   => 5,                // типовых решений на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
        ),
        'backend' => array(                      // административная часть сайта
            'article'  => array(
                'perpage'   => 20,               // статей на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'news'     => array(
                'perpage'   => 20,               // новостей на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'blog'     => array(
                'perpage'   => 20,               // постов на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'products' => array(
                'perpage'   => 20,               // товаров на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'orders'   => array(
                'perpage'   => 20,               // заказов на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
            'users'    => array(
                'perpage'   => 20,               // пользователей на страницу
                'leftright' => 2,                // кол-во ссылок слева и справа
            ),
        )
    ),
    'user' => array(
        'prefix' => '', // префикс к паролю пользователя для усложнения взлома
        'cookie' => 365, // время хранения уникального идентификатора посетителя на компьютере пользователя 365 дней
    ),
);