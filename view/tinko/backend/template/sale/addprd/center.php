<?php
/**
 * Форма для добавления товара,
 * файл view/example/backend/template/sale/addprd/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $categories - массив всех категорий
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/sale/addprd/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новый товар</h1>

<?php if (!empty($errorMessage)): ?>
    <ul>
    <?php foreach ($errorMessage as $message): ?>
        <li><?php echo $message; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
    $code        = '';
    $name        = '';
    $title       = '';
    $category    = 0;
    $description = '';
    $price1      = '';
    $price2      = '';
    $count       = 1;

    if (isset($savedFormData)) {
        $code        = htmlspecialchars($savedFormData['code']);
        $name        = htmlspecialchars($savedFormData['name']);
        $title       = htmlspecialchars($savedFormData['title']);
        $category    = $savedFormData['category'];
        $description = htmlspecialchars($savedFormData['description']);
        $price1      = $savedFormData['price1'];
        if (empty($price1)) {
            $price1  = '';
        }
        $price2      = $savedFormData['price2'];
        if (empty($price2)) {
            $price2  = '';
        }
        $count       = $savedFormData['count'];
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
<div id="add-edit-product">
    <div>
        <div>Наименование, код</div>
        <div>
            <input type="text" name="name" maxlength="100" value="<?php echo $name; ?>" />
            <input type="text" name="code" maxlength="16" value="<?php echo $code; ?>" />
        </div>
    </div>
    <div>
        <div>Функциональное наименование</div>
        <div><input type="text" name="title" maxlength="200" value="<?php echo $title; ?>" /></div>
    </div>
    <div>
        <div>Цена, количество</div>
        <div>
            <input type="text" name="price1" value="<?php echo $price1; ?>" />
            <input type="text" name="price2" value="<?php echo $price2; ?>" />
            <input type="text" name="count" value="<?php echo $count; ?>" />
        </div>
    </div>
    <div>
        <div>Категория</div>
        <div>
            <select name="category">
            <option value="0">Выберите</option>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $item): ?>
                    <option value="<?php echo $item['id']; ?>"<?php if ($item['id'] == $category) echo 'selected="selected"'; ?>><?php echo $item['name']; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
            </select>
        </div>
    </div>
    <div>
        <div>Краткое описание</div>
        <div><textarea name="description"><?php echo $description; ?></textarea></div>
    </div>
    <div>
        <div>Изображение</div>
        <div><input type="file" name="image" /></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/sale/addprd/center.php -->
