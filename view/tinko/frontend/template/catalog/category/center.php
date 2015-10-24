<?php
/**
 * Категория каталога, список дочерних категорий + список товаров категории,
 * общедоступная часть сайта, файл view/example/frontend/template/catalog/category/center.php
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $id - уникальный идентификатор категории
 * $name - наименование категории
 * $thisPageUrl - URL этой страницы
 * $childCategories - массив дочерних категорий
 * $action - атрибут action тега форм
 * $group - id выбранной функциональной группы или ноль
 * $maker - id выбранного производителя или ноль
 * $hit - показывать только лидеров продаж?
 * $countHit - количество лидеров продаж
 * $new - показывать только новинки?
 * $countNew - количество новинок
 * $param - массив выбранных параметров подбора
 * $groups - массив функциональных групп
 * $makers - массив производителей
 * $params - массив всех параметров подбора
 * $sort - выбранная сортировка
 * $sortorders - массив всех вариантов сортировки
 * $units - массив единиц измерения товара
 * $products - массив товаров категории
 * $pager - постраничная навигация
 * $page - текущая страница
 *
 * $products = Array (
 *   [0] => Array (
 *     [id] => 37
 *     [code] => 001007
 *     [name] => ИП 212
 *     [title] => Извещатель пожарный дымовой
 *     [price] => 123.45
 *     [shortdescr] =>
 *     [ctg_id] => 2
 *     [ctg_name] => Извещатели пожарные
 *     [mkr_id] => 5
 *     [mkr_name] => Болид
 *     [url] => Array (
 *       [product] => /catalog/product/37
 *       [maker] => /catalog/maker/5
 *       [image] => /files/catalog/products/small/nophoto.jpg
 *     )
 *     [action] => Array (
 *       [basket] => /basket/addprd/37
 *       [wished] => /wished/addprd/37
 *       [compared] => /compared/addprd/37
 *     )
 *   )
 *   [1] => Array (
 *     .....
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * $groups = Array (
 *   [0] => Array (
 *     [id] => 11
 *     [name] => Видеокамеры корпусные
 *     [count] => 12
 *   )
 *   [1] => Array (
 *     [id] => 23
 *     [name] => Видеокамеры купольные
 *     [count] => 14
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * $params = Array (
 *   [0] => Array (
 *     [id] => 5
 *     [name] => Напряжение питания
 *     [values] => Array (
 *       [0] => Array (
 *         [id] => 7
 *         [name] => 12 Вольт
 *         [count] => 3
 *       )
 *       [1] => Array (
 *         [id] => 9
 *         [name] => 24 Вольт
 *         [count] => 5
 *       )
 *     )
 *   )
 *   [1] => Array (
 *     [id] => 8
 *     [name] => Встроенная ИК подсветка
 *     [values] => Array (
 *       [0] => Array (
 *         [id] => 11
 *         [name] => есть
 *         [count] => 4
 *       )
 *       [1] => Array (
 *         [id] => 14
 *         [name] => нет
 *         [count] => 6
 *       )
 *     )
 *   )
 * )
 *
 * ключ элемента массива - id параметра (например, «Напряжение питания»)
 * значение элемента массива - id значения (например, «12 Вольт»)
 * $param = Array (
 *   [5] => 7
 *   [8] => 11
 * )
 *
 * $makers = Array (
 *   [0] => Array (
 *     [id] => 380
 *     [name] => EverFocus
 *     [count] => 12
 *   )
 *   [1] => Array (
 *     [id] => 384
 *     [name] => MicroDigital
 *     [count] => 14
 *   )
 *   [2] => Array (
 *     .....
 *   )
 * )
 *
 * $sortorders = Array (
 *   [0] => Array (
 *     [url] => /catalog/category/1
 *     [name] => без сортировки
 *   )
 *   [1] => Array (
 *     [url] => /catalog/category/1/sort/1
 *     [name] => цена, возр.
 *   )
 *   [2] => Array (
 *     [url] => /catalog/category/1/sort/2
 *     [name] => цена, убыв.
 *   )
 *   [3] => Array (
 *     [url] => /catalog/category/1/sort/3
 *     [name] => название, возр.
 *   )
 *   [4] => Array (
 *     [url] => /catalog/category/1/sort/4
 *     [name] => название, убыв.
 *   )
 *   [5] => Array (
 *     [url] => /catalog/category/1/sort/5
 *     [name] => код, возр.
 *   )
 *   [6] => Array (
 *     [url] => /catalog/category/1/sort/6
 *     [name] => код, убыв.
 *   )
 * )
 *
 * $units = Array (
 *     0 => 'руб',
 *     1 => 'руб/шт',
 *     2 => 'руб/компл',
 *     3 => 'руб/упак',
 *     4 => 'руб/метр',
 *     5 => 'руб/пара',
 * )
 *
 * $pager = Array (
 *     [first] => 1
 *     [prev] => 2
 *     [current] => 3
 *     [next] => 4
 *     [last] => 5
 *     [left] => Array (
 *         [0] => 2
 *     )
 *     [right] => Array (
 *         [0] => 4
 *     )
 * )
 *
 */

