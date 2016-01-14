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
 *   ..........
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
 *   ..........
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

<?php if ( ! empty($errorMessage)): ?>
    <div class="error-message">
        <ul>
        <?php foreach($errorMessage as $message): ?>
            <li><?php echo $message; ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php
    $name          = '';
    $linked_params = array();

    if (isset($savedFormData)) {
        $name = htmlspecialchars($savedFormData['name']);
        $linked_params = $savedFormData['linked_params'];
    }

    /*
     * $linked_params = Array (
     *   [0] => Array (
     *     [id] => 7
     *     [name] => Напряжение питания
     *     [ids] => Array (
     *       [0] => 1 // идентификатор значения параметра «12 Вольт»
     *       [1] => 3 // идентификатор значения параметра «220 Вольт»
     *       [2] => 2 // идентификатор значения параметра «24 Вольт»
     *     )
     *   )
     *   [1] => Array (
     *     [id] => 9
     *     [name] => Цветная или черно-белая
     *     [ids] => Array (
     *       [0] => 6 // идентификатор значения параметра «цветная»
     *       [1] => 7 // идентификатор значения параметра «черно-белая»
     *     )
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
        <?php if ( ! empty($allParams)): ?>
            <select name="new_params[]" class="items-multi-select" multiple="multiple">
            <?php foreach ($allParams as $param): ?>
                <option value="<?php echo $param['id']; ?>"><?php echo $param['name']; ?></option>
            <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p>Нет параметров</p>
        <?php endif; ?>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="params" value="Добавить" /></div>
    </div>
    <?php if ( ! empty($linked_params)): ?>
    <div>
        <div>Значения параметров</div>
        <div>
        <?php foreach ($linked_params as $param): ?>
            <p><?php echo $param['name']; ?></p>
            <?php if ( ! empty($allValues)): ?>
                <select name="params_values[<?php echo $param['id']; ?>][]" class="items-multi-select" multiple="multiple">
                <?php foreach ($allValues as $value): ?>
                    <option value="<?php echo $value['id']; ?>"<?php echo in_array($value['id'], $param['ids']) ? ' selected="selected"' : ''; ?>><?php echo $value['name']; ?></option>
                <?php endforeach; ?>
                </select>
            <?php else: ?>
                <p>Нет значений</p>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить"<?php echo empty($linked_params) ? ' disabled="disabled"' : ''; ?> /></div>
    </div>
</div>
</form>

<!-- Конец шаблона шаблона view/example/backend/template/filter/addgroup/center.php -->
