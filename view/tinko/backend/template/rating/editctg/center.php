<?php
/**
 * Форма для редактирования категории,
 * файл view/example/backend/template/rating/editctg/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $name - наименование категории
 * $parent - родительская категория
 * $categories - массив категорий верхнего уровня, для возможности выбора родителя
 * 
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/rating/editctg/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование категории</h1>

<?php if (!empty($errorMessage)): ?>
    <ul>
    <?php foreach($errorMessage as $message): ?>
        <li><?php echo $message; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
    $name = htmlspecialchars($name);

    if (isset($savedFormData)) {
        $name   = htmlspecialchars($savedFormData['name']);
        $parent = $savedFormData['parent'];
    }
?>

<form action="<?php echo $action; ?>" method="post" id="add-edit-category">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Родитель</div>
        <div>
            <select name="parent">
            <option value="0">Выберите</option>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $item): ?>
                    <option value="<?php echo $item['id']; ?>"<?php if ($item['id'] == $parent) echo 'selected="selected"'; ?>><?php echo $item['name']; ?></option>
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

<!-- Конец шаблона view/example/backend/template/rating/editctg/center.php -->