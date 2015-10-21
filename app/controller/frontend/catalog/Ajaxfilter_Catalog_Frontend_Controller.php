<?php
/**
 * Класс Ajaxfilter_Catalog_Frontend_Controller формирует ответ на запрос XmlHttpRequest
 * в формате JSON, получает данные от модели Catalog_Frontend_Model, общедоступная
 * часть сайта. Ответ содержит результат фильтрации товаров выбранной категории
 */
class Ajaxfilter_Catalog_Frontend_Controller extends Catalog_Frontend_Controller {

	/**
	 * результат фильтрации товаров в формате JSON
	 */
	private $output;

	public function __construct($params = null) {
		if ( ! (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ) {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			die();
		}
		if ( ! $this->isPostMethod()) {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			die();
		}
		parent::__construct($params);
	}

	public function request() {

		// если не передан id категории или id категории не число
		if ( ! (isset($this->params['category']) && ctype_digit($this->params['category'])) ) {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			die();
		}

		// данные формы: фильтр по функционалу, производителю, параметрам; сортировка
		$group = 0; // функционал
		if (isset($_POST['group']) && ctype_digit($_POST['group'])  && $_POST['group'] > 0) {
			$group = (int)$_POST['group'];
		}
		$maker = 0; // производитель
		if (isset($_POST['maker']) && ctype_digit($_POST['maker'])  && $_POST['maker'] > 0) {
			$maker = (int)$_POST['maker'];
		}
        $param = array(); // параметры подбора
		if ($group && isset($_POST['param'])) {
			foreach ($_POST['param'] as $key => $value) {
				if ($key > 0 && ctype_digit($value) && $value > 0) {
					$param[$key] = (int)$value;
				}
			}
		}
        // если была выбрана новая функциональная группа, переданные параметры
        // подбора учитывать не надо, потому как у новой группы они будут другие
        if (isset($_POST['change']) && $_POST['change'] == '1') {
            $param = array();
        }
		$sort = 0; // сортировка
		if (isset($_POST['sort'])
			&& ctype_digit($_POST['sort'])
			&& in_array($_POST['sort'], array(1,2,3,4,5,6))
		) {
			$sort = (int)$_POST['sort'];
		}

		// получаем от модели массив дочерних категорий
		$childs = $this->catalogFrontendModel->getChildCategories($this->params['category'], $group, $maker, $param, $sort);

		// получаем от модели массив функциональных групп
		$groups = $this->catalogFrontendModel->getCategoryGroups($this->params['category'], $maker);

		// получаем от модели массив производителей
		$makers = $this->catalogFrontendModel->getCategoryMakers($this->params['category'], $group, $param);

		// получаем от модели массив параметров подбора
		$params = $this->catalogFrontendModel->getGroupParams($this->params['category'], $group, $maker, $param);

		// постраничная навигация
		$totalProducts = $this->catalogFrontendModel->getCountCategoryProducts($this->params['category'], $group, $maker, $param);

		$temp = new Pager(
			1,                                                  // текущая страница
			$totalProducts,                                     // общее кол-во товаров категории
			$this->config->pager->frontend->products->perpage,  // кол-во товаров на странице
			$this->config->pager->frontend->products->leftright // кол-во ссылок слева и справа
		);
		$pager = $temp->getNavigation();
		if (false === $pager) { // постраничная навигация не нужна
			$pager = null;
		}

		// получаем от модели массив товаров категории
		$products = $this->catalogFrontendModel->getCategoryProducts($this->params['category'], $group, $maker, $param, $sort);

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
		$sortorders = array();
		for ($i = 0; $i <= 6; $i++) {
			$url = 'frontend/catalog/category/id/' . $this->params['category'];
			if ($group) {
				$url = $url . '/group/' . $group;
			}
			if ($maker) {
				$url = $url . '/maker/' . $maker;
			}
			if ( ! empty($param)) {
				$temp = array();
				foreach ($param as $k => $v) {
					$temp[] = $k . '.' . $v;
				}
				$url = $url . '/param/' . implode('-', $temp);
			}
			if ($i) {
				$url = $url . '/sort/' . $i;
			}
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

		// URL этой страницы
		$url = 'frontend/catalog/category/id/' . $this->params['category'];
		if ($group) {
			$url = $url . '/group/' . $group;
		}
		if ($maker) {
			$url = $url . '/maker/' . $maker;
		}
		if ( ! empty($param)) {
			$temp = array();
			foreach ($param as $k => $v) {
				$temp[] = $k . '.' . $v;
			}
			$url = $url . '/param/' . implode('-', $temp);
		}
		if ($sort) {
			$url = $url . '/sort/' . $sort;
		}
		$thisPageUrl = $this->catalogFrontendModel->getURL($url);

		// формируем HTML результатов поиска
		$output = $this->render(
			$this->config->site->theme . '/frontend/template/catalog/ajax/filter.php',
			array(
				'id'          => $this->params['category'], // id категории
				'thisPageUrl' => $thisPageUrl,              // URL этой страницы
				'childs'      => $childs,                   // массив дочерних категорий
				'group'       => $group,                    // id выбранной функциональной группы или ноль
				'maker'       => $maker,                    // id выбранного производителя или ноль
				'param'       => $param,                    // массив выбранных параметров подбора
				'groups'      => $groups,                   // массив функциональных групп
				'makers'      => $makers,                   // массив производителей
				'params'      => $params,                   // массив всех параметров подбора
				'sort'        => $sort,                     // выбранная сортировка
				'sortorders'  => $sortorders,               // массив вариантов сортировки
				'units'       => $units,                    // массив единиц измерения товара
				'products'    => $products,                 // массив товаров категории
				'pager'       => $pager,                    // постраничная навигация
				'page'        => 1,                         // текущая страница
			)
		);
		$output = explode('¤', $output);
		// дочерние категории, подбор по параметрам, список товаров
		$result = array('childs' => $output[0], 'filters' => $output[1], 'products' => $output[2]);
		$this->output = json_encode($result);

	}

	public function getContentLength() {
		return strlen($this->output);
	}

	public function sendHeaders() {
		header('Content-type: application/json; charset=utf-8');
		header('Content-Length: ' . $this->getContentLength());
	}

	public function getPageContent() {
		return $this->output;
	}

}