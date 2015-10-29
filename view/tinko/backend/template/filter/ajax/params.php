<?php
/**
 * Страница со списком всех параметров для функциональной группы,
 * файл view/example/backend/template/filter/ajax/params.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $allParams - массив параметров, привязанных к группе и массивы привязанных к
 * этим параметрам значений
 * $params - массив параметров, привязанных к товару и массивы привязанных к этим
 * параметрам значений
 *
 * $allParams = Array (
 *    [0] => Array (
 *       [id] => 2
 *       [name] => Напряжение питания
 *       [values] => Array (
 *          [0] => Array (
 *             [id] => 1
 *             [name] => 12 Вольт
 *          )
 *          [1] => Array (
 *             [id] => 3
 *             [name] => 220 Вольт
 *          )
 *          [2] => Array (
 *             [id] => 2
 *             [name] => 24 Вольт
 *          )
 *       )
 *    )
 *    [1] => Array (
 *       [id] => 5
 *       [name] => Цветная или черно-белая
 *       [values] => Array (
 *          [0] => Array (
 *             [id] => 6
 *             [name] => цветная
 *          )
 *          [1] => Array (
 *             [id] => 7
 *             [name] => черно-белая
 *          )
 *       )
 *    )
 * )
 *
 * $params = Array (
 *    [2] => Array ( // 2 - уникальный id параметра, например, «Напряжение питания»
 *       [0] => 1 // 1 - уникальный id значения параметра, например, «12 Вольт»
 *       [1] => 2 // 2 - уникальный id значения параметра, например, «24 Вольт»
 *    )
 *    [5] => Array ( // 5 - уникальный id параметра, например, «Цветная или черно-белая»
 *       [0] => 6 // 6 - уникальный id значения параметра, например, «цветная»
 *    )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<?php foreach ($allParams as $item): ?>
    <div>
        <p><?php echo $item['name']; ?></p>
        <?php if (!empty($item['values'])): ?>
            <ul>
            <?php foreach ($item['values'] as $value): ?>
                <li><input type="checkbox" name="params[<?php echo $item['id']; ?>][<?php echo $value['id']; ?>]"<?php echo (isset($params[$item['id']]) && in_array($value['id'], $params[$item['id']])) ? ' checked="checked"' : '' ?> value="1" /> <?php echo $value['name']; ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