defined('ZCMS') or die('Access denied');

/*
 * Варианты сортировки:
 * 0 - по умолчанию,
 * 1 - по цене, по возрастанию
 * 2 - по цене, по убыванию
 * 3 - по наименованию, по возрастанию
 * 4 - по наименованию, по убыванию
 * 5 - по коду, по возрастанию
 * 6 - по коду, по убыванию
 * Можно переопределить текст по умолчанию:
 */
for ($i = 0; $i <= 6; $i++) {
	switch ($i) {
		case 0: $text = 'прайс-лист'; $class = '';               break;
		case 1: $text = 'цена';       $class = 'sort-asc-blue';  break;
		case 2: $text = 'цена';       $class = 'sort-desc-blue'; break;
		case 3: $text = 'название';   $class = 'sort-asc-blue';  break;
		case 4: $text = 'название';   $class = 'sort-desc-blue'; break;
		case 5: $text = 'код';        $class = 'sort-asc-blue';  break;
		case 6: $text = 'код';        $class = 'sort-desc-blue'; break;
	}
	if ($sort && $i == $sort) {
		$class = str_replace('blue', 'orange', $class);
	}
	$sortorders[$i]['name'] = $text;
	$sortorders[$i]['class'] = $class;
}

?>

<!-- Начало шаблона view/example/frontend/template/catalog/category/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
	<div id="breadcrumbs">
	<?php foreach ($breadcrumbs as $item): ?>
		<a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
	<?php endforeach; ?>
	</div>
<?php endif; ?>

<h1><?php echo $name; ?></h1>

<?php if (!empty($childCategories)): // дочерние категории ?>
	<div id="category-childs">
		<div>Категории:</div>
		<div>
			<ul>
			<?php
				$divide = 0;
				$count = count($childCategories);
				if ($count > 5) {
					$divide = ceil($count/2);
				}
			?>
			<?php foreach ($childCategories as $key => $item): ?>
				<li>
					<?php if ($item['count']): // есть товары в категории? ?>
						<span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
					<?php else: ?>
						<span><span><?php echo $item['name']; ?></span> <span>0</span></span>
					<?php endif; ?>
				</li>
				<?php if ($divide && $divide == ($key+1)): ?>
					</ul><ul>
				<?php endif; ?>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>

<?php if (empty($products) && empty($groups) && empty($makers)): ?>
	<p>Нет товаров в этой категории.</p>
	<?php return; ?>
<?php endif; ?>

