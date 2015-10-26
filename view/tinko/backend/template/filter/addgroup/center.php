<?php
/**
 * Старница с формой для добавления новой функциональной группы,
 * файл view/example/backend/template/filter/addgroup/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $allParams - массив всех параметров подбора
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/filter/addgroup/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Новая группа</h1>

<?php if (!empty($errorMessage)): ?>
    <ul>
    <?php foreach($errorMessage as $message): ?>
        <li><?php echo $message; ?></li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php
    $name = '';
    $params = array();

    if (isset($savedFormData)) {
        $name = htmlspecialchars($savedFormData['name']);
        $params = $savedFormData['params'];
    }
?>

<form action="<?php echo $action; ?>" method="post">
<div id="add-edit-group">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="100" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Параметры</div>
        <div>
        <?php if (!empty($allParams)): ?>
            <ul>
            <?php foreach ($allParams as $item): ?>
                <li><input type="checkbox" name="params[<?php echo $item['id']; ?>]"<?php echo in_array($item['id'], $params) ? ' checked="checked"' : ''; ?> value="1" /> <?php echo $item['name']; ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона шаблона view/example/backend/template/filter/addgroup/center.php -->
