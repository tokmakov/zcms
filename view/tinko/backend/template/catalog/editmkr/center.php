<?php
/**
 * Форма для добавления производителя,
 * файл view/example/backend/template/catalog/editmkr/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $id - уникальный идентификатор производителя
 * $name - наименование производителя
 * $altname - альтернативное наименование производителя
 * $keywords - содержимое мета-тега keywords
 * $description - содержимое мета-тега description
 * $brand - признак того, что призводитель является брендом
 * $popular - признак того, что это популярный бренд
 * $logo - файл изображения логотипа
 * $cert - файл изображения сертификата
 * $body - описание производителя в формате HTML
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже введенными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/catalog/editmkr/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование производителя</h1>

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
    $name        = htmlspecialchars($name);
    $altname     = htmlspecialchars($altname);
    $keywords    = htmlspecialchars($keywords);
    $description = htmlspecialchars($description);
    $body        = htmlspecialchars($body);

    if (isset($savedFormData)) {
        $name        = htmlspecialchars($savedFormData['name']);
        $altname     = htmlspecialchars($savedFormData['altname']);
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
        $brand       = $savedFormData['brand'];
        $popular     = $savedFormData['popular'];
        $body        = htmlspecialchars($savedFormData['body']);
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="add-edit-maker">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="64" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Наименование (alt)</div>
        <div><input type="text" name="altname" maxlength="64" value="<?php echo $altname; ?>" /></div>
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
        <div>Бренд, популярный</div>
        <div>
            <input type="checkbox" name="brand" value="1"<?php echo ($brand) ? ' checked="checked"' : ''; ?> /> бренд
            <input type="checkbox" name="popular" value="1"<?php echo ($popular) ? ' checked="checked"' : ''; ?> /> популярный
        </div>
    </div>
    <div>
        <div>Логотип</div>
        <div>
            <input type="file" name="logo" />
            <?php if ( ! empty($logo)): ?>
                <input type="checkbox" name="remove_logo" value="1" /> удалить
                <a href="<?php echo $logo; ?>" class="zoom">логотип</a>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div>Сертификат</div>
        <div>
            <input type="file" name="cert" />
            <?php if ( ! empty($cert)): ?>
                <input type="checkbox" name="remove_cert" value="1" /> удалить
                <a href="<?php echo $cert; ?>" class="zoom">сертификат</a>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div>Описание</div>
        <div><textarea name="body"><?php echo $body; ?></textarea></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/backend/template/catalog/editmkr/center.php -->
