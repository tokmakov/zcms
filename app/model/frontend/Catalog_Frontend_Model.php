<?php
/**
 * Класс Catalog_Frontend_Model для работы с каталогом товаров, взаимодействует
 * с базой данных, общедоступная часть сайта
 */
class Catalog_Frontend_Model extends Frontend_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Функция возвращает корневые категории; результат работы кэшируется
	 */
	public function getRootCategories() {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->rootCategories();
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()';
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает корневые категории
	 */
	protected function rootCategories() {
		$query = "SELECT `id`, `name` FROM `categories` WHERE `parent` = 0 ORDER BY `sortorder`";
		$root = $this->database->fetchAll($query, array());
		// добавляем в массив информацию об URL категорий
		foreach($root as $key => $value) {
			$root[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
		}
		return $root;
	}

	/**
	 * Функция возвращает массив корневых категорий и их детей в виде дерева;
	 * результат работы кэшируется
	 */
	public function getRootAndChilds() {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->rootAndChilds();
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()';
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает массив корневых категорий и их детей в виде дерева
	 */
	protected function rootAndChilds() {
		// получаем корневые категории и их детей
		$query = "SELECT `id`, `name`, `parent`
				  FROM `categories`
				  WHERE `parent` = 0 OR `parent` IN (SELECT `id` FROM `categories` WHERE `parent` = 0)
				  ORDER BY `sortorder`";
		$root = $this->database->fetchAll($query, array());
		// добавляем в массив информацию об URL категорий
		foreach($root as $key => $value) {
			$root[$key]['url'] = $this->getURL('frontend/catalog/category/id/' . $value['id']);
			if (isset($value['childs'])) {
				foreach($value['childs'] as $k => $v) {
					$root[$key]['childs'][$k]['url'] = $this->getURL('frontend/catalog/category/id/' . $v['id']);
				}
			}
		}
		// строим дерево
		$tree = $this->makeTree($root);
		return $tree;
	}

	/**
	 * Возвращает информацию о товаре с уникальным идентификатором $id;
	 * результат работы кэшируется
	 */
	public function getProduct($id) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->product($id);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Возвращает информацию о товаре с уникальным идентификатором $id
	 */
	protected function product($id) {
		$query = "SELECT
					  `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
					  `a`.`price` AS `price`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
					  `a`.`image` AS `image`, `a`.`purpose` AS `purpose`, `a`.`techdata` AS `techdata`,
					  `a`.`features` AS `features`, `a`.`complect` AS `complect`, `a`.`equipment` AS `equipment`,
					  `a`.`category2` AS `second`,
					  `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
					  `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`
				  FROM
					  `products` `a`
					  INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
					  INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
				  WHERE
					  `a`.`id` = :id AND `a`.`visible` = 1";
		$product = $this->database->fetch($query, array('id' => $id));
		if (false === $product) {
			return false;
		}
		// добавляем информацию о файлах документации
		$query = "SELECT
					  `a`.`id` AS `id`, `a`.`title` AS `title`,
					  `a`.`filename` AS `file`, `a`.`filetype` AS `type`
				  FROM
					  `docs` `a` INNER JOIN `doc_prd` `b`
					  ON `a`.`id`=`b`.`doc_id`
				  WHERE
					  `b`.`prd_id` = :id
				  ORDER BY
					  `a`.`title`";
		$docs = $this->database->fetchAll($query, array('id' => $id));
		$product['docs'] = $docs;
		return $product;
	}

	/**
	 * Возвращает информацию о категории с уникальным идентификатором $id;
	 * результат работы кэшируется
	 */
	public function getCategory($id) {
		$query = "SELECT
					  `name`, `description`, `keywords`, `parent`
				  FROM
					  `categories`
				  WHERE
					  `id` = :id";
		return $this->database->fetch($query, array('id' => $id), $this->enableCache);
	}

	/**
	 * Возвращает массив дочерних категорий категории с уникальным идентификатором
	 * $id с количеством товаров в каждой из них (и во всех дочерних); результат
	 * работы кэшируется
	 */
	public function getChildCategories($id, $maker = 0) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->childCategories($id, $maker);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Возвращает массив дочерних категорий категории с уникальным идентификатором
	 * $id с количеством товаров в каждой из них (и во всех дочерних)
	 */
	protected function childCategories($id, $maker = 0, $sort = 0) {
		$query = "SELECT
					  `id`, `name`
				  FROM
					  `categories`
				  WHERE
					  `parent` = :id
				  ORDER BY
					  `sortorder`";
		$childCategories = $this->database->fetchAll($query, array('id' => $id));
		foreach ($childCategories as $key => $value) {
			$childs = $this->getAllChildIds($value['id']);
			$childs[] = $value['id'];
			$ids = implode(',', $childs);
			$query = "SELECT
						  COUNT(*)
					  FROM
						  `products` `a`
						  INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
						  INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
					  WHERE
						  (`category` IN (" . $ids . ") OR `category2` IN (" . $ids . ")) AND `a`.`visible` = 1";
			// количество товаров в категории (и ее потомках)
			// при выключенном фильтре по производителю
			$count1 = $this->database->fetchOne($query, array());
			$count2 = $count1;
			if ($maker) {
				$query = $query . " AND `maker` = " . $maker;
				// количество товаров в категории (и ее потомках)
				// при включенном фильтре по производителю
				$count2 = $this->database->fetchOne($query, array());
			}
			// добавляем в массив информацию об URL дочерних категорий
			$url = 'frontend/catalog/category/id/' . $value['id'];
			$childCategories[$key]['count'] = $count1;
			if ($maker) {
				if ($count1 > $count2) {
					// если включен фильтр по производителю и при этом $count1 == $count2,
					// (в дочерней категории только товары выбранного производителя), то
					// нет смысла добавлять в URL фильтр по производителю, т.к. для URL
					// http://server.com/frontend/catalog/category/123 и для URL
					// http://server.com/frontend/catalog/category/123/maker/456
					// будет сформирован одинаковый результат
					$url = $url . '/maker/' . $maker;
				}
				$childCategories[$key]['count'] = $count2;
			}
			if ($sort) {
				$url = $url . '/sort/' . $sort;
			}
			$childCategories[$key]['url'] = $this->getURL($url);
		}

		return $childCategories;
	}

	/**
	 * Возвращает массив идентификаторов всех потомков категории $id, т.е.
	 * дочерние, дочерние дочерних и так далее; результат работы кэшируется
	 */
	public function getAllChildIds($id) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->allChildIds($id);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Возвращает массив идентификаторов всех потомков категории $id,
	 * т.е. дочерние, дочерние дочерних и т.п.
	 */
	protected function allChildIds($id) {
		$childs = array();
		$ids = $this->childIds($id);
		foreach ($ids as $item) {
			$childs[] = $item;
			$c = $this->allChildIds($item);
			foreach ($c as $v) {
				$childs[] = $v;
			}
		}
		return $childs;
	}


	/**
	 * Возвращает массив идентификаторов дочерних категорий (прямых потомков)
	 * категории с уникальным идентификатором $id
	 */
	private function childIds($id) {
		$query = "SELECT
					  `id`
				  FROM
					  `categories`
				  WHERE
					  `parent` = :id
				  ORDER BY
					  `sortorder`";
		$res = $this->database->fetchAll($query, array('id' => $id), $this->enableCache);
		$ids = array();
		foreach ($res as $item) {
			$ids[] = $item['id'];
		}
		return $ids;
	}

	/**
	 * Возвращает массив товаров категории $id и ее потомков, т.е. не только товары
	 * этой категории, но и товары дочерних категорий, товары дочерних-дочерних
	 * категорий и так далее; результат работы кэшируется
	 */
	public function getCategoryProducts($id, $maker = 0, $sortorder = 0, $start = 0) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->categoryProducts($id, $maker, $sortorder, $start);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id . '-maker-' . $maker . '-sortorder-' . $sortorder . '-start-' . $start;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Возвращает массив товаров категории $id и ее потомков, т.е. не только товары
	 * этой категории, но и товары дочерних категорий, товары дочерних-дочерних
	 * категорий и так далее
	 */
	protected function categoryProducts($id, $maker = 0, $sortorder = 0, $start = 0) {

		$childs = $this->getAllChildIds($id);
		$childs[] = $id;
		$ids = implode(',', $childs);
		$tmp = '';
		if ($maker) { // фильтр по производителю
			$tmp = " AND `maker` = " . $maker;
		}
		switch ($sortorder) { // сортировка
			case 0: $temp = '`b`.`globalsort`, `a`.`sortorder`';  break; // сортировка по умолчанию
			case 1: $temp = '`a`.`price`';      break;                   // сортировка по цене, по возрастанию
			case 2: $temp = '`a`.`price` DESC'; break;                   // сортировка по цене, по убыванию
			case 3: $temp = '`a`.`name`';       break;                   // сортировка по наименованию, по возрастанию
			case 4: $temp = '`a`.`name` DESC';  break;                   // сортировка по наименованию, по убыванию
			case 5: $temp = '`a`.`code`';       break;                   // сортировка по коду, по возрастанию
			case 6: $temp = '`a`.`code` DESC';  break;                   // сортировка по коду, по убыванию
		}

		$query = "SELECT
					  `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
					  `a`.`price` AS `price`, `a`.`unit` AS `unit`, `a`.`shortdescr` AS `shortdescr`,
					  `a`.`image` AS `image`,
					  `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`,
					  `c`.`id` AS `mkr_id`, `c`.`name` AS `mkr_name`
				  FROM
					  `products` `a`
					  INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
					  INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
				  WHERE
					  (`a`.`category` IN (" . $ids . ") OR `a`.`category2` IN (" . $ids . "))" . $tmp . "  AND `a`.`visible` = 1
				  ORDER BY " . $temp . "
				  LIMIT " . $start . ", " . $this->config->pager->frontend->products->perpage;
		$products = $this->database->fetchAll($query, array());

		// добавляем в массив товаров информацию об URL товаров, производителей, фото
		foreach($products as $key => $value) {
			// URL ссылки на страницу товара
			$products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
			// URL ссылки на страницу производителя
			$products[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
			// URL ссылки на фото товара
			if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
				$products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
			} else {
				$products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
			}
			// атрибут action тега form для добавления товара в корзину
			$products[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
			// атрибут action тега form для добавления товара в список отложенных
			$products[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
			// атрибут action тега form для добавления товара в список сравнения
			$products[$key]['action']['compared'] = $this->getURL('frontend/compared/addprd');
		}

		return $products;
	}

	/**
	 * Возвращает количество товаров в категории $id и в ее потомках, т.е.
	 * суммарное кол-во товаров не только в категории $id, но и в дочерних
	 * категориях и в дочерних-дочерних категориях и так далее; результат
	 * работы кэшируется
	 */
	public function getCountAllCategoryProducts($id, $maker = 0) {
		$childs = $this->getAllChildIds($id);
		$childs[] = $id;
		$ids = implode(',', $childs);
		$query = "SELECT
					  COUNT(*)
				  FROM
					  `products` `a`
					  INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
					  INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
				  WHERE
					  (`a`.`category` IN (" . $ids . ") OR `a`.`category2` IN (" . $ids . ")) AND `a`.`visible` = 1";
		if ($maker) {
			$query = $query . " AND `a`.`maker` = " . $maker;
		}
		return $this->database->fetchOne($query, array(), $this->enableCache);
	}

	/**
	 * Возвращает массив производителей товаров в категории $id и в ее потомках,
	 * т.е. не только производителей товаров этой категории, но и производителей
	 * товаров в дочерних категориях, производителей товаров в дочерних-дочерних
	 * категориях и так далее; результат работы кэшируется
	 */
	public function getCategoryMakers($id, $sort = 0) {
        // если не включено кэширование данных
        if (!$this->enableCache) {
            return $this->categoryMakers($id, $sort);
        }

        // уникальный ключ доступа к кэшу
        $key = __METHOD__ . '()-id-' . $id . '-sort-' . $sort;
        // имя этой функции (метода)
        $function = __FUNCTION__;
        // арументы, переданные этой функции
        $arguments = func_get_args();
        // получаем данные из кэша
        return $this->getCachedData($key, $function, $arguments);
	}

    /**
     * Возвращает массив производителей товаров в категории $id и в ее потомках,
     * т.е. не только производителей товаров этой категории, но и производителей
     * товаров в дочерних категориях, производителей товаров в дочерних-дочерних
     * категориях и так далее
     */
    protected function categoryMakers($id, $sort = 0) {
        $childs = $this->getAllChildIds($id);
        $childs[] = $id;
        $ids = implode(',', $childs);
        $query = "SELECT
					  `a`.`id` AS `id`, `a`. `name` AS `name`, COUNT(*) AS `count`
				  FROM
					  `makers` `a`
					  INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
					  INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
				  WHERE
					  (`b`.`category` IN (" . $ids . ") OR `b`.`category2` IN (" . $ids . ")) AND `b`.`visible` = 1
				  GROUP BY
					  `a`.`id`, `a`. `name`
				  ORDER BY
					  `a`.`name`";
        $makers = $this->database->fetchAll($query, array());
        // добавляем в массив производителей информацию об URL ссылок
        $count = 0; // кол-во товаров всех производителей
        foreach($makers as $key => $value) {
            $url = 'frontend/catalog/category/id/' . $id . '/maker/' . $value['id'];
            if ($sort) {
                $url = $url . '/sort/' . $sort;
            }
            $makers[$key]['url'] = $this->getURL($url);
            $count = $count + $value['count'];
        }
        // добавляем в начало массива ссылку для удаления фильтра по производителю
        $url = 'frontend/catalog/category/id/' . $id;
        if ($sort) {
            $url = $url . '/sort/' . $sort;
        }
        array_unshift(
            $makers,
            array('id' => 0, 'name' => 'Не важно', 'count' => $count, 'url' => $this->getURL($url))
        );
        return $makers;
    }

	/**
	 * Функция возвращает путь от корня каталога до категории с уникальным
	 * идентификатором $id; результат работы кэшируется
	 */
	public function getCategoryPath($id) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->categoryPath($id);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает путь от корня каталога до категории с уникальным
	 * идентификатором $id
	 */
	protected function categoryPath($id) {

		$path = array();
		$current = $id;
		while ($current) {
			$query = "SELECT `parent`, `name` FROM `categories` WHERE `id` = :current";
			$res = $this->database->fetch($query, array('current' => $current), $this->enableCache);
			$path[] = array(
				'url' => $this->getURL('frontend/catalog/category/id/' . $current),
				'name' => $res['name']
			);
			$current = $res['parent'];
		}
		$path[] = array('url' => $this->getURL('frontend/catalog/index'), 'name' => 'Каталог');
		$path[] = array('url' => $this->getURL('frontend/index/index'), 'name' => 'Главная');
		$path = array_reverse($path);
		return $path;

	}

	/**
	 * Функция возвращает массив категорий каталога для построения навигационной
	 * панели (дерево каталога + путь до текущей категории); результат работы
	 * кэшируется
	 */
	public function getCatalogMenu($id = 0) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->catalogMenu($id);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает массив категорий каталога для построения навигационной
	 * панели (дерево каталога + путь до текущей категории)
	 */
	protected function catalogMenu($id = 0) {
		$path = $this->getAllCategoryParents($id);
		return $this->catalogBranch($path, 0, $id);
	}

	/**
	 * Функция возвращает массив категорий каталога для построения навигационной
	 * панели (дерево каталога с одной раскрытой веткой)
	 */
	private function catalogBranch($path, $level, $id) {
		// этот код возвращает массив, где уровень вложенности задает переменная $level
		$query = "SELECT `id`, `name` FROM `categories` WHERE `parent` = :parent ORDER BY `sortorder`";
		$items = $this->database->fetchAll($query, array('parent' => $path[$level]), $this->enableCache);
		$result = array();
		foreach ($items as $item) {
			$result[] = array(
				'id' => $item['id'],
				'name' => $item['name'],
				'url' => $this->getURL('frontend/catalog/category/id/' . $item['id']),
				'level' => $level
			);
			if ($id == $item['id']) $result[count($result)-1]['current'] = true;
			// получить подкатегории?
			if (($level+1 < count($path)) && ($item['id'] == $path[$level+1])) {
				if ($level == 0) $result[count($result)-1]['opened'] = true;
				// рекурсивный вызов функции catalogBranch()
				$out = $this->catalogBranch($path, $level + 1, $id);
				// добавляем подкатегории в конец массива $result
				foreach ($out as $value) {
					$result[] = $value;
				}
			}
		}
		return $result;
		/*
		// этот код возвращает массив, где уровень вложенности определается наличием вложенного массива childs
		$query = "SELECT `id`, `name` FROM `categories` WHERE `parent` = :parent ORDER BY `sortorder`";
		$res = $this->database->fetchAll($query, array('parent' => $path[$level]), $this->cache);
		$result = array();
		foreach ($res as $i => $item) {
			$result[$i] = array('id' => $item['id'], 'name' => $item['name']);
			// получить подкатегории?
			if (($level+1 < count($path)) && ($item['id'] == $path[$level+1])) {
				// рекурсивный вызов функции getCatalogBranch()
				$out = $this->getCatalogBranch($path, $level + 1);
				// добавляем подкатегории текущей категории
				foreach ($out as $value) {
					$result[$i]['childs'][] = $value;
				}
			}
		}
		return $result;
		*/
	}

	/**
	 * Функция возвращает массив всех родителей категории с уникальным
	 * идентификатром $id; результат работы кэшируется
	 */
	private function getAllCategoryParents($id) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->allCategoryParents($id);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает массив всех родителей категории с уникальным
	 * идентификатром $id
	 */
	protected function allCategoryParents($id) {
		if ($id == 0) {
			return array(0 => 0);
		}
		$path = array();
		$path[] = $id;
		$current = $id;
		while ($current) {
			$query = "SELECT `parent` FROM `categories` WHERE `id` = :current";
			$res = $this->database->fetchOne($query, array('current' => $current), $this->enableCache);
			$path[] = $res;
			$current = $res;
		}
		$path = array_reverse($path);
		return $path;
	}

	/**
	 * Функция возвращает массив всех производителей (если $limit=0); результат
	 * работы кэшируется
	 */
	public function getAllMakers($limit = 0) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->allMakers($limit);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-limit-' . $limit;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает массив всех производителей (если $limit=0)
	 */
	protected function allMakers($limit = 0) {
		$query = "SELECT
					  `a`.`id` AS `id`, `a`.`name` AS `name`, COUNT(*) AS `count`
				  FROM
					  `makers` `a`
					  INNER JOIN `products` `b` ON `a`.`id` = `b`.`maker`
					  INNER JOIN `categories` `c` ON `b`.`category` = `c`.`id`
				  WHERE
					  `b`.`visible` = 1
				  GROUP BY
					  `a`.`id`, `a`.`name`
				  ORDER BY
					  `a`.`name`";
		if ($limit) {
			$query = $query . ' LIMIT ' . $limit;
		}
		$makers = $this->database->fetchAll($query, array());
		// добавляем в массив URL ссылок на страницы отдельных производителей
		foreach($makers as $key => $value) {
			$makers[$key]['url'] = $this->getURL('frontend/catalog/maker/id/' . $value['id']);
		}
		return $makers;
	}

	/**
	 * Функция возвращает информацию о производителе с уникальным идентификатором
	 * $id; результат работы кэшируется
	 */
	public function getMaker($id) {
		$query = "SELECT
					  `id`, `name`, `keywords`, `description`, `body`
				  FROM
					  `makers`
				  WHERE
					  `id` = :id";
		return $this->database->fetch($query, array('id' => $id), $this->enableCache);
	}

	/**
	 * Функция возвращает массив товаров производителя с уникальным идентификатором
	 * $id; результат работы кэшируется
	 */
	public function getMakerProducts($id, $sortorder = 0, $start = 0) {
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->makerProducts($id, $sortorder, $start);
		}

		// уникальный ключ доступа к кэшу
		$key = __METHOD__ . '()-id-' . $id . '-sortorder-' . $sortorder . '-start-' . $start;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает массив товаров производителя с уникальным идентификатором $id
	 */
	protected function makerProducts($id, $sortorder = 0, $start = 0) {
		switch ($sortorder) { // сортировка
			case 0: $temp = '`b`.`globalsort`, `a`.`sortorder`';  break; // сортировка по умолчанию
			case 1: $temp = '`a`.`price`';                        break; // сортировка по цене, по возрастанию
			case 2: $temp = '`a`.`price` DESC';                   break; // сортировка по цене, по убыванию
			case 3: $temp = '`a`.`name`';                         break; // сортировка по наименованию, по возрастанию
			case 4: $temp = '`a`.`name` DESC';                    break; // сортировка по наименованию, по убыванию
			case 5: $temp = '`a`.`code`';                         break; // сортировка по коду, по возрастанию
			case 6: $temp = '`a`.`code` DESC';                    break; // сортировка по коду, по убыванию
		}
		$query = "SELECT
					  `a`.`id` AS `id`, `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`title` AS `title`,
					  `a`.`image` AS `image`, `a`.`price` AS `price`, `a`.`unit` AS `unit`,
					  `a`.`shortdescr` AS `shortdescr`, `b`.`id` AS `ctg_id`, `b`.`name` AS `ctg_name`
				  FROM
					  `products` `a`
					  INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
				  WHERE
					  `a`.`maker` = :id AND `a`.`visible` = 1
				  ORDER BY " . $temp . "
				  LIMIT " . $start . ", " . $this->config->pager->frontend->products->perpage;
		$products = $this->database->fetchAll($query, array('id' => $id));
		// добавляем в массив URL ссылок на товары и фото
		foreach($products as $key => $value) {
			$products[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
			if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
				$products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
			} else {
				$products[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
			}
			// атрибут action тега form для добавления товара в корзину
			$products[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
			// атрибут action тега form для добавления товара в список отложенных
			$products[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
			// атрибут action тега form для добавления товара в список сравнения
			$products[$key]['action']['compared'] = $this->getURL('frontend/compared/addprd');
		}
		return $products;
	}

	/**
	 * Функция возвращает кол-во товаров производителя с уникальным идентификатором
	 * $id; результат работы кэшируется
	 */
	public function getCountMakerProducts($id) {
		$query = "SELECT
					  COUNT(*)
				  FROM
					  `products` `a` INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
				  WHERE
					  `a`.`maker` = :id AND `a`.`visible` = 1";
		return $this->database->fetchOne($query, array('id' => $id), $this->enableCache);
	}

	/**
	 * Функция возвращает единицы измерения товаров в каталоге
	 */
	public function getUnits() {
		$units = array(
			0 => 'руб',
			1 => 'руб/шт',
			2 => 'руб/компл',
			3 => 'руб/упак',
			4 => 'руб/метр',
			5 => 'руб/пара',
		);
		return $units;
	}

	/**
	 * Функция возвращает результаты поиска по каталогу; результат работы
	 * кэшируется
	 */
	public function getSearchResults($search = '', $start = 0, $ajax = false) {
		$search = $this->cleanSearchString($search);
		// если не включено кэширование данных
		if (!$this->enableCache) {
			return $this->searchResults($search, $start, $ajax);
		}

		// уникальный ключ доступа к кэшу
		$a = ($ajax) ? 'true' : 'false';
		$key = __METHOD__ . '()-search-' . md5($search) . '-start-' . $start . '-ajax-' . $a;
		// имя этой функции (метода)
		$function = __FUNCTION__;
		// арументы, переданные этой функции
		$arguments = func_get_args();
		// получаем данные из кэша
		return $this->getCachedData($key, $function, $arguments);
	}

	/**
	 * Функция возвращает результаты поиска по каталогу
	 */
	protected function searchResults($search = '', $start = 0, $ajax = false) {
		if (empty($search)) {
			return array();
		}
		$query = $this->getSearchQuery($search);
		if (empty($query)) {
			return array();
		}
		$query = $query . ' LIMIT ' . $start . ', ' . $this->config->pager->frontend->products->perpage;
		$result = $this->database->fetchAll($query, array());
		// добавляем в массив результатов поиска информацию об URL товаров и фото
		foreach($result as $key => $value) {
			if ($ajax) { // для поиска в шапке сайта
				$result[$key]['url'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
				unset(
					$result[$key]['shortdescr'],
					$result[$key]['image'],
					$result[$key]['ctg_id'],
					$result[$key]['ctg_name'],
					$result[$key]['mkr_id'],
					$result[$key]['ctg_name'],
					$result[$key]['relevance']
				);
			} else { // для страницы поиска
				// URL ссылки на страницу товара
				$result[$key]['url']['product'] = $this->getURL('frontend/catalog/product/id/' . $value['id']);
				// URL ссылки на страницу производителя
				$result[$key]['url']['maker'] = $this->getURL('frontend/catalog/maker/id/' . $value['mkr_id']);
				// URL ссылки на фото товара
				if ((!empty($value['image'])) && is_file('./files/catalog/imgs/small/' . $value['image'])) {
					$result[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/' . $value['image'];
				} else {
					$result[$key]['url']['image'] = $this->config->site->url . 'files/catalog/imgs/small/nophoto.jpg';
				}
				// атрибут action тега form для добавления товара в корзину
				$result[$key]['action']['basket'] = $this->getURL('frontend/basket/addprd');
				// атрибут action тега form для добавления товара в список отложенных
				$result[$key]['action']['wished'] = $this->getURL('frontend/wished/addprd');
				// атрибут action тега form для добавления товара в список сравнения
				$result[$key]['action']['compared'] = $this->getURL('frontend/compared/addprd');
			}
		}
		return $result;
	}

	/**
	 * Функция возвращает количество результатов поиска по каталогу
	 */
	public function getCountSearchResults($search = '') {
		$search = $this->cleanSearchString($search);
		if (empty($search)) {
			return 0;
		}
		$query = $this->getCountSearchQuery($search);
		if (empty($query)) {
			return 0;
		}
		return $this->database->fetchOne($query, array(), $this->enableCache);
	}

	/**
	 * Функция возвращает SQL-запрос для поиска по каталогу
	 */
	private function getSearchQuery($search) {
		if (empty($search)) {
			return '';
		}
		if (utf8_strlen($search) < 2) {
			return '';
		}
		// небольшок хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
		if (preg_match('#[a-zA-Zа-яА-ЯёЁ]{2,}\d{2,}#u', $search)) {
			preg_match_all('#[a-zA-Zа-яА-ЯёЁ]{2,}\d{2,}#u', $search, $temp1);
			$search = preg_replace('#([a-zA-Zа-яА-ЯёЁ]{2,})(\d{2,})#u', '$1 $2', $search );
		}
		if (preg_match('#\d{2,}[a-zA-Zа-яА-ЯёЁ]{2,}#u', $search)) {
			preg_match_all('#\d{2,}[a-zA-Zа-яА-ЯёЁ]{2,}#u', $search, $temp2);
			$search = preg_replace( '#(\d{2,})([a-zA-Zа-яА-ЯёЁ]{2,})#u', '$1 $2', $search );
		}
		$matches = array_merge(isset($temp1[0]) ? $temp1[0] : array(), isset($temp2[0]) ? $temp2[0] : array());

		$words = explode(' ', $search);
		$query = "SELECT
					  `a`.`id` AS `id`,
					  `a`.`code` AS `code`,
					  `a`.`name` AS `name`,
					  `a`.`title` AS `title`,
					  `a`.`price` AS `price`,
					  `a`.`unit` AS `unit`,
					  `a`.`shortdescr` AS `shortdescr`,
					  `a`.`image` AS `image`,
					  `b`.`id` AS `ctg_id`,
					  `b`.`name` AS `ctg_name`,
					  `c`.`id` AS `mkr_id`,
					  `c`.`name` AS `mkr_name`";

		$query = $query.", IF( LOWER(`a`.`name`) REGEXP '^".$words[0]."', 0.1, 0 ) + IF( LOWER(`a`.`title`) REGEXP '^".$words[0]."', 0.05, 0 )";

		$prd_name = 1.0; // коэффициент веса для `name`
		$length = utf8_strlen($words[0]);
		$weight = 0.5;
		if ($length < 5) {
			$weight = 0.1 * $length;
		}
		$query = $query." + ".$prd_name."*( IF( `a`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
		$query = $query." + IF( LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
		$query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
		// здесь просто выполняются действия для второго, третьего и т.п. слов поискового запроса,
		// как и для первого слова
		for ($i = 1; $i < count($words); $i++) {
			$length = utf8_strlen($words[$i]);
			$weight = 0.5;
			if ($length < 5) {
				$weight = 0.1 * $length;
			}
			$query = $query." + IF( `a`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
			$query = $query." + IF( LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
			$query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
		}
		// если слова расположены рядом в нужном порядке
		for ($i = 1; $i < count($words); $i++) {
			$query = $query." + IF( LOWER(`a`.`name`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0 )";
		}
		// если мы разделяли строку ABC123 на ABC и 123
		if (!empty($matches)) {
			foreach ($matches as $item) {
				$query = $query." + IF( `a`.`name` LIKE '%".$item."%', 0.1, 0 )";
			}
		}
		$query = $query." )";

		$prd_title = 0.8; // коэффициент веса для `title`
		$length = utf8_strlen($words[0]);
		$weight = 0.5;
		if ($length < 5) {
			$weight = 0.1 * $length;
		}
		$query = $query." + ".$prd_title."*( IF( `a`.`title` LIKE '%".$words[0]."%', ".$weight.", 0 )";
		$query = $query." - IF( `a`.`title` LIKE '%".$words[0]."%' AND `a`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
		$query = $query." + IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
		$query = $query." - IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.05, 0 )";
		$query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
		$query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.05, 0 )";
		// здесь просто выполняются действия для второго, третьего и т.п. слов поискового запроса,
		// как и для первого слова
		for ($i = 1; $i < count($words); $i++) {
			$length = utf8_strlen($words[$i]);
			$weight = 0.5;
			if ($length < 5) {
				$weight = 0.1 * $length;
			}
			$query = $query." + IF( `a`.`title` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
			$query = $query." - IF( `a`.`title` LIKE '%".$words[$i]."%' AND `a`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
			$query = $query." + IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
			$query = $query." - IF( LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.05, 0 )";
			$query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
			$query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.05, 0 )";
		}
		// если слова расположены рядом в нужном порядке
		for ($i = 1; $i < count($words); $i++) {
			$query = $query." + IF( LOWER(`a`.`title`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0 )";
			$query = $query." - IF( LOWER(`a`.`title`) REGEXP '".$words[$i-1].".?".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '".$words[$i-1].".?".$words[$i]."', 0.1, 0  )";
		}
		// если мы разделяли строку ABC123 на ABC и 123
		if (!empty($matches)) {
			foreach ($matches as $item) {
				$query = $query." + IF( `a`.`title` LIKE '%".$item."%', 0.1, 0 )";
			}
		}
		$query = $query." )";

		$prd_maker = 0.6; // коэффициент веса для `mkr_name`
		$length = utf8_strlen($words[0]);
		$weight = 0.5;
		if ($length < 5) {
			$weight = 0.1 * $length;
		}
		$query = $query." + ".$prd_maker."*( IF( `c`.`name` LIKE '%".$words[0]."%', ".$weight.", 0 )";
		$query = $query." - IF( (`c`.`name` LIKE '%".$words[0]."%' AND `a`.`name` LIKE '%".$words[0]."%') OR (`c`.`name` LIKE '%".$words[0]."%' AND `a`.`title` LIKE '%".$words[0]."%'), ".$weight.", 0 )";
		$query = $query." + IF( LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."', 0.1, 0 )";
		$query = $query." - IF( (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[0]."') OR (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[0]."' AND LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[0]."'), 0.1, 0 )";
		$query = $query." + IF( LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]', 0.1, 0 )";
		$query = $query." - IF( (LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[0]."[[:>:]]') OR (LOWER(`c`.`name`) REGEXP '".$words[0]."[[:>:]]' AND LOWER(`a`.`title`) REGEXP '".$words[0]."[[:>:]]'), 0.1, 0 )";
		// здесь просто выполняются действия для второго, третьего и т.п. слов поискового запроса,
		// как и для первого слова
		for ($i = 1; $i < count($words); $i++) {
			$length = utf8_strlen($words[$i]);
			$weight = 0.5;
			if ($length < 5) {
				$weight = 0.1 * $length;
			}
			$query = $query." + IF( `c`.`name` LIKE '%".$words[$i]."%', ".$weight.", 0 )";
			$query = $query." - IF( (`c`.`name` LIKE '%".$words[$i]."%' AND `a`.`name` LIKE '%".$words[$i]."%') OR (`c`.`name` LIKE '%".$words[$i]."%' AND `a`.`title` LIKE '%".$words[$i]."%'), ".$weight.", 0 )";
			$query = $query." + IF( LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."', 0.1, 0 )";
			$query = $query." - IF( (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`name`) REGEXP '[[:<:]]".$words[$i]."') OR (LOWER(`c`.`name`) REGEXP '[[:<:]]".$words[$i]."' AND LOWER(`a`.`title`) REGEXP '[[:<:]]".$words[$i]."'), 0.1, 0 )";
			$query = $query." + IF( LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]', 0.1, 0 )";
			$query = $query." - IF( (LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`name`) REGEXP '".$words[$i]."[[:>:]]') OR (LOWER(`c`.`name`) REGEXP '".$words[$i]."[[:>:]]' AND LOWER(`a`.`title`) REGEXP '".$words[$i]."[[:>:]]'), 0.1, 0 )";
		}
		$query = $query." )";

		$prd_code = 1.0; // коэффициент веса для `code`
		$codes = array();
		foreach($words as $word) {
			if (preg_match('#^\d{4}$#', $word)) $codes[] = '00'.$word;
			if (preg_match('#^\d{5}$#', $word)) $codes[] = '0'.$word;
			if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
		}
		if (count($codes) > 0) {
			$query = $query." + " . $prd_code . "*( IF( `a`.`code`='".$codes[0]."', 1.0, 0 )";
			for ($i = 1; $i < count($codes); $i++) {
				$query = $query." + IF( `a`.`code`='".$codes[$i]."', 1.0, 0 )";
			}
			$query = $query." )";
		}

		$query = $query." AS `relevance`";

		$query = $query." FROM
							  `products` `a`
							  INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
							  INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
						  WHERE (";

		$query = $query."`a`.`name` LIKE '%".$words[0]."%'";
		for ($i = 1; $i < count($words); $i++) {
			$query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
		}
		for ($i = 0; $i < count($words); $i++) {
			$query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
		}
		for ($i = 0; $i < count($words); $i++) {
			$query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
		}
		if (count($codes) > 0) {
			$query = $query." OR `a`.`code`='".$codes[0]."'";
			for ($i = 1; $i < count( $codes ); $i++) {
			  $query = $query." OR `a`.`code`='".$codes[$i]."'";
			}
		}
		$query = $query.") AND `a`.`visible` = 1";
		$query = $query." ORDER BY `relevance` DESC, LENGTH(`a`.`name`), `a`.`name`";

		return $query;
	}

	/**
	 * Функция возвращает SQL-запрос для поиска по каталогу
	 */
	private function getCountSearchQuery($search) {
		if (empty($search)) {
			return '';
		}
		if (utf8_strlen($search) < 2) {
			return '';
		}
		// небольшок хак: разделяем строку ABC123 на ABC и 123 (пример LG100 или NEC200)
		$search = preg_replace('#([a-zA-Zа-яА-ЯёЁ]{2,})(\d{2,})#u', '$1 $2', $search );
		$search = preg_replace( '#(\d{2,})([a-zA-Zа-яА-ЯёЁ]{2,})#u', '$1 $2', $search );

		$words = explode(' ', $search);
		$query = "SELECT
					  COUNT(*)
				  FROM
					  `products` `a`
					  INNER JOIN `categories` `b` ON `a`.`category` = `b`.`id`
					  INNER JOIN `makers` `c` ON `a`.`maker` = `c`.`id`
				  WHERE (";

		$query = $query."`a`.`name` LIKE '%".$words[0]."%'";
		for ($i = 1; $i < count($words); $i++) {
			$query = $query." OR `a`.`name` LIKE '%".$words[$i]."%'";
		}
		for ($i = 0; $i < count($words); $i++) {
			$query = $query." OR `a`.`title` LIKE '%".$words[$i]."%'";
		}
		for ($i = 0; $i < count($words); $i++) {
			$query = $query." OR `c`.`name` LIKE '%".$words[$i]."%'";
		}
		$codes = array();
		foreach($words as $word) {
			if (preg_match('#^\d{4}$#', $word)) $codes[] = '00'.$word;
			if (preg_match('#^\d{5}$#', $word)) $codes[] = '0'.$word;
			if (preg_match('#^\d{6}$#', $word)) $codes[] = $word;
		}
		if (count($codes) > 0) {
			$query = $query." OR `a`.`code`='".$codes[0]."'";
			for ($i = 1; $i < count( $codes ); $i++) {
				$query = $query." OR `a`.`code`='".$codes[$i]."'";
			}
		}
		$query = $query.") AND `a`.`visible` = 1";

		return $query;
	}

	/**
	 * Вспмогательная функция, очищает строку поискового запроса с сайта
	 * от всякого мусора
	 */
	private function cleanSearchString($search) {
		$search = utf8_substr($search, 0, 64);
		// удаляем все, кроме букв и цифр
		$search = preg_replace('#[^0-9a-zA-ZА-Яа-яёЁ]#u', ' ', $search);
		// сжимаем двойные пробелы
		$search = preg_replace('#\s+#u', ' ', $search);
		$search = trim($search);
		$search = utf8_strtolower($search);
		return $search;
	}

}
