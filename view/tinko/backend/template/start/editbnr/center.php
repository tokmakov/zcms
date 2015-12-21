<?php
/**
 * Форма для редактирования баннера на главной странице сайта,
 * файл view/example/backend/template/start/editbnr/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $id - уникальный идентификатор баннера
 * $name - наименование баннера
 * $url - URL ссылки с баннера
 * $alttext - alt текст баннера
 * $visible - показывать баннер?
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже отредактированными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/start/editbnr/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование баннера</h1>

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
    $name    = htmlspecialchars($name);
    $url     = htmlspecialchars($url);
    $alttext = htmlspecialchars($alttext);

    if (isset($savedFormData)) {
        $name    = htmlspecialchars($savedFormData['name']);
        $url     = htmlspecialchars($savedFormData['url']);
        $alttext = htmlspecialchars($savedFormData['alttext']);
        $visible = $savedFormData['visible'];
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
<div id="add-edit-banner">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="100" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Url ссылки</div>
        <div><input type="text" name="url" maxlength="250" value="<?php echo $url; ?>" /></div>
    </div>
    <div>
        <div>Alt текст</div>
        <div><input type="text" name="alttext" maxlength="100" value="<?php echo $alttext; ?>" /></div>
    </div>
    <div>
        <div>Изображение</div>
        <div>
            <input type="file" name="image" />
            <?php if (is_file('./files/index/banners/' . $id . '.jpg')): ?>
                <input type="checkbox" name="remove_image" value="1" /> удалить
                <a href="/files/index/banners/<?php echo $id; ?>.jpg" class="zoom">изображение</a>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="checkbox" name="visible" value="1"<?php echo ($visible) ? ' checked="checked"' : ''; ?> /> показывать</div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/start/editbnr/center.php -->
