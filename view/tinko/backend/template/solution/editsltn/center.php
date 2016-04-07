<?php
/**
 * Страница с формой для редактирования типового решения,
 * файл view/example/backend/template/solutions/editsltn/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $categories - массив категорий для возможности выбора родителя
 * $name - наименование типового решения
 * $category -  категория типового решения
 * $categories - массив всех категорий
 * $keywords - мета-тег keywords
 * $description - мета-тег description
 * $excerpt - краткое описание типового решения
 * $content1 - основное содержание типового решения
 * $content2 - дополнительное содержание типового решения
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/solutions/editsltn/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактировать типовое решение</h1>

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
    if (isset($savedFormData)) {
        $name        = htmlspecialchars($savedFormData['name']);
        $category    = $savedFormData['category'];
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
        $excerpt     = htmlspecialchars($savedFormData['excerpt']);
        $content1    = htmlspecialchars($savedFormData['content1']);
        $content2    = htmlspecialchars($savedFormData['content2']);
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
<div id="add-edit-solution">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="150" value="<?php echo $name; ?>" /></div>
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
        <div>Ключевые слова (meta)</div>
        <div><input type="text" name="keywords" maxlength="250" value="<?php echo $keywords; ?>" /></div>
    </div>
    <div>
        <div>Описание (meta)</div>
        <div><input type="text" name="description" maxlength="250" value="<?php echo $description; ?>" /></div>
    </div>
    <div>
        <div>Краткое описание</div>
        <div><textarea name="excerpt"><?php echo $excerpt; ?></textarea></div>
    </div>
    <div>
        <div>Содержание</div>
        <div><textarea name="content1"><?php echo $content1; ?></textarea></div>
    </div>
    <div>
        <div>Изображение</div>
        <div><input type="file" name="image" /></div>
    </div>
    <div>
        <div>Файл PDF</div>
        <div><input type="file" name="pdf" /></div>
    </div>
    <div>
        <div>Заключение</div>
        <div><textarea name="content2"><?php echo $content2; ?></textarea></div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона шаблона view/example/backend/template/solutions/editsltn/center.php -->
