<?php
/**
 * Форма для добавления партнера компании,
 * файл view/example/backend/template/partner/add/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $expire - срок действия сертификата
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки,
 * мы должны снова предъявить форму, заполненную уже отредактированными данными и вывести
 * сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/partner/add/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новый партнер</h1>

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
    $alttext = '';
    $expire  = htmlspecialchars($expire);

    if (isset($savedFormData)) {
        $name    = htmlspecialchars($savedFormData['name']);
        $alttext = htmlspecialchars($savedFormData['alttext']);
        $expire  = $savedFormData['expire'];
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="add-edit-partner">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="100" value="<?php echo $name; ?>" /></div>
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
        <div>Действителен до</div>
        <div><input type="text" name="expire" value="<?php echo $expire; ?>" /></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/backend/template/partner/add/center.php -->
