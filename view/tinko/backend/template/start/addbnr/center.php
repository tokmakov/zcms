<?php
/**
 * Форма для добавления баннера на главную страницу сайта,
 * файл view/example/backend/template/start/addbnr/center.php,
 * адиминистративная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже отредактированными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/start/addbnr/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новый баннер</h1>

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
    $name    = '';
    $url     = '';
    $alttext = '';
    $visible = 1;

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
        <div><input type="file" name="image" /></div>
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

<!-- Конец шаблона view/example/backend/template/start/addbnr/center.php -->
