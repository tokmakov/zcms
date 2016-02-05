<?php
/**
 * Форма для редактирования статьи,
 * файл view/example/backend/template/article/edititem/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $id - уникальный идентификатор статьи
 * $name - заголовок статьи
 * $categories - массив всех категорий
 * $category - id категории
 * $keywords - содержимое мета-тега keywords
 * $description - содержимое мета-тега description
 * $excerpt - анонс статьи
 * $body - текст статьи в формате html
 * $date - текущая дата
 * $time - текущее время
 * $files - массив файлов
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже
 * отредактированными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/article/edititem/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование статьи</h1>

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
    $keywords    = htmlspecialchars($keywords);
    $description = htmlspecialchars($description);
    $excerpt     = htmlspecialchars($excerpt);
    $body        = htmlspecialchars($body);
    $date        = htmlspecialchars($date);
    $time        = htmlspecialchars($time);

    if (isset($savedFormData)) {
        $name        = htmlspecialchars($savedFormData['name']);
        $category    = $savedFormData['category'];
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
        $excerpt     = htmlspecialchars($excerpt);
        $body        = htmlspecialchars($savedFormData['body']);
        $date        = htmlspecialchars($savedFormData['date']);
        $time        = htmlspecialchars($savedFormData['time']);
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
<div id="add-edit-article">
    <div>
        <div>Заголовок статьи</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Категория</div>
        <div>
            <select name="category">
            <option value="0">Выберите</option>
            <?php if (!empty($categories)): ?>
                <?php foreach($categories as $ctg): ?>
                    <option value="<?php echo $ctg['id']; ?>"<?php if ($ctg['id'] == $category) echo ' selected="selected"'; ?>><?php echo $ctg['name']; ?></option>
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
        <div>Изображение</div>
        <div>
            <input type="file" name="image" />
            <?php if (is_file('files/article/' . $id . '/' . $id . '.jpg')): ?>
                <input type="checkbox" name="remove_image" value="1" /> удалить
                <a href="/files/article/<?php echo $id; ?>/<?php echo $id; ?>.jpg" class="zoom">изображение</a>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div>Анонс</div>
        <div><textarea name="excerpt"><?php echo $excerpt; ?></textarea></div>
    </div>
    <div>
        <div>Текст (содержание)</div>
        <div><textarea name="body"><?php echo $body; ?></textarea></div>
    </div>
    <div>
        <div>Дата и время</div>
        <div>
            <input type="text" name="date" value="<?php echo $date; ?>" />
            <input type="text" name="time" value="<?php echo $time; ?>" />
        </div>
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
    <?php if (!empty($files)): ?>
        <div id="old-files">
            <div>Уже загружены</div>
            <div>
                <?php foreach ($files as $file): ?>
                    <div>
                        <a href="/files/article/<?php echo $id . '/' . $file; ?>" target="_blank"><?php echo $file; ?></a>
                        <span>вставить</span>
                        <input type="checkbox" name="remove_files[]" value="<?php echo $file; ?>" /> удалить
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/article/edititem/center.php -->
