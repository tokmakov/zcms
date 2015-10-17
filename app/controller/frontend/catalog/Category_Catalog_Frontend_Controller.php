<?php
/**
 * Класс Category_Catalog_Frontend_Controller формирует страницу категории каталога,
 * т.е. список дочерних категорий и список товаров категории, получает данные от
 * модели Catalog_Frontend_Model, общедоступная часть сайта
 */
class Category_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

	public function __construct($params = null) {
		parent::__construct($params);
	}

	/**
	 * Функция получает от модели данные, необходимые для формирования страницы
	 * категории каталога, т.е. список дочерних категорий и список товаров категории
	 */
	protected function input() {

		/*
		 * сначала обращаемся к родительскому классу Catalog_Frontend_Controller,
		 * чтобы установить значения переменных, которые нужны для работы всех его
		 * потомков, потом переопределяем эти переменные (если необходимо) и
		 * устанавливаем значения перменных, которые нужны для работы только
		 * Category_Catalog_Frontend_Controller
		 */
		parent::input();

		// если не передан id категории или id категории не число
		if ( ! (isset($this->params['id']) && ctype_digit($this->params['id'])) ) {
			$this->notFoundRecord = true;
			return;
		}

		// если данные формы были отправлены (выбор функциональной группы или производителя)
		if ($this->isPostMethod()) {
			$url = 'frontend/catalog/category/id/' . $this->params['id'];
			$grp = false;
			if (isset($_POST['group']) && ctype_digit($_POST['group'])  && $_POST['group'] > 0) {
				$url = $url . '/group/' . $_POST['group'];
				$grp = true;
			}
			if (isset($_POST['maker']) && ctype_digit($_POST['maker'])  && $_POST['maker'] > 0) {
				$url = $url . '/maker/' . $_POST['maker'];
			}
			if ($grp && isset($_POST['param'])) {
				$param = array();
				foreach ($_POST['param'] as $key => $value) {
					if ($key > 0 && is_array($value)) {
						foreach ($value as $v) {
							if (ctype_digit($v) && $v > 0) {
								$param[$key][] = (int)$v;
							}
						}
					}
				}
				if (!empty($param)) {
					$ids = array();
					foreach ($param as $key => $value) {
						$ids[] = $key . '.' . implode('.', $value);
					}
					$url = $url . '/param/' . implode('-', $ids);
				}
			}
			if (isset($_POST['sort'])
				&& ctype_digit($_POST['sort'])
				&& in_array($_POST['sort'], array(1,2,3,4,5,6))
			) {
				$url = $url . '/sort/' . $_POST['sort'];
			}
			$this->redirect($this->catalogFrontendModel->getURL($url));
		}

		/*
		 * получаем от модели данные, необходимые для формирования страницы категории, и
		 * записываем их в массив переменных, который будет передан в шаблон center.php
		 */
		$this->getCategory();
		// товар не найден в таблице БД categories
		if ($this->notFoundRecord) {
			return;
		}

		// переопределяем переменную, которая будет передана в шаблон left.php,
		// чтобы раскрыть ветку текущей категории
		$this->leftVars['catalogMenu'] = $this->catalogFrontendModel->getCatalogMenu($this->params['id']);
		// помечаем текущую категорию

	}

	/**
	 * Функция получает от модели данные о категории и сохраняет их в массиве,
	 * который будет передан в шаблон center.php
	 */
	private function getCategory() {

		// получаем от модели информацию о категории
		$category = $this->catalogFrontendModel->getCategory($this->params['id']);
		// если запрошенная категория не найдена в БД
		if (empty($category)) {
			$this->notFoundRecord = true;
			return;
		}

		$this->title = $category['name'];

		if (!empty($category['keywords'])) {
			$this->keywords = $category['keywords'];
		}
		if (!empty($category['description'])) {
			$this->description = $category['description'];
		}

		// формируем хлебные крошки
		$breadcrumbs = $this->catalogFrontendModel->getCategoryPath($this->params['id']); // путь до категории
		array_pop($breadcrumbs); // последний элемент - текущая категория, нам она не нужна

		// включен фильтр по функциональной группе?
		$group = 0;
		if (isset($this->params['group']) && ctype_digit($this->params['group'])) {
			$group = (int)$this->params['group'];
		}

		// включен фильтр по производителю?
		$maker = 0;
		if (isset($this->params['maker']) && ctype_digit($this->params['maker'])) {
			$maker = (int)$this->params['maker'];
		}

		// включен фильтр по параметрам?
		$param = array();
		if ($group && isset($this->params['param']) && preg_match('~^\d+\.\d+(\.\d+)*(-\d+\.\d+(\.\d)*)*$~', $this->params['param'])) {
			$temp = explode('-', $this->params['param']);
			foreach ($temp as $value) {
				$tmp = explode('.', $value);
				$key = (int)array_shift($tmp);
				foreach ($tmp as $v) {
					$param[$key][] = (int)$v;
				}
			}
			/*
			if ( ! $this->catalogFrontendModel->isValidParams($param)) {
				$this->notFoundRecord = true;
				return;
			}
			*/
		}

		// включена сортировка?
		$sort = 0;
		if (isset($this->params['sort'])
			&& ctype_digit($this->params['sort'])
			&& in_array($this->params['sort'], array(1,2,3,4,5,6))
		) {
			$sort = (int)$this->params['sort'];
		}

		// мета-тег robots
		if ($maker || $sort) {
			$this->robots = false;
		}

		// получаем от модели массив дочерних категорий
		$childCategories = $this->catalogFrontendModel->getChildCategories($this->params['id'], $group, $maker, $param, $sort);

		// получаем от модели массив функциональных групп
		$groups = $this->catalogFrontendModel->getCategoryGroups($this->params['id'], $group, $maker, $param);

		// получаем от модели массив производителей
		$makers = $this->catalogFrontendModel->getCategoryMakers($this->params['id'], $group, $maker, $param);

		// постраничная навигация
		$page = 1;
		if (isset($this->params['page']) && ctype_digit($this->params['page'])) {
			$page = $this->params['page'];
		}
		// общее кол-во товаров категории
		$totalProducts = $this->catalogFrontendModel->getCountCategoryProducts($this->params['id'], $group, $maker, $param);

		$temp = new Pager(
			$page,                                              // текущая страница
			$totalProducts,                                     // общее кол-во товаров категории
			$this->config->pager->frontend->products->perpage,  // кол-во товаров на странице
			$this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
		);
		$pager = $temp->getNavigation();
		if (is_null($pager)) { // недопустимое значение $page (за границей диапазона)
			$this->notFoundRecord = true;
			return;
		}
		if (false === $pager) { // постраничная навигация не нужна
			$pager = null;
		}
		// стартовая позиция для SQL-запроса
		$start = ($page - 1) * $this->config->pager->frontend->products->perpage;

		// получаем от модели массив товаров категории
		$products = $this->catalogFrontendModel->getCategoryProducts($this->params['id'], $group, $maker, $param, $sort, $start);

		// получаем от модели массив всех параметров подбора
		$params = $this->catalogFrontendModel->getCategoryParams($this->params['id'], $group, $maker, $param);

		/*
		 * Варианты сортировки:
		 * 0 - по умолчанию,
		 * 1 - по цене, по возрастанию
		 * 2 - по цене, по убыванию
		 * 3 - по наименованию, по возрастанию
		 * 4 - по наименованию, по убыванию
		 * 5 - по коду, по возрастанию
		 * 6 - по коду, по убыванию
		 */
		for ($i = 0; $i <= 6; $i++) {
			$url = 'frontend/catalog/category/id/' . $this->params['id'];
			if ($maker) {
				$url = $url . '/maker/' . $maker;
			}
			if ($i) {
				$url = $url . '/sort/' . $i;
			}
			/*
			if ($page > 1) {
				$url = $url . '/page/' . $page;
			}
			*/
			switch ($i) {
				case 0: $name = 'без сортировки';  break;
				case 1: $name = 'цена, возр.';     break;
				case 2: $name = 'цена, убыв.';     break;
				case 3: $name = 'название, возр.'; break;
				case 4: $name = 'название, убыв.'; break;
				case 5: $name = 'код, возр.';      break;
				case 6: $name = 'код, убыв.';      break;
			}
			$sortorders[$i] = array(
				'url' => $this->catalogFrontendModel->getURL($url),
				'name' => $name
			);
		}

		// единицы измерения товара
		$units = $this->catalogFrontendModel->getUnits();

		// URL этой страницы, атрибут action тега форм
		$url = $action = 'frontend/catalog/category/id/' . $this->params['id'];
		$action = $this->catalogFrontendModel->getURL($action);
		if ($group) {
			$url = $url . '/group/' . $group;
		}
		if ($maker) {
			$url = $url . '/maker/' . $maker;
		}
		if (!empty($param)) {
			$url = $url . '/param/' . $this->params['param'];
		}
		if ($sort) {
			$url = $url . '/sort/' . $sort;
		}
		$thisPageUrl = $this->catalogFrontendModel->getURL($url);

		/*
		 * массив переменных, которые будут переданы в шаблон center.php
		 */
		$this->centerVars = array(
			'breadcrumbs'     => $breadcrumbs,        // хлебные крошки
			'id'              => $this->params['id'], // уникальный идентификатор категории
			'name'            => $category['name'],   // наименование категории
			'thisPageUrl'     => $thisPageUrl,        // URL этой страницы
			'childCategories' => $childCategories,    // массив дочерних категорий
			'action'          => $action,             // атрибут action тега форм
			'group'           => $group,              // id выбранной функциональной группы или ноль
			'maker'           => $maker,              // id выбранного производителя или ноль
			'param'           => $param,              // массив выбранных параметров подбора
			'groups'          => $groups,             // массив функциональных групп
			'makers'          => $makers,             // массив производителей
			'params'          => $params,             // массив всех параметров подбора
			'sort'            => $sort,               // выбранная сортировка
			'sortorders'      => $sortorders,         // массив вариантов сортировки
			'units'           => $units,              // массив единиц измерения товара
			'products'        => $products,           // массив товаров категории
			'pager'           => $pager,              // постраничная навигация
			'page'            => $page,               // текущая страница
		);

	}

}
