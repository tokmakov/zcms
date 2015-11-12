<?php
/**
 * Форма для добавления нового пункта меню,
 * файл view/example/backend/template/menu/additem/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $menuItems - массив всех пунктов меню для возможности выбора родителя
 * $pages - массив всех страниц сайта
 * $catalogCategories - массив категорий каталога верхнего уровня
 * $newsCategories - массив категорий новостей
 * $solutionsCategories - массив категорий типовых решений
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/menu/additem/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Добавить пункт меню</h1>

<?php if (!empty($errorMessage)): ?>
    <ul>
    <?php foreach($errorMessage as $message): ?>
        <li><?php echo $message; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
$name   = '';
$url    = '';
$parent = 0;

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
                <?php if (!empty($pages)): ?>
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

                <?php if (!empty($catalogCategories)): ?>
                    <optgroup label="Каталог">
                        <option value="frontend/catalog/index">Каталог</option>
                        <?php foreach($catalogCategories as $category) : ?>
                            <option value="frontend/catalog/category/id/<?php echo $category['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if (!empty($newsCategories)): ?>
                    <optgroup label="Новости">
                        <option value="frontend/news/index">Новости</option>
                        <?php foreach($newsCategories as $category) : ?>
                            <option value="frontend/news/category/id/<?php echo $category['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if (!empty($solutionsCategories)): ?>
                    <optgroup label="Типовые решения">
                        <option value="frontend/solutions/index">Типовые решения</option>
                        <?php foreach($solutionsCategories as $category) : ?>
                            <option value="frontend/solutions/category/id/<?php echo $category['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category['name']; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
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
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                        <?php if (isset($item['childs'])): ?>
                            <?php foreach($item['childs'] as $child): ?>
                                <option value="<?php echo $child['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $child['name']; ?></option>
                                <?php if (isset($child['childs'])): ?>
                                    <?php foreach($child['childs'] as $value): ?>
                                        <option value="<?php echo $value['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value['name']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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

<!-- Конец шаблона view/example/backend/template/menu/additem/center.php -->
