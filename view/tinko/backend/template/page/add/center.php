<?php
/**
 * Форма для добавления новой страницы,
 * файл view/example/backend/template/page/add/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $pages - массив всех страниц (для возможности выбора родителя)
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже введенными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/page/add/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новая страница</h1>

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
    $name        = '';
    $title       = '';
    $sefurl      = '';
    $parent      = 0;
    $keywords    = '';
    $description = '';
    $body        = '';

    if (isset($savedFormData)) {
        $name        = htmlspecialchars($savedFormData['name']);
        $title       = htmlspecialchars($savedFormData['title']);
        $sefurl      = htmlspecialchars($savedFormData['sefurl']);
        $parent      = $savedFormData['parent'];
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
        $body        = htmlspecialchars($savedFormData['body']);
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
<div id="add-edit-page">
    <div>
        <div>Заголовок страницы (h1):</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Название страницы (title):</div>
        <div><input type="text" name="title" maxlength="250" value="<?php echo $title; ?>" /></div>
    </div>
    <div>
        <div>ЧПУ (SEF) страницы:</div>
        <div><input type="text" name="sefurl" maxlength="100" value="<?php echo $sefurl; ?>" /></div>
    </div>
    <div>
        <div>Родитель</div>
        <div>
            <select name="parent">
            <option value="0">Выберите</option>
            <?php if (!empty($pages)): ?>
                <?php foreach($pages as $page): ?>
                    <option value="<?php echo $page['id']; ?>"<?php if ($page['id'] == $parent) echo 'selected="selected"'; ?>><?php echo $page['name']; ?></option>
                    <?php if (isset($page['childs'])): ?>
                        <?php foreach($page['childs'] as $child): ?>
                            <option value="<?php echo $child['id']; ?>"<?php if ($child['id'] == $parent) echo 'selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $child['name']; ?></option>
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
        <div>Текст (содержание)</div>
        <div><textarea name="body"><?php echo $body; ?></textarea></div>
    </div>
    <div id="new-files">
        <div>Загрузить файл(ы)</div>
        <div>
            <div>
                <input type="file" name="files[]" />
                <span>Добавить</span>
                <span>Удалить</span>
            </div>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/page/add/center.php -->
