<?php
/**
 * Форма для редактирования категории,
 * файл view/example/backend/template/catalog/editctg/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $id - уникальный идентификатор категории
 * $parent - родительская категория
 * $categories - массив всех категорий, для возможности выбора родителя
 * $name - наименование категории
 * $keywords - содержимое мета-тега keywords
 * $description - содержимое мета-тега description
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже
 * отредактированными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/catalog/editctg/center.php -->

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
    $name        = htmlspecialchars($name);
    $parent      = $parent;
    $keywords    = htmlspecialchars($keywords);
    $description = htmlspecialchars($description);

    if (isset($savedFormData)) {
        $name        = htmlspecialchars($savedFormData['name']);
        $parent      = $savedFormData['parent'];
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
    }
?>

<form action="<?php echo $action; ?>" method="post">
<div id="add-edit-category">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Родитель</div>
        <div>
            <select name="parent">
                <option value="0">Выберите</option>
                <?php if (isset($categories) && is_array($categories) && count($categories) > 0): ?>
                    <?php foreach($categories as $value1): ?>
                        <option value="<?php echo $value1['id']; ?>"<?php if ($value1['id'] == $parent) echo ' selected="selected"'; ?>><?php echo $value1['name']; ?></option>
                        <?php if (isset($value1['childs'])): ?>
                            <?php foreach($value1['childs'] as $value2): ?>
                                <option value="<?php echo $value2['id']; ?>"<?php if ($value2['id'] == $parent) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value2['name']; ?></option>
                                <?php if (isset($value2['childs'])): ?>
                                    <?php foreach($value2['childs'] as $value3): ?>
                                        <option value="<?php echo $value3['id']; ?>"<?php if ($value3['id'] == $parent) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value3['name']; ?></option>
                                        <?php if (isset($value3['childs'])): ?>
                                            <?php foreach($value3['childs'] as $value4): ?>
                                                <option value="<?php echo $value4['id']; ?>"<?php if ($value4['id'] == $parent) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value4['name']; ?></option>
                                                <?php if (isset($value4['childs'])): ?>
                                                    <?php foreach($value4['childs'] as $value5): ?>
                                                        <option value="<?php echo $value5['id']; ?>"<?php if ($value5['id'] == $parent) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value5['name']; ?></option>
                                                        <?php if (isset($value5['childs'])): ?>
                                                            <?php foreach($value5['childs'] as $value6): ?>
                                                                <option value="<?php echo $value6['id']; ?>"<?php if ($value6['id'] == $parent) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value6['name']; ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
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
        <div>Ключевые слова (meta)</div>
        <div><input type="text" name="keywords" maxlength="250" value="<?php echo $keywords; ?>" /></div>
    </div>
    <div>
        <div>Описание (meta)</div>
        <div><input type="text" name="description" maxlength="250" value="<?php echo $description; ?>" /></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/catalog/editctg/center.php -->