<?php if (!empty($groups) || !empty($makers)): // фильтры ?>
	<div id="category-filters">
		<div>
			<span>
			Фильтры:
				<?php if ($maker || $group): ?>
					<span class="selected">включены</span>
				<?php endif; ?>
			</span>
			<span><span>показать</span></span>
		</div>
		<div>
			<form action="<?php echo $action; ?>" method="post">
				<div>
					<div>
						<div>
							<span>Функциональное назначение</span>
						</div>
						<div>
							<span>
							<select name="group">
								<option value="0">Выберите</option>
								<?php foreach ($groups as $item): ?>
									<option value="<?php echo $item['id']; ?>"<?php echo ($item['id'] == $group) ? ' selected="selected"' : ''; ?><?php echo (!$item['count']) ? ' class="empty-option"' : ''; ?>><?php echo $item['name']; ?> [<?php echo $item['count']; ?>]</option>
								<?php endforeach; ?>
							</select>
							</span>
						</div>
					</div>
					<div>
						<div>
							<span>Производитель</span>
						</div>
						<div>
							<span>
							<select name="maker">
								<option value="0">Выберите</option>
								<?php foreach ($makers as $item): ?>
									<option value="<?php echo $item['id']; ?>"<?php echo ($item['id'] == $maker) ? ' selected="selected"' : ''; ?><?php echo (!$item['count']) ? ' class="empty-option"' : ''; ?>><?php echo $item['name']; ?> [<?php echo $item['count']; ?>]</option>
								<?php endforeach; ?>
							</select>
							</span>
						</div>
					</div>
					<?php if (!empty($params)): ?>
						<?php foreach ($params as $item): ?>
							<?php $selected = false; ?>
							<div>
								<div>
									<span><?php echo $item['name']; ?></span>
								</div>
								<div>
									<span>
									<select name="param[<?php echo $item['id']; ?>]">
										<option value="0">Выберите</option>
										<?php foreach ($item['values'] as $value): ?>
											<?php $selected = isset($param[$item['id']]) && $param[$item['id']] == $value['id']; ?>
											<option value="<?php echo $value['id']; ?>"<?php echo $selected ? ' selected="selected"' : ''; ?><?php echo (!$value['count']) ? ' class="empty-option"' : ''; ?>><?php echo htmlspecialchars($value['name']); ?> [<?php echo $value['count']; ?>]</option>
										<?php endforeach; ?>
									</select>
									</span>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
                    <div>
                        <div<?php echo empty($countHit) ? ' class="empty-checkbox"' : ''; ?>>
                            <label><input type="checkbox" name="hit"<?php echo $hit ? ' checked="checked"' : ''; ?> value="1" /> <span>Лидер продаж</span></label>
                        </div>
                        <div<?php echo empty($countNew) ? ' class="empty-checkbox"' : ''; ?>>
                            <label><input type="checkbox" name="new"<?php echo $new ? ' checked="checked"' : ''; ?> value="1" /> <span>Новинка</span></label>
                        </div>
                    </div>
				</div>
				<div>
					<?php if ($sort): ?>
						<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
					<?php endif; ?>
					<input type="hidden" name="change" value="0" />
					<input type="submit" name="submit" value="Применить" />
				</div>
			</form>
		</div>
	</div>
<?php endif; ?>

