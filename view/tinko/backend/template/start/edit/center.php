<?php
/**
 * Форма для редактирования главной страницы сайта,
 * файл view/example/backend/template/start/edit/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $name - заголовок главной страницы
 * $title - название главной страницы
 * $keywords - содержимое мета-тега keywords
 * $description - содержимое мета-тега description
 * $body - текст главной страницы в формате html
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже отредактированными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/start/edit/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование витрины</h1>

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
    $title       = htmlspecialchars($title);
    $keywords    = htmlspecialchars($keywords);
    $description = htmlspecialchars($description);
    $body        = htmlspecialchars($body);

    if (isset($savedFormData)) {
        $name        = htmlspecialchars($savedFormData['name']);
        $title       = htmlspecialchars($savedFormData['title']);
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
        $body        = htmlspecialchars($savedFormData['body']);
    }
?>

<form action="<?php echo $action; ?>" method="post">
<div id="edit-start-page">
    <div>
        <div>Заголовок страницы (html h1):</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Название страницы (html title):</div>
        <div><input type="text" name="title" maxlength="250" value="<?php echo $title; ?>" /></div>
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
        <div>Текст (содержание)</div>
        <div><textarea name="body"><?php echo $body; ?></textarea></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/start/edit/center.php -->
