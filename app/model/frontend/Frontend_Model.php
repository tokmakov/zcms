<?php
/**
 * Абстрактный класс Frontend_Model, родительский для всех моделей
 * общедоступной части сайта
 */
abstract class Frontend_Model extends Base_Model {

    /**
     * кэшировать данные?
     */
    protected $enableDataCache;


    public function __construct() {
        parent::__construct();
        // кэшировать данные?
        $this->enableDataCache = $this->config->cache->enable->data;
    }

    /**
     * Вспомогательный метод, возвращает данные из кэша или получает данные от
     * БД, сохраняет их кэш, а потом возвращает. Например, контроллер
     * Product_Catalog_Frontend_Controller вызывает метод модели
     * Catalog_Frontend_Model::getProduct(). Если кэширование разрешено, метод
     * Catalog_Frontend_Model::getProduct(), в свою очередь, обращается к методу
     * Frontend_Model::getCachedData(), передавая в качестве параметров ключ
     * доступа к кэшу $key, свое имя $function и массив аргументов $arguments,
     * с которыми он был вызван. Метод Frontend_Model::getCachedData(), либо
     * берет данные из кэша, либо вызывает
     * $data = Catalog_Frontend_Model::product($key, $function, $arguments)
     * сохраняет $data в кэш с ключом $key и возвращает $data.
     */
    protected function getCachedData($key, $function, $arguments) {

        /*
         * Из имени вызывающего метода, например Catalog_Frontend_Model::getProduct(),
         * получаем имя метода, который должен быть вызван, если кэш пустой или
         * устарел, например, Catalog_Frontend_Model::product. Имена методов,
         * передаваемые в аргументе $function, имеют пару, например:
         * 1. Catalog_Frontend_Model::getProduct() и Catalog_Frontend_Model::product()
         * 2. Page_Frontend_Model::getPage() и Page_Frontend_Model::page()
         * 3. Menu_Frontend_Model::getMenu() и Menu_Frontend_Model::menu()
         * и так далее.
         */
        $function = lcfirst(substr($function, 3));
        if (!method_exists($this, $function)) {
            throw new Exception('Метод ' . get_class($this) . '::' . $function . ' не существует');
        }

        /*
         * данные сохранены в кэше?
         */
        if ($this->register->cache->isExists($key)) {
            // получаем данные из кэша
            return $this->register->cache->getValue($key);
        }

        /*
         * данных в кэше нет, но другой процесс поставил блокировку и в этот
         * момент получает данные от БД, чтобы записать их в кэш, нам надо их
         * только получить из кэша после снятия блокировки
         */
        if ($this->register->cache->isLocked($key)) {
            // получаем данные из кэша
            try {
                return $this->register->cache->getValue($key);
            } catch (Exception $e) {
                /*
                 * другой процесс поставил блокировку, попытался получить данные
                 * от БД и записать их в кэш; если по каким-то причинам это не
                 * получилось сделать, мы здесь будем пытаться читать из кэша
                 * значение, которого не существует или оно устарело
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
            $data = call_user_func_array(array($this, $function), $arguments);
            $this->register->cache->setValue($key, $data);
        } finally {
            $this->register->cache->unlockValue($key);
        }
        // возвращаем результат
        return $data;
    }

    /**
     * Функция возвращает абсолютный URL вида http://www.server.com/frontend/
     * controller/action/param/value принимая на вход относительный URL вида
     * frontend/controller/action/param/value. Если в настройках указано
     * использовать SEF (ЧПУ), функция возвращает абсолютный SEF URL.
     */
    public function getURL($url) {
        $url = trim($url, '/');
        // если в настройках не разрешено использовать SEF (ЧПУ)
        if (!$this->config->sef->enable) {
            return parent::getURL($url);
        }
        // если не включено кэширование данных
        if (!$this->enableDataCache) {
            return $this->URL($url);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-' . $url;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэш
        return $this->getCachedData($key, $function, $arguments);
    }

    /**
     * Функция возвращает абсолютный SEF (ЧПУ) URL, принимая на вход
     * относительный URL вида frontend/controller/action/param/value
     */
    protected function URL($url) {
        $cap2sef = $this->config->sef->cap2sef;
        foreach($cap2sef as $key => $value) {
            if (preg_match($key, $url)) {
                return $this->config->site->url . preg_replace($key, $value, $url);
            }
        }
        throw new Exception('Не найдено правило преобразования CAP->SEF для ' . $url);
    }

}