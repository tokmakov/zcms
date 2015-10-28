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
 * $allValues - массив всех значений параметров подбора
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 *
 * $allParams = Array (
 *   [0] => Array (
 *     [id] => 5
 *     [name] => Объем HDD
 *   )
 *   [1] => Array (
 *     [id] => 7
 *     [name] => Напряжение питания, Вольт
 *   )
 *   [2] => Array (
 *     [id] => 9
 *     [name] => Цветная или черно-белая
 *   )
 * )
 *
 * $allValues = Array (
 *   [0] => Array (
 *     [id] => 4
 *     [name] => 1 Тбайт
 *   )
 *   [1] => Array (
 *     [id] => 1
 *     [name] => 12 Вольт
 *   )
 *   [2] => Array (
 *     [id] => 5
 *     [name] => 2 Тбайт
 *   )
 *   [3] => Array (
 *     [id] => 3
 *     [name] => 220 Вольт
 *   )
 *   [4] => Array (
 *     [id] => 2
 *     [name] => 24 Вольт
 *   )
 *   [5] => Array (
 *     [id] => 6
 *     [name] => цветная
 *   )
 *   [6] => Array (
 *     [id] => 7
 *     [name] => черно-белая
 *   )
 * )
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
    $params_values = array();

    if (isset($savedFormData)) {
        $name          = htmlspecialchars($savedFormData['name']);
        $params_values = $savedFormData['params_values'];
    }

    /*
     * $params_values = Array (
     *   [3] => Array ( // 5 - уникальный id параметра подбора, например, «Объем HDD»
     *     [0] => 4 // 4 - уникальный id значения параметра подбора, например, «1 Тбайт»
     *     [1] => 5 // 5 - уникальный id значения параметра подбора, например, «2 Тбайт»
     *   )
     *   [7] => Array ( // 7 - уникальный id параметра подбора, например, «Напряжение питания»
     *     [0] => 1 // 1 - уникальный id значения параметра подбора,например,  «12 Вольт»
     *     [1] => 3 // 3 - уникальный id значения параметра подбора, например, «220 Вольт»
     *     [2] => 2 // 2 - уникальный id значения параметра подбора, например, «24 Вольт»
     *   )
     * )
     */
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
            <?php foreach ($allParams as $param): ?>
                <p><?php echo $param['name']; ?></p>
                <?php if (!empty($allValues)): ?>
                    <ul>
                    <?php foreach ($allValues as $value): ?>
                        <li><input type="checkbox" name="params_values[<?php echo $param['id']; ?>][<?php echo $value['id']; ?>]"<?php echo (isset($params_values[$param['id']]) && in_array($value['id'], $params_values[$param['id']])) ? ' checked="checked"' : ''; ?> value="1" /> <?php echo $value['name']; ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Нет значений</p>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Нет параметров</p>
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
