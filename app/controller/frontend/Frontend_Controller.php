<?php
/**
 * Абстрактный класс Frontend_Controller, родительский для всех контроллеров
 * общедоступной части сайта
 */
abstract class Frontend_Controller extends Base_Controller {

    /**
     * мета-тег robots, управляет индексацией страницы
     */
    protected $robots = true;

    /**
     * уникальный идентификатор посетителя сайта
     */
    protected $visitorId;

    /**
     * пользователь авторизован?
     */
    protected $authUser = false;

    /**
     * кэшировать шаблоны?
     */
    protected $enableHtmlCache;

    /**
     * не использовать кэширование шаблонов, даже если в настройках
     * указано использовать кэш; см. Frontend_Controller::render() и
     * Frontend_Controller::output()
     */
    protected $notUseCache = false;


    /*
     * Все модели, которые нужны для работы
     */

    /**
     * экземпляр класса модели для работы со статьями
     */
    protected $articleFrontendModel;

    /**
     * экземпляр класса модели для работы с баннерами
     */
    protected $bannerFrontendModel;

    /**
     * экземпляр класса модели для работы с корзиной
     */
    protected $basketFrontendModel;

    /**
     * экземпляр класса модели для работы с блогом
     */
    protected $blogFrontendModel;

    /**
     * экземпляр класса модели для работы с каталогом товаров
     */
    protected $catalogFrontendModel;

    /**
     * экземпляр класса модели для работы с товарами для сравнения
     */
    protected $compareFrontendModel;

    /**
     * экземпляр класса модели для работы с главной страницей сайта
     */
    protected $indexFrontendModel;

    /**
     * экземпляр класса модели для работы с главным меню сайта
     */
    protected $menuFrontendModel;

    /**
     * экземпляр класса модели для работы со страницами сайта
     */
    protected $pageFrontendModel;
    
    /**
     * экземпляр класса модели для работы с рейтингом продаж
     */
    protected $ratingFrontendModel;

    /**
     * экземпляр класса модели для работы с товарами по сниженным ценам
     */
    protected $saleFrontendModel;

    /**
     * экземпляр класса модели для работы с картой сайта
     */
    protected $sitemapFrontendModel;

    /**
     * экземпляр класса модели для работы с типовыми решениями
     */
    protected $solutionsFrontendModel;

    /**
     * экземпляр класса модели для работы с пользователями
     */
    protected $userFrontendModel;

    /**
     * экземпляр класса модели для работы с просмотренными товарами
     */
    protected $viewedFrontendModel;

    /**
     * экземпляр класса модели для работы с отложенными товарами
     */
    protected $wishedFrontendModel;


