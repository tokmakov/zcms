<?php
/**
 * Форма для редактирования пункта меню,
 * файл view/example/backend/template/menu/edititem/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $id - уникальный идентификатор пункта меню
 * $name - наименование пункта меню
 * $url - URL пункта меню
 * $parent - родитель пункта меню
 * $menuItems - массив всех пунктов меню для возможности выбора родителя
 * $pages - массив всех страниц сайта
 * $catalogCategories - массив категорий каталога верхнего уровня
 * $blogCategories - массив категорий блога
 * $solutionCategories - массив категорий типовых решений
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже отредактированными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/menu/edititem/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование пункта меню</h1>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
    $name   = htmlspecialchars($name);
    $url    = htmlspecialchars($url);
    $parent = $parent;

    if (isset($savedFormData)) {
        $name   = htmlspecialchars($savedFormData['name']);
        $url    = htmlspecialchars($savedFormData['url']);
        $parent = $savedFormData['parent'];
    }
?>

<form action="<?php echo $action; ?>" method="post">
<div id="add-edit-menu-item">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>URL</div>
        <div><input type="text" name="url" value="<?php echo $url; ?>" /></div>
    </div>
    <div>
        <div>Или выберите</div>
        <div>
            <select id="menu-item">
                <option value="0">Выберите</option>
                <?php if ( ! empty($pages)): ?>
                    <optgroup label="Страницы">
                    <?php foreach($pages as $page) : ?>
                        <option value="frontend/page/index/id/<?php echo $page['id']; ?>"><?php echo $page['name']; ?></option>
                        <?php if (isset($page['childs'])): ?>
                            <?php foreach($page['childs'] as $child): ?>
                                <option value="frontend/page/index/id/<?php echo $child['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $child['name']; ?></option>
                                <?php if (isset($child['childs'])): ?>
                                    <?php foreach($child['childs'] as $item): ?>
                                        <option value="frontend/page/index/id/<?php echo $item['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $item['name']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if ( ! empty($catalogCategories)): ?>
                    <optgroup label="Каталог">
                        <option value="frontend/catalog/index">Каталог</option>
                        <?php foreach($catalogCategories as $category) : ?>
                            <option value="frontend/catalog/category/id/<?php echo $category['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if ( ! empty($blogCategories)): ?>
                    <optgroup label="blog">
                        <option value="frontend/blog/index">Блог</option>
                        <?php foreach($blogCategories as $category) : ?>
                            <option value="frontend/blog/category/id/<?php echo $category['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if ( ! empty($solutionCategories)): ?>
                    <optgroup label="Типовые решения">
                        <option value="frontend/solution/index">Типовые решения</option>
                        <?php foreach($solutionsCategories as $category) : ?>
                            <option value="frontend/solution/category/id/<?php echo $category['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
                
                <optgroup label="Разное">
                    <option value="frontend/sale/index">Распродажа</option>
                    <option value="frontend/rating/index">Рейтинг</option>
                    <option value="frontend/sitemap/index">Карта сайта</option>
                </optgroup>
            </select>
        </div>
    </div>
    <div>
        <div>Родитель</div>
        <div>
            <select name="parent">
                <option value="0">Выберите</option>
                <?php if (!empty($menuItems)): ?>
                    <?php foreach($menuItems as $item): ?>
                        <option value="<?php echo $item['id']; ?>"<?php if ($item['id'] == $parent) echo ' selected="selected"'; ?>><?php echo $item['name']; ?></option>
                        <?php if (isset($item['childs'])): ?>
                            <?php foreach($item['childs'] as $child): ?>
                                <option value="<?php echo $child['id']; ?>"<?php if ($child['id'] == $parent) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $child['name']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/menu/edititem/center.php -->