<div id="category-products">

	<?php if (empty($products)): ?>
		<p>По вашему запросу ничего не найдено.</p>
		<?php return; ?>
	<?php endif; ?>

	<div id="sort-orders">
		<ul>
			<li>Сортировка</li>
			<?php foreach ($sortorders as $key => $value): ?>
				<li>
					<?php if ($key == $sort): ?>
						<span class="selected<?php echo (!empty($value['class'])) ? ' ' . $value['class'] : ''; ?>"><?php echo $value['name']; ?></span>
					<?php else: ?>
						<a href="<?php echo $value['url']; ?>"<?php echo (!empty($value['class'])) ? ' class="' . $value['class'] . '"' : ''; ?>><span><?php echo $value['name']; ?></span></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="products-list-line">
		<?php foreach ($products as $product): ?>
			<div>
				<div class="product-line-heading">
					<h2><a href="<?php echo $product['url']['product']; ?>"><?php echo $product['name']; ?></a></h2>
					<?php if (!empty($product['title'])): ?>
						<h3><?php echo $product['title']; ?></h3>
					<?php endif; ?>
				</div>
				<div class="product-line-image">
					<a href="<?php echo $product['url']['product']; ?>"><img src="<?php echo $product['url']['image']; ?>" alt="" /></a>
				</div>
				<div class="product-line-info">
					<div>
						<span>Цена:</span>
						<span><strong><?php echo number_format($product['price'], 2, '.', ''); ?></strong> <?php echo $units[$product['unit']]; ?></span>
					</div>
					<div>
						<span>Код:</span>
						<span><strong><?php echo $product['code']; ?></strong></span>
					</div>
					<div>
						<span>Производитель:</span>
						<span><a href="<?php echo $product['url']['maker']; ?>"<?php echo ($maker) ? ' class="selected"' : ''; ?>><?php echo $product['mkr_name']; ?></a></span>
					</div>
				</div>
				<div class="product-line-basket">
					<form action="<?php echo $product['action']['basket']; ?>" method="post" class="add-basket-form">
						<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
						<input type="text" name="count" value="1" size="5" />
						<input type="hidden" name="return" value="category" />
						<input type="hidden" name="return_ctg_id" value="<?php echo $id; ?>" />
						<?php if ($maker): ?>
							<input type="hidden" name="maker" value="<?php echo $maker; ?>" />
						<?php endif; ?>
						<?php if ($sort): ?>
							<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
						<?php endif; ?>
						<?php if ($page > 1): ?>
							<input type="hidden" name="page" value="<?php echo $page; ?>" />
						<?php endif; ?>
						<input type="submit" name="submit" value="В корзину" title="Добавить в корзину" />
					</form>
					<form action="<?php echo $product['action']['wished']; ?>" method="post" class="add-wished-form">
						<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
						<input type="hidden" name="return" value="category" />
						<input type="hidden" name="return_ctg_id" value="<?php echo $id; ?>" />
						<?php if ($maker): ?>
							<input type="hidden" name="maker" value="<?php echo $maker; ?>" />
						<?php endif; ?>
						<?php if ($sort): ?>
							<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
						<?php endif; ?>
						<?php if ($page > 1): ?>
							<input type="hidden" name="page" value="<?php echo $page; ?>" />
						<?php endif; ?>
						<input type="submit" name="submit" value="Отложить" title="Добавить в отложенные" />
					</form>
					<form action="<?php echo $product['action']['compared']; ?>" method="post" class="add-compared-form">
						<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
						<input type="hidden" name="return" value="category" />
						<input type="hidden" name="return_ctg_id" value="<?php echo $id; ?>" />
						<?php if ($maker): ?>
							<input type="hidden" name="maker" value="<?php echo $maker; ?>" />
						<?php endif; ?>
						<?php if ($sort): ?>
							<input type="hidden" name="sort" value="<?php echo $sort; ?>" />
						<?php endif; ?>
						<?php if ($page > 1): ?>
							<input type="hidden" name="page" value="<?php echo $page; ?>" />
						<?php endif; ?>
						<input type="submit" name="submit" value="К сравнению" title="Добавить к сравнению" />
					</form>
				</div>
				<div class="product-line-descr"><?php echo $product['shortdescr']; ?></div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if (!empty($pager)): // постраничная навигация ?>
		<ul class="pager">
		<?php if (isset($pager['first'])): ?>
			<li>
				<a href="<?php echo $thisPageUrl; ?><?php echo ($pager['first'] != 1) ? '/page/'.$pager['first'] : ''; ?>" class="first-page"></a>
			</li>
		<?php endif; ?>
		<?php if (isset($pager['prev'])): ?>
			<li>
				<a href="<?php echo $thisPageUrl; ?><?php echo ($pager['prev'] != 1) ? '/page/'.$pager['prev'] : ''; ?>" class="prev-page"></a>
			</li>
		<?php endif; ?>
		<?php if (isset($pager['left'])): ?>
			<?php foreach ($pager['left'] as $left) : ?>
				<li>
					<a href="<?php echo $thisPageUrl; ?><?php echo ($left != 1) ? '/page/'.$left : ''; ?>"><?php echo $left; ?></a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>

			<li>
				<span><?php echo $pager['current']; // текущая страница ?></span>
			</li>

		<?php if (isset($pager['right'])): ?>
			<?php foreach ($pager['right'] as $right) : ?>
				<li>
					<a href="<?php echo $thisPageUrl; ?>/page/<?php echo $right; ?>"><?php echo $right; ?></a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if (isset($pager['next'])): ?>
			<li>
				<a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['next']; ?>" class="next-page"></a>
			</li>
		<?php endif; ?>
		<?php if (isset($pager['last'])): ?>
			<li>
				<a href="<?php echo $thisPageUrl; ?>/page/<?php echo $pager['last']; ?>" class="last-page"></a>
			</li>
		<?php endif; ?>
		</ul>
	<?php endif; ?>

</div>

<!-- Конец шаблона view/example/frontend/template/catalog/category/center.php -->

