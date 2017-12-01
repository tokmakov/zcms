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
     * канонический URL, для роботов поисковых систем
     */
    protected $canonicalURL = false;

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
     * Модели, которые нужны для работы всех страниц сайта
     */

    /**
     * экземпляр класса модели для работы с баннерами
     */
    protected $bannerFrontendModel;

    /**
     * экземпляр класса модели для работы с корзиной
     */
    protected $basketFrontendModel;

    /**
     * экземпляры классов моделей для работы с каталогом товаров
     */
    protected $indexCatalogFrontendModel,
              $categoryCatalogFrontendModel,
              $groupCatalogFrontendModel,
              $makerCatalogFrontendModel,
              $productCatalogFrontendModel,
              $searchCatalogFrontendModel,
              $menuCatalogFrontendModel;

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
     * экземпляр класса модели для работы с картой сайта
     */
    protected $sitemapFrontendModel;

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
        $this->bannerFrontendModel =
            isset($this->register->bannerFrontendModel) ? $this->register->bannerFrontendModel : new Banner_Frontend_Model();

        // экземпляр класса модели для работы с корзиной
        $this->basketFrontendModel =
            isset($this->register->basketFrontendModel) ? $this->register->basketFrontendModel : new Basket_Frontend_Model();

        // экземпляры классов моделей для работы с каталогом товаров
        $this->indexCatalogFrontendModel =
            isset($this->register->indexCatalogFrontendModel) ? $this->register->indexCatalogFrontendModel : new Index_Catalog_Frontend_Model();
        $this->categoryCatalogFrontendModel =
            isset($this->register->categoryCatalogFrontendModel) ? $this->register->categoryCatalogFrontendModel : new Category_Catalog_Frontend_Model();
        $this->groupCatalogFrontendModel =
            isset($this->register->groupCatalogFrontendModel) ? $this->register->groupCatalogFrontendModel : new Group_Catalog_Frontend_Model();
        $this->makerCatalogFrontendModel =
            isset($this->register->makerCatalogFrontendModel) ? $this->register->makerCatalogFrontendModel : new Maker_Catalog_Frontend_Model();
        $this->productCatalogFrontendModel =
            isset($this->register->productCatalogFrontendModel) ? $this->register->productCatalogFrontendModel : new Product_Catalog_Frontend_Model();
        $this->searchCatalogFrontendModel =
            isset($this->register->searchCatalogFrontendModel) ? $this->register->searchCatalogFrontendModel : new Search_Catalog_Frontend_Model();
        $this->menuCatalogFrontendModel =
            isset($this->register->menuCatalogFrontendModel) ? $this->register->menuCatalogFrontendModel : new Menu_Catalog_Frontend_Model();

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

        // экземпляр класса модели для работы с картой сайта
        $this->sitemapFrontendModel =
            isset($this->register->sitemapFrontendModel) ? $this->register->sitemapFrontendModel : new Sitemap_Frontend_Model();

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
         * массив переменных, которые будут переданы в шаблон head.php
         */
        // этот массив еще будет дополнен элементами, см. комментарий
        // в методе Frontend_Controller::output()
        $this->headVars = array(
            'cssFiles'    => $this->cssFiles,
            'jsFiles'     => $this->jsFiles,
        );

        /*
         * массив переменных, которые будут переданы в шаблон header.php
         */
        $this->headerVars = array(
            // URL ссылки на главную страницу сайта
            'indexUrl'     => $this->indexFrontendModel->getURL('frontend/index/index'),
            // URL страницы поиска по каталогу товаров
            'searchUrl'    => $this->searchCatalogFrontendModel->getURL('frontend/catalog/search'),
            // URL ссылки на страницу личного кабинета
            'userUrl'      => $this->userFrontendModel->getURL('frontend/user/index'),
            // пользователь авторизован?
            'authUser'     => $this->authUser,
            // URL ссылки на страницу корзины
            'basketUrl'    => $this->basketFrontendModel->getURL('frontend/basket/index'),
            // корзина пуста?
            'emptyBasket'  => ! $this->basketFrontendModel->getBasketCount(),
            // URL ссылки на страницу отложенных товаров
            'wishedUrl'    => $this->wishedFrontendModel->getURL('frontend/wished/index'),
            // список отложенных товаров пустой?
            'emptyWished'  => ! $this->wishedFrontendModel->getWishedCount(),
            // URL ссылки на страницу сравнения товаров
            'compareUrl'   => $this->compareFrontendModel->getURL('frontend/compare/index'),
            // список товаров для сравнения пустой?
            'emptyCompare' => ! $this->compareFrontendModel->getCompareCount(),
            // URL ссылки на страницу просмотренных товаров
            'viewedUrl'    => $this->viewedFrontendModel->getURL('frontend/viewed/index'),
            // список просмотренных товаров пустой?
            'emptyViewed'  => ! $this->viewedFrontendModel->getViewedCount(),
        );

        // главное меню сайта
        $menu = $this->menuFrontendModel->getMenu();

        /*
         * массив переменных, которые будут переданы в шаблон menu.php
         */
        $this->menuVars = array('menu' => $menu);


        /*
         * массив переменных, которые будут переданы в шаблон left.php
         */

        // чтобы правильно сформировать ссылки на категории каталога, на страницы
        // производителей и на страницы функциональных групп, надо знать, выбрал
        // пользователь сортировку и кол-во товаров на странице
        $sort = 0; // пользователь выбрал сортировку?
        if (isset($_COOKIE['sort']) && in_array($_COOKIE['sort'], array(1,2,3,4,5,6))) {
            $sort = (int)$_COOKIE['sort'];
        }
        $perpage = 0; // пользователь выбрал кол-во товаров?
        $others = $this->config->pager->frontend->products->getValue('others'); // доступные варианты
        if (isset($_COOKIE['perpage']) && in_array($_COOKIE['perpage'], $others)) {
            $perpage = (int)$_COOKIE['perpage'];
        }

        // меню каталога (для левой колонки)
        $catalogMenu = $this->menuCatalogFrontendModel->getCatalogMenu(0, $sort, $perpage);

        // список производителей (для левой колонки)
        $makers = $this->makerCatalogFrontendModel->getMakers(10, $sort, $perpage);

        // список функциональных групп (для левой колонки)
        $groups = $this->groupCatalogFrontendModel->getGroups(10, $sort, $perpage);

        $this->leftVars = array(
            // каталог меню для левой колонки
            'catalogMenu'  => $catalogMenu,
            // массив производителей
            'makers'       => $makers,
            // массив функциональных групп
            'groups'       => $groups,
            // URL ссылки на страницу со списком всех производителей
            'allMakersURL' => $this->makerCatalogFrontendModel->getURL('frontend/catalog/makers'),
            // URL ссылки на страницу со списком всех функциональных групп
            'allGroupsURL' => $this->groupCatalogFrontendModel->getURL('frontend/catalog/groups'),
        );

        /*
         * массив переменных, которые будут переданы в шаблон right.php
         */

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

        // переменные $this->title, $this->keywords, $this->description и $this->robots,
        // которые будут переданы в шаблон head.php, могут быть изменены в методе input()
        // дочерних классов, поэтому помещаем их в массив $this->headVars только здесь,
        // а не в методе Frontend_Controller::input()
        $this->headVars['title']        = $this->title;
        $this->headVars['keywords']     = $this->keywords;
        $this->headVars['description']  = $this->description;
        $this->headVars['robots']       = $this->robots;
        $this->headVars['canonicalURL'] = $this->canonicalURL;

        /*
         * получаем html-код тега <head>
         */
        $this->headContent = $this->render(
            $this->headTemplateFile,
            $this->headVars
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
        $this->rightContent = $this->render(
            $this->rightTemplateFile,
            $this->rightVars
        );

        /*
         * получаем html-код подвала страницы
         */
        $this->footerContent = $this->render(
            $this->footerTemplateFile,
            $this->footerVars
        );

        /*
         * html-код отдельных частей страницы получен, теперь формируем
         * всю страницу целиком
         */
        $this->pageContent = $this->render(
            $this->wrapperTemplateFile,
            array(
                'headContent'   => $this->headContent,
                'headerContent' => $this->headerContent,
                'menuContent'   => $this->menuContent,
                'centerContent' => $this->centerContent,
                'leftContent'   => $this->leftContent,
                'rightContent'  => $this->rightContent,
                'footerContent' => $this->footerContent
            )
        );

    }

    /**
     * Функция для обработки шаблонов, принимает имя файла шаблона и массив переменных,
     * которые должны быть доступны в шаблоне, возвращает html-код шаблона с подставленными
     * значениями переменных
     */
    protected function render($template, $params = array()) {

        // если не включено кэширование шаблонов
        if ( ! $this->enableHtmlCache) {
            return parent::render($template, $params);
        }

        /*
         * кэширование включено, но в некоторых случаях оно не оправдывает себя
         */
        if ($this->notUseCache) {
            return parent::render($template, $params);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-' . $template . '-' . md5(serialize($params));

        /*
         * Данные сохранены в кэше?
         */
        if ($this->cache->isExists($key)) {
            // получаем данные из кэша
            return $this->cache->getValue($key);
        }

        /*
         * Данных в кэше нет, но другой процесс поставил блокировку и в этот
         * момент получает данные от parent::render(), чтобы записать их в
         * кэш, нам надо их только получить из кэша после снятия блокировки
         */
        if ($this->cache->isLocked($key)) {
            return $this->cache->getValue($key);
        }

        /*
         * Данных в кэше нет, блокировка не стоит, значит:
         * 1. ставим блокировку
         * 2. получаем данные из БД
         * 3. записываем данные в кэш
         * 4. снимаем блокировку
         */
        $this->cache->lockValue($key);
        $html = parent::render($template, $params);
        $this->cache->setValue($key, $html);
        $this->cache->unlockValue($key);

        // возвращаем результат
        return $html;

    }

}