    public function __construct($params = null) {

        parent::__construct($params);

        // кэшировать шаблоны?
        $this->enableHtmlCache = $this->config->cache->enable->html;

        /*
         * Все модели, которые нужны для работы
         */

        // экземпляр класса модели для работы с баннерами
        $this->articleFrontendModel =
            isset($this->register->articleFrontendModel) ? $this->register->articleFrontendModel : new Article_Frontend_Model();

        // экземпляр класса модели для работы с баннерами
        $this->bannerFrontendModel =
            isset($this->register->bannerFrontendModel) ? $this->register->bannerFrontendModel : new Banner_Frontend_Model();

        // экземпляр класса модели для работы с корзиной
        $this->basketFrontendModel =
            isset($this->register->basketFrontendModel) ? $this->register->basketFrontendModel : new Basket_Frontend_Model();

        // экземпляр класса модели для работы с блогом
        $this->blogFrontendModel =
            isset($this->register->blogFrontendModel) ? $this->register->blogFrontendModel : new Blog_Frontend_Model();

        // экземпляр класса модели для работы с каталогом товаров
        $this->catalogFrontendModel =
            isset($this->register->catalogFrontendModel) ? $this->register->catalogFrontendModel : new Catalog_Frontend_Model();

        // экземпляр класса модели для работы с товарами для сравнения
        $this->compareFrontendModel =
            isset($this->register->compareFrontendModel) ? $this->register->compareFrontendModel : new Compare_Frontend_Model();

        // экземпляр класса модели для работы с главной страницей сайта
        $this->indexFrontendModel =
            isset($this->register->indexFrontendModel) ? $this->register->indexFrontendModel : new Index_Frontend_Model();

        // экземпляр класса модели для работы с главным меню
        $this->menuFrontendModel =
            isset($this->register->menuFrontendModel) ? $this->register->menuFrontendModel : new Menu_Frontend_Model();

        // экземпляр класса модели для работы со страницами сайта
        $this->pageFrontendModel =
            isset($this->register->pageFrontendModel) ? $this->register->pageFrontendModel : new Page_Frontend_Model();
            
        // экземпляр класса модели для работы с рейтингом продаж
        $this->ratingFrontendModel =
            isset($this->register->ratingFrontendModel) ? $this->register->ratingFrontendModel : new Rating_Frontend_Model();

        // экземпляр класса модели для работы со страницами сайта
        $this->saleFrontendModel =
            isset($this->register->saleFrontendModel) ? $this->register->saleFrontendModel : new Sale_Frontend_Model();

        // экземпляр класса модели для работы с картой сайта
        $this->sitemapFrontendModel =
            isset($this->register->sitemapFrontendModel) ? $this->register->sitemapFrontendModel : new Sitemap_Frontend_Model();

        // экземпляр класса модели для работы с типовыми решениями
        $this->solutionsFrontendModel =
            isset($this->register->solutionsFrontendModel) ? $this->register->solutionsFrontendModel : new Solutions_Frontend_Model();

        // экземпляр класса модели для работы с пользователями
        $this->userFrontendModel =
            isset($this->register->userFrontendModel) ? $this->register->userFrontendModel : new User_Frontend_Model();

        // экземпляр класса модели для работы с просмотренными товарами
        $this->viewedFrontendModel =
            isset($this->register->viewedFrontendModel) ? $this->register->viewedFrontendModel : new Viewed_Frontend_Model();

        // экземпляр класса модели для работы с отложенными товарами
        $this->wishedFrontendModel =
            isset($this->register->wishedFrontendModel) ? $this->register->wishedFrontendModel : new Wished_Frontend_Model();


        // пользователь авторизован?
        $this->authUser = $this->userFrontendModel->isAuthUser();
        // если не авторизован, пробуем войти автоматически
        if ( ! $this->authUser) {
            $this->authUser = $this->userFrontendModel->autoLogin();
        }

    }

