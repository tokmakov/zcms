<?php
/**
 * Форма для редактирования товара рейтинга продаж,
 * файл view/example/backend/template/rating/editprd/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $code - код (артикул) товара
 * $name - наименование товара
 * $title - функциональное наименование изделия
 * $category - родительская категория товара
 * $categories - массив всех категорий
 *
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/rating/editprd/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование товара</h1>

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
    $code  = htmlspecialchars($code);
    $name  = htmlspecialchars($name);
    $title = htmlspecialchars($title);

    if (isset($savedFormData)) {
        $code     = htmlspecialchars($savedFormData['code']);
        $name     = htmlspecialchars($savedFormData['name']);
        $title    = htmlspecialchars($savedFormData['title']);
        $category = $savedFormData['category'];
    }
?>

<form action="<?php echo $action; ?>" method="post" id="add-edit-product">
    <div>
        <div>Код (артикул)</div>
        <div><input type="text" name="code" maxlength="16" value="<?php echo $code; ?>" /> <span id="load-by-code">Загрузить товар</span></div>
    </div>
    <div>
        <div>Торговое наименование</div>
        <div>
            <input type="text" name="name" maxlength="100" value="<?php echo $name; ?>" />
        </div>
    </div>
    <div>
        <div>Функциональное наименование</div>
        <div><input type="text" name="title" maxlength="200" value="<?php echo $title; ?>" /></div>
    </div>
    <div>
        <div>Категория</div>
        <div>
            <select name="category">
            <option value="0">Выберите</option>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $item): ?>
                    <optgroup label="<?php echo htmlspecialchars($item['name']); ?>">
                    <?php if (isset($item['childs'])): ?>
                        <?php foreach($item['childs'] as $child): ?>
                            <option value="<?php echo $child['id']; ?>"<?php if ($child['id'] == $category) echo 'selected="selected"'; ?>><?php echo $child['name']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </optgroup>
                <?php endforeach; ?>
            <?php endif; ?>
            </select>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/backend/template/rating/editprd/center.php -->
