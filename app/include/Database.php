<?php
/**
 * Класс Database, предоставляет доступ к базе данных,
 * реализует шаблон проектирования «Одиночка»
 */
class Database {

    /**
     * для хранения единственного экземпляра данного класса
     */
    private static $instance;

    /**
     * для хранения экземпляра класса PDO
     */
    private $pdo;

    /**
     * настройки приложения, экземпляр класса Config
     */
    private $config;

    /**
     * включена балансировка нагрузки между master и slave?
     */
    private $balancing;

    /**
     * для хранения экземпляра класса, отвечающего за кэширование
     */
    private $cache;


    /**
     * Функция возвращает ссылку на экземпляр данного класса,
     * реализация шаблона проектирования «Одиночка»
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Закрытый конструктор, необходим для реализации шаблона
     * проектирования «Одиночка»
     */
    private function __construct() {
        // настройки приложения, экземпляр класса Config
        $this->config = Config::getInstance();
        // включена балансировка нагрузки?
        $this->balancing = $this->config->database->balancing;
        // экземпляр класса, отвечающего за кэширование
        $this->cache = Cache::getInstance();
        // создаем новый экземпляр класса PDO
        $this->pdo = new PDO(
            'mysql:host=' . $this->config->database->host . ';dbname=' . $this->config->database->name,
            $this->config->database->user,
            $this->config->database->pass,
            array(
                PDO::ATTR_PERSISTENT         => $this->config->database->pcon,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            )
        );
    }