    /**
     * Функция получает из настроек и от моделей данные, необходимые для
     * работы всех потомков класса Frontend_Controller
     */
    protected function input() {

        /*
         * сначала обращаемся к родительскому классу Base_Controller, чтобы
         * установить значения переменных, которые нужны для работы всех его
         * потомков, потом переопределяем эти переменные (если необходимо) и
         * устанавливаем значения перменных, которые нужны для работы всех
         * потомков Frontend_Controller
         */
        parent::input();

        // получаем из настроек значения по умолчанию для мета-тегов
        $this->title       = $this->config->meta->default->title;
        $this->keywords    = $this->config->meta->default->keywords;
        $this->description = $this->config->meta->default->description;

        /*
         * массив переменных, которые будут переданы в шаблон header.php
         */
        $this->headerVars = array(
            // URL ссылки на главную страницу сайта
            'indexUrl'    => $this->indexFrontendModel->getURL('frontend/index/index'),
            // URL страницы поиска по каталогу товаров
            'searchUrl'   => $this->catalogFrontendModel->getURL('frontend/catalog/search'),
            // URL ссылки на страницу корзины
            'basketUrl'   => $this->basketFrontendModel->getURL('frontend/basket/index'),
            // URL ссылки на страницу личного кабинета
            'userUrl'     => $this->userFrontendModel->getURL('frontend/user/index'),
            // URL ссылки на страницу отложенных товаров
            'wishedUrl'   => $this->wishedFrontendModel->getURL('frontend/wished/index'),
            // URL ссылки на страницу сравнения товаров
            'compareUrl'  => $this->compareFrontendModel->getURL('frontend/compare/index'),
            // URL ссылки на страницу просмотренных товаров
            'viewedUrl'   => $this->viewedFrontendModel->getURL('frontend/viewed/index'),
        );

        // главное меню сайта
        $menu = $this->menuFrontendModel->getMenu();

        /*
         * массив переменных, которые будут переданы в шаблон menu.php
         */
        $this->menuVars = array('menu' => $menu);

        // меню каталога (для левой колонки)
        $catalogMenu = $this->catalogFrontendModel->getCatalogMenu();

        // список производителей (для левой колонки)
        $makers = $this->catalogFrontendModel->getMakers();

        /*
         * массив переменных, которые будут переданы в шаблон left.php
         */
        $this->leftVars = array(
            // каталог меню для левой колонки
            'catalogMenu'  => $catalogMenu,
            // массив производителей
            'makers'       => $makers,
            // URL ссылки на страницу со списком всех производителей
            'allMakersUrl' => $this->catalogFrontendModel->getURL('frontend/catalog/allmkrs'),
        );

        // получаем от модели массив товаров в корзине (для правой колонки)
        $sideBasketProducts = $this->basketFrontendModel->getSideBasketProducts();
        // общая стоимость товаров в корзине (для правой колонки)
        $sideBasketTotalCost = $this->basketFrontendModel->getSideTotalCost();

        // получаем от модели массив последних отложенных товаров (для правой колонки)
        $sideWishedProducts = $this->wishedFrontendModel->getSideWishedProducts();

        // получаем от модели массив последних товаров для сравнения (для правой колонки)
        $sideCompareProducts = $this->compareFrontendModel->getSideCompareProducts();

        // получаем от модели массив последних просмотренных товаров (для правой колонки)
        $sideViewedProducts = $this->viewedFrontendModel->getSideViewedProducts();

        /*
         * массив переменных, которые будут переданы в шаблон right.php
         */
        // пользователь авторизован?
        $this->rightVars['authUser'] = $this->authUser;
        if ($this->authUser) {
            // ссылка на страницу личного кабинета
            $this->rightVars['userIndexUrl']    = $this->userFrontendModel->getURL('frontend/user/index');
            // ссылка на страницу с формой для редактирования личных данных
            $this->rightVars['userEditUrl']     = $this->userFrontendModel->getURL('frontend/user/edit');
            // ссылка на страницу со списком всех профилей
            $this->rightVars['userProfilesUrl'] = $this->userFrontendModel->getURL('frontend/user/allprof');
            // ссылка на страницу со списком всех заказов
            $this->rightVars['userOrdersUrl']   = $this->userFrontendModel->getURL('frontend/user/allorders');
            // ссылка для выхода из личного кабинета
            $this->rightVars['userLogoutUrl']   = $this->userFrontendModel->getURL('frontend/user/logout');
        } else {
            // атрибут action тега form формы для авторизации пользователя
            $this->rightVars['action']          = $this->userFrontendModel->getURL('frontend/user/login');
            // ссылка на страницу регистрации
            $this->rightVars['regFormUrl']      = $this->userFrontendModel->getURL('frontend/user/reg');
            // ссылка на страницу восстановления пароля
            $this->rightVars['forgotFormUrl']   = $this->userFrontendModel->getURL('frontend/user/forgot');
        }
        // массив товаров в корзине
        $this->rightVars['basketProducts']   = $sideBasketProducts;
        // общая стоимость товаров в корзине
        $this->rightVars['basketTotalCost']  = $sideBasketTotalCost;
        // URL ссылки на страницу корзины
        $this->rightVars['basketURL']        = $this->basketFrontendModel->getURL('frontend/basket/index');
        // URL ссылки на страницу оформления заказа
        $this->rightVars['checkoutURL']      = $this->basketFrontendModel->getURL('frontend/basket/checkout');
        // массив отложенных товаров (избранное)
        $this->rightVars['wishedProducts']   = $sideWishedProducts;
        // URL ссылки на страницу отложенных товаров
        $this->rightVars['wishedURL']        = $this->wishedFrontendModel->getURL('frontend/wished/index');
        // массив товаров для сравнения
        $this->rightVars['compareProducts']  = $sideCompareProducts;
        // URL ссылки на страницу товаров для сравнения
        $this->rightVars['compareURL']       = $this->compareFrontendModel->getURL('frontend/compare/index');
        // URL ссылки для удаления всех товаров из сравнения
        $this->rightVars['clearCompareURL']  = $this->compareFrontendModel->getURL('frontend/compare/clear');
        // массив просмотренных товаров
        $this->rightVars['viewedProducts']   = $sideViewedProducts;
        // URL ссылки на страницу просмотренных товаров
        $this->rightVars['viewedURL']        = $this->viewedFrontendModel->getURL('frontend/viewed/index');
        // массив баннеров
        $this->rightVars['banners']          = $this->bannerFrontendModel->getBanners();

        /*
         * массив переменных, которые будут переданы в шаблон footer.php
         */
        $this->footerVars = array(
            'siteMapUrl' => $this->sitemapFrontendModel->getURL('frontend/sitemap/index')
        );

    }

