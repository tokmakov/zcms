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
	 * для хранения экземпляра класса, отвечающего за кэширование
	 */
	private $cache;


	/**
	 * Функция возвращает ссылку на экземпляр данного класса,
	 * реализация шаблона проектирования «Одиночка»
	 */
	public static function getInstance(){
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
		// экземпляр класса, отвечающего за кэширование
		$this->cache = Cache::getInstance();
		// создаем новый экземпляр класса PDO
		$this->pdo = new PDO(
			'mysql:host=' . $this->config->database->host . ';dbname=' . $this->config->database->name,
			$this->config->database->user,
			$this->config->database->pass,
			array(
				PDO::ATTR_PERSISTENT => $this->config->database->pcon,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
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
	 * сохраняет $data в кэш с ключом $key и возвращает $data.
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
		if (!method_exists($this, $function)) {
			throw new Exception('Метод ' . __CLASS__ . '::' . $function . ' не существует');
		}

		/*
		 * данные сохранены в кэше?
		 */
		if ($this->cache->isExists($key)) {
			// получаем данные из кэша
			return $this->cache->getValue($key);
		}

		/*
		 * данных в кэше нет, но другой процесс поставил блокировку и в этот
		 * момент получает данные от БД, чтобы записать их в кэш, нам надо их
		 * только получить из кэша после снятия блокировки
		 */
		if ($this->cache->isLocked($key)) {
			// получаем данные из кэша
			try {
				return $this->cache->getValue($key);
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
		$this->cache->lockValue($key);
		try {
			$data = $this->$function($arguments[0], $arguments[1]);
			$this->cache->setValue($key, $data);
		} finally {
			$this->cache->unlockValue($key);
		}
		// возвращаем результат
		return $data;
	}

	/**
	 *  Метод-обертка для PDOStatement::execute()
	 */
	public function execute($query, $params = array()) {
		// подготавливаем запрос к выполнению
		$statementHandler = $this->pdo->prepare($query);
		// выполняем запрос
		return $statementHandler->execute($params);
	}

	/**
	 *  Метод-обертка для PDOStatement::fetchAll()
	 */
	public function fetchAll($query, $params = array(), $cache = false) {
		// если кэширование запрещено
		if (!$cache) {
			return $this->pdoFetchAll($query, $params);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-' . md5($query . serialize($params));
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		array_pop($arguments);
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	private function pdoFetchAll($query, $params = array()) {
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
	 * Метод-обертка для PDOStatement::fetch()
	 */
	public function fetch($query, $params = array(), $cache = false) {
		// если кэширование запрещено
		if (!$cache) {
			return $this->pdoFetch($query, $params);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-' . md5($query . serialize($params));
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		array_pop($arguments);
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	private function pdoFetch($query, $params = array()) {
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
	public function fetchOne($query, $params = array(), $cache = false) {
		// если кэширование запрещено
		if (!$cache) {
			return $this->pdoFetchOne($query, $params);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-' . md5($query . serialize($params));
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		array_pop($arguments);
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	private function pdoFetchOne($query, $params = array()) {
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
		return $this->pdo->lastInsertId();
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