    /**
     * Вспомогательный метод, возвращает данные из кэша или получает данные от
     * БД, сохраняет их кэш, а потом возвращает. Например, внешний код вызывает
     * метод Database::fetchAll(). Если кэширование разрешено, метод
     * Database::fetchAll(), в свою очередь, обращается к методу
     * Database::getCachedData(), передавая в качестве параметров ключ доступа к
     * кэшу $key, свое имя $function и массив аргументов $arguments, с которыми
     * он был вызван. Метод Database::getCachedData(), либо берет данные из кэша,
     * либо вызывает $data = Database::pdoFetchAll($key, $function, $arguments),
     * сохраняет полученные данные $data в кэш с ключом $key и возвращает $data.
     */
    private function getCachedData($key, $function, $arguments) {

        /*
         * Из имени вызывающего метода (например, fetchAll) получаем имя метода,
         * который должен быть вызван, если кэш пустой или устарел (например,
         * pdoFetchAll). Имена методов, передаваемые в аргументе $function, имеют
         * пару:
         * 1. public function fetchAll() и private function pdoFetchAll()
         * 2. public function fetch() и private function pdoFetch()
         * 3. public function fetchOne() и private function pdoFetchOne()
         */
        $function = 'pdo' . ucfirst($function);
        if ( ! method_exists($this, $function)) {
            throw new Exception('Метод ' . __CLASS__ . '::' . $function . ' не существует');
        }

        /*
         * Данные сохранены в кэше?
         */
        if ($this->cache->isExists($key)) {
            // получаем данные из кэша
            return $this->cache->getValue($key);
        }

        /*
         * Данных в кэше нет, но другой процесс поставил блокировку и в этот
         * момент получает данные от БД, чтобы записать их в кэш, нам надо их
         * только получить из кэша после снятия блокировки
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
        $data = $this->$function($arguments[0], $arguments[1], $arguments[2]);
        $this->cache->setValue($key, $data);
        $this->cache->unlockValue($key);

        // возвращаем результат
        return $data;

    }

    /**
     *  Метод-обертка для PDOStatement::execute()
     */
    public function execute($query, $params = array()) {
        // включена балансировка нагрузки?
        if ($this->balancing) {
            $query = '/*' . MYSQLND_MS_MASTER_SWITCH . '*/ ' . $query;
        }
        // подготавливаем запрос к выполнению
        $statementHandler = $this->pdo->prepare($query);
        // выполняем запрос
        return $statementHandler->execute($params);
    }

    /**
     *  Метод-обертка для PDOStatement::fetchAll(); результат работы кэшируется
     * (если включено кэширование); если включена балансировка нагрузки MySQL,
     * запросы отправляются на master или slave сервер
     */
    public function fetchAll($query, $params = array(), $cache = false, $slave = false) {

        /*
         * если кэширование запрещено, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $cache) {
            return $this->pdoFetchAll($query, $params, $slave);
        }

        /*
         * если кэширование разрешено, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-' . md5($query . serialize($params));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = array($query, $params, $slave);
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Метод-обертка для PDOStatement::fetchAll(); если включена балансировка
     * нагрузки MySQL, запросы отправляются на master или slave сервер
     */
    private function pdoFetchAll($query, $params, $slave) {
        // включена балансировка нагрузки?
        if ($this->balancing) {
            if ($slave) { // выполняем запрос на slave-сервере
                $query = '/*' . MYSQLND_MS_SLAVE_SWITCH . '*/ ' . $query;
            } else { // выполняем запрос на master-сервере
                $query = '/*' . MYSQLND_MS_MASTER_SWITCH . '*/ ' . $query;
            }
        }
        // подготавливаем запрос к выполнению
        $statementHandler = $this->pdo->prepare($query);
        // выполняем запрос
        $statementHandler->execute($params);
        // получаем результат
        $result = $statementHandler->fetchAll(PDO::FETCH_ASSOC);
        // возвращаем результаты запроса
        return $result;
    }

    /**
     * Метод-обертка для PDOStatement::fetch(); результат работы кэшируется
     * (если включено кэширование); если включена балансировка нагрузки MySQL,
     * запросы отправляются на master или slave сервер
     */
    public function fetch($query, $params = array(), $cache = false, $slave = false) {

        /*
         * если кэширование запрещено, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $cache) {
            return $this->pdoFetch($query, $params, $slave);
        }

        /*
         * если кэширование разрешено, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-' . md5($query . serialize($params));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = array($query, $params, $slave);
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    /**
     * Метод-обертка для PDOStatement::fetch(); если включена балансировка
     * нагрузки MySQL, запросы отправляются на master или slave сервер
     */
    private function pdoFetch($query, $params, $slave) {

        // включена балансировка нагрузки?
        if ($this->balancing) {
            if ($slave) { // выполняем запрос на slave-сервере
                $query = '/*' . MYSQLND_MS_SLAVE_SWITCH . '*/ ' . $query;
            } else { // выполняем запрос на master-сервере
                $query = '/*' . MYSQLND_MS_MASTER_SWITCH . '*/ ' . $query;
            }
        }
        // подготавливаем запрос к выполнению
        $statementHandler = $this->pdo->prepare($query);
        // выполняем запрос
        $statementHandler->execute($params);
        // получаем результат
        $result = $statementHandler->fetch(PDO::FETCH_ASSOC);
        // возвращаем результат запроса
        return $result;

    }

    /**
     *  Метод возвращает значение первого столбца из строки
     */
    public function fetchOne($query, $params = array(), $cache = false, $slave = false) {

        /*
         * если кэширование запрещено, получаем данные с помощью
         * запроса к базе данных
         */
        if ( ! $cache) {
            return $this->pdoFetchOne($query, $params, $slave);
        }

        /*
         * если кэширование разрешено, получаем данные из кэша; если данные
         * в кэше не актуальны, будет выполнен запрос к базе данных
         */
        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-' . md5($query . serialize($params));
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = array($query, $params, $slave);
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);

    }

    private function pdoFetchOne($query, $params, $slave) {

        // включена балансировка нагрузки?
        if ($this->balancing) {
            if ($slave) { // выполняем запрос на slave-сервере
                $query = '/*' . MYSQLND_MS_SLAVE_SWITCH . '*/ ' . $query;
            } else { // выполняем запрос на master-сервере
                $query = '/*' . MYSQLND_MS_MASTER_SWITCH . '*/ ' . $query;
            }
        }
        // подготавливаем запрос к выполнению
        $statementHandler = $this->pdo->prepare($query);
        // выполняем запрос
        $statementHandler->execute($params);
        // получаем результат
        $result = $statementHandler->fetch(PDO::FETCH_NUM);
        // возвращаем результат запроса
        if (false === $result) {
            return false;
        }
        return $result[0];

    }

    public function lastInsertId() {
        return (int)$this->pdo->lastInsertId();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}