    /**
     * Функция формирует html-код отдельных частей страницы:
     * шапка, главное меню, центральная колонка, левая и правая
     * колонки, подвал и т.п.
     */
    protected function output() {

        /*
         * получаем html-код тега <head>
         */
        $this->headContent = $this->render(
            $this->headTemplateFile,
            array(
                'title' => $this->title,
                'keywords' => $this->keywords,
                'description' => $this->description,
                'cssFiles' => $this->cssFiles,
                'jsFiles' => $this->jsFiles,
                'robots' => $this->robots,
            )
        );

        /*
         * получаем html-код шапки сайта
         */
        $this->headerContent = $this->render(
            $this->headerTemplateFile,
            $this->headerVars
        );

        /*
         * получаем html-код меню
         */
        $this->menuContent = $this->render(
            $this->menuTemplateFile,
            $this->menuVars
        );

        /*
         * получаем html-код центральной колонки
         */
        $this->centerContent = $this->render(
            $this->centerTemplateFile,
            $this->centerVars
        );

        /*
         * получаем html-код левой колонки
         */
        $this->leftContent = $this->render(
            $this->leftTemplateFile,
            $this->leftVars
        );

        /*
         * получаем html-код правой колонки
         */
        // кэширование не оправдывает себя; см. комментарии
        // к методу Frontend_Controller::render()
        $this->notUseCache = true;
        $this->rightContent = $this->render(
            $this->rightTemplateFile,
            $this->rightVars
        );
        $this->notUseCache = false;

        /*
         * получаем html-код подвала страницы
         */
        $this->footerContent = $this->render(
            $this->footerTemplateFile,
            $this->footerVars
        );

        /*
         * html-код отдельных частей страницы получен, теперь формируем
         * всю страницу целиком, обращаясь к Base_Controller::output()
         */
        // кэширование не оправдывает себя: если не кэширован right.php,
        // то и wrapper.php нет смысла кэшировать
        $this->notUseCache = true;
        parent::output();
        $this->notUseCache = false;

    }

    /**
     * Функция для обработки шаблонов, принимает имя файла шаблона и массив переменных,
     * которые должны быть доступны в шаблоне, возвращает html-код шаблона с подставленными
     * значениями переменных
     */
    protected function render($template, $params = array()) {

        // если не включено кэширование шаблонов
        if (!$this->enableHtmlCache) {
            return parent::render($template, $params);
        }

        /*
         * Кэширование включено, но в некоторых случаях оно не оправдывает себя,
         * например, для шаблона right.php. У разных пользователей будут разные
         * корзины, списки отложенных товаров, товаров для сравнения, просмотренных
         * товаров. Вероятность, что у двух пользователей содержимое всех четырех
         * списков совпадёт, ничтожно мала. Поэтому в Frontend_Controller::output()
         * для шаблона правой колонки notUseCache выставляется в true, а потом
         * обратно в false.
         */
        if ($this->notUseCache) {
            return parent::render($template, $params);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-' . $template . '-' . md5(serialize($params));

        /*
         * данные сохранены в кэше?
         */
        if ($this->register->cache->isExists($key)) {
            // получаем данные из кэша
            return $this->register->cache->getValue($key);
        }

        /*
         * данных в кэше нет, но другой процесс поставил блокировку и в этот
         * момент получает данные от parent::render(), чтобы записать их в кэш,
         * нам надо их только получить из кэша после снятия блокировки
         */
        if ($this->register->cache->isLocked($key)) {
            try {
                // получаем данные из кэша
                return $this->register->cache->getValue($key);
            } catch (Exception $e) {
                /*
                 * другой процесс поставил блокировку, попытался получить данные
                 * от parent::render() и записать их в кэш; если по каким-то
                 * причинам это не получилось сделать, мы здесь будем пытаться
                 * читать из кэша значение, которого не существует или оно устарело
                 */
                throw $e;
            }
        }

        /*
         * данных в кэше нет, блокировка не стоит, значит:
         * 1. ставим блокировку
         * 2. получаем данные из БД
         * 3. записываем данные в кэш
         * 4. снимаем блокировку
         */
        $this->register->cache->lockValue($key);
        try {
            $html = parent::render($template, $params);
            $this->register->cache->setValue($key, $html);
        } finally {
            $this->register->cache->unlockValue($key);
        }
        // возвращаем результат
        return $html;

    }

}