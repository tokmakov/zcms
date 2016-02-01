<?php
/**
 * Форма для добавления товара,
 * файл view/example/backend/template/catalog/addprd/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - атрибут action тега form
 * $category - родительская категория товара по умолчанию
 * $categories - массив всех категорий, для возможности выбора родителя
 * $group - функциональная группа по умолчанию
 * $groups - массив всех функциональных групп, для возможности выбора
 * $maker - производитель по умолчанию
 * $makers - массив всех производителей, для возможности выбора
 * $units - все единицы измерения для возможности выбора
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/catalog/addprd/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Добавить товар</h1>

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
    // массив параметров, привязанных к группе и массивы привязанных к этим параметрам значений
    $allParams   = array();
    // массив параметров, привязанных к товару и массивы привязанных к этим параметрам значений
    $params      = array();
    // дополнительная категория по умолчанию
    $category2   = 0;
    // функциональная группа по умолчанию
    $group = 0;
    // производитель по умолчанию
    $maker = 0;
    $name        = '';
    $title       = '';
    $keywords    = '';
    $description = '';
    $code        = '';
    $price       = '';
    $price2      = '';
    $price3      = '';
    $price4      = '';
    $price5      = '';
    $price6      = '';
    $price7      = '';
    // единица измерения по умолчанию
    $unit = 0;
    $shortdescr  = '';
    $purpose     = '';
    $features    = '';
    $complect    = '';
    $equipment   = '';
    $padding     = '';
    $techdata    = array();

    if (isset($savedFormData)) {
        $allParams   = $savedFormData['allParams'];
        $params      = $savedFormData['params'];
        $category    = $savedFormData['category'];
        $category2   = $savedFormData['category2'];
        $group       = $savedFormData['group'];
        $maker       = $savedFormData['maker'];
        $name        = htmlspecialchars($savedFormData['name']);
        $title       = htmlspecialchars($savedFormData['title']);
        $keywords    = htmlspecialchars($savedFormData['keywords']);
        $description = htmlspecialchars($savedFormData['description']);
        $code        = htmlspecialchars($savedFormData['code']);
        $price       = $savedFormData['price'];
        if (empty($price)) {
            $price   = '';
        }
        $price2      = $savedFormData['price2'];
        if (empty($price2)) {
            $price2  = '';
        }
        $price3      = $savedFormData['price3'];
        if (empty($price3)) {
            $price3  = '';
        }
        $price4      = $savedFormData['price4'];
        if (empty($price4)) {
            $price4  = '';
        }
        $price5      = $savedFormData['price5'];
        if (empty($price5)) {
            $price5  = '';
        }
        $price6      = $savedFormData['price6'];
        if (empty($price6)) {
            $price6  = '';
        }
        $price7      = $savedFormData['price7'];
        if (empty($price7)) {
            $price7  = '';
        }
        $unit        = $savedFormData['unit'];
        $shortdescr  = htmlspecialchars($savedFormData['shortdescr']);
        $purpose     = htmlspecialchars($savedFormData['purpose']);
        $features    = htmlspecialchars($savedFormData['features']);
        $complect    = htmlspecialchars($savedFormData['complect']);
        $equipment   = htmlspecialchars($savedFormData['equipment']);
        $padding     = htmlspecialchars($savedFormData['padding']);
        if (count($savedFormData['techdata']) > 0) {
            foreach ($savedFormData['techdata'] as $value) {
                $techdata[] = array(htmlspecialchars($value[0]), htmlspecialchars($value[1]));
            }
        }
    }

    /*
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
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
<div id="add-edit-product">
    <div>
        <div>Наименование товара</div>
        <div><input type="text" name="name" maxlength="250" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Функц. наименование</div>
        <div><input type="text" name="title" maxlength="250" value="<?php echo $title; ?>" /></div>
    </div>
    <div>
        <div>Категория</div>
        <div>
            <select name="category">
                <option value="0">Выберите</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach($categories as $value1): ?>
                        <option value="<?php echo $value1['id']; ?>"<?php if ($value1['id'] == $category) echo ' selected="selected"'; ?>><?php echo $value1['name']; ?></option>
                        <?php if (isset($value1['childs'])): ?>
                            <?php foreach($value1['childs'] as $value2): ?>
                                <option value="<?php echo $value2['id']; ?>"<?php if ($value2['id'] == $category) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value2['name']; ?></option>
                                <?php if (isset($value2['childs'])): ?>
                                    <?php foreach($value2['childs'] as $value3): ?>
                                        <option value="<?php echo $value3['id']; ?>"<?php if ($value3['id'] == $category) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value3['name']; ?></option>
                                        <?php if (isset($value3['childs'])): ?>
                                            <?php foreach($value3['childs'] as $value4): ?>
                                                <option value="<?php echo $value4['id']; ?>"<?php if ($value4['id'] == $category) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value4['name']; ?></option>
                                                <?php if (isset($value4['childs'])): ?>
                                                    <?php foreach($value4['childs'] as $value5): ?>
                                                        <option value="<?php echo $value5['id']; ?>"<?php if ($value5['id'] == $category) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value5['name']; ?></option>
                                                        <?php if (isset($value5['childs'])): ?>
                                                            <?php foreach($value5['childs'] as $value6): ?>
                                                                <option value="<?php echo $value6['id']; ?>"<?php if ($value6['id'] == $category) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value6['name']; ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div>
        <div>Доп.категория</div>
        <div>
            <select name="category2">
                <option value="0">Выберите</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach($categories as $value1): ?>
                        <option value="<?php echo $value1['id']; ?>"<?php if ($value1['id'] == $category2) echo ' selected="selected"'; ?>><?php echo $value1['name']; ?></option>
                        <?php if (isset($value1['childs'])): ?>
                            <?php foreach($value1['childs'] as $value2): ?>
                                <option value="<?php echo $value2['id']; ?>"<?php if ($value2['id'] == $category2) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value2['name']; ?></option>
                                <?php if (isset($value2['childs'])): ?>
                                    <?php foreach($value2['childs'] as $value3): ?>
                                        <option value="<?php echo $value3['id']; ?>"<?php if ($value3['id'] == $category2) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value3['name']; ?></option>
                                        <?php if (isset($value3['childs'])): ?>
                                            <?php foreach($value3['childs'] as $value4): ?>
                                                <option value="<?php echo $value4['id']; ?>"<?php if ($value4['id'] == $category2) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value4['name']; ?></option>
                                                <?php if (isset($value4['childs'])): ?>
                                                    <?php foreach($value4['childs'] as $value5): ?>
                                                        <option value="<?php echo $value5['id']; ?>"<?php if ($value5['id'] == $category2) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value5['name']; ?></option>
                                                        <?php if (isset($value5['childs'])): ?>
                                                            <?php foreach($value5['childs'] as $value6): ?>
                                                                <option value="<?php echo $value6['id']; ?>"<?php if ($value6['id'] == $category2) echo ' selected="selected"'; ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $value6['name']; ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div>
        <div>Функц. группа</div>
        <div>
            <select name="group">
                <option value="0">Выберите</option>
                <?php if (!empty($groups)): ?>
                    <?php foreach ($groups as $item): ?>
                        <option value="<?php echo $item['id']; ?>"<?php if ($item['id'] == $group) echo ' selected="selected"'; ?>><?php echo $item['name']; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div id="params">
        <div><span>Параметры и значения</span></div>
        <div>
            <?php if (!empty($allParams)): ?>
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
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div>Производитель</div>
        <div>
            <select name="maker">
                <option value="0">Выберите</option>
                <?php if (!empty($makers)): ?>
                    <?php foreach ($makers as $item): ?>
                        <option value="<?php echo $item['id']; ?>"<?php if ($item['id'] == $maker) echo ' selected="selected"'; ?>><?php echo $item['name']; ?></option>
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
        <div>Код (артикул)</div>
        <div><input type="text" name="code" value="<?php echo $code; ?>" /></div>
    </div>
    <div id="price">
        <div>Цена</div>
        <div>
            <input type="text" name="price" value="<?php echo $price; ?>" />
            <input type="text" name="price2" value="<?php echo $price2; ?>" />
            <input type="text" name="price3" value="<?php echo $price3; ?>" />
            <input type="text" name="price4" value="<?php echo $price4; ?>" />
            <input type="text" name="price5" value="<?php echo $price5; ?>" />
            <input type="text" name="price6" value="<?php echo $price6; ?>" />
            <input type="text" name="price7" value="<?php echo $price7; ?>" />
        </div>
    </div>
    <div>
        <div>Единица измерения</div>
        <div>
        <?php if (!empty($units)): ?>
            <select name="unit">
            <?php foreach ($units as $key => $value): ?>
                <option value="<?php echo $key; ?>"<?php if ($key == $unit) echo ' selected="selected"'; ?>><?php echo $value; ?></option>
            <?php endforeach; ?>
            </select>
        <?php endif; ?>
        </div>
    </div>
    <div>
        <div>Фото</div>
        <div><input type="file" name="image" /></div>
    </div>
    <div>
        <div>Краткое описание</div>
        <div><textarea name="shortdescr" maxlength="2000"><?php echo $shortdescr; ?></textarea></div>
    </div>
    <div>
        <div>Назначение изделия</div>
        <div><textarea name="purpose"><?php echo $purpose; ?></textarea></div>
    </div>
    <div id="techdata">
        <div>Технические характеристики</div>
        <div>
            <?php if (count($techdata) > 0): ?>
                <?php foreach($techdata as $value): ?>
                    <div>
                        <input type="text" name="techdata_name[]"  maxlength="250" value="<?php echo $value[0]; ?>" />
                        <input type="text" name="techdata_value[]"  maxlength="250" value="<?php echo $value[1]; ?>" />
                        <span>Добавить</span>
                        <span>Удалить</span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div>
                <input type="text" name="techdata_name[]"  maxlength="250" value="" />
                <input type="text" name="techdata_value[]"  maxlength="250" value="" />
                <span>Добавить</span>
                <span>Удалить</span>
            </div>
        </div>
    </div>
    <div>
        <div>Особенности</div>
        <div><textarea name="features"><?php echo $features; ?></textarea></div>
    </div>
    <div>
        <div>Комплектация</div>
        <div><textarea name="complect"><?php echo $complect; ?></textarea></div>
    </div>
    <div>
        <div>Доп. оборудование</div>
        <div><textarea name="equipment"><?php echo $equipment; ?></textarea></div>
    </div>
    <div>
        <div>Дополнительно</div>
        <div><textarea name="padding"><?php echo $padding; ?></textarea></div>
    </div>
    <div id="docs">
        <div>Документация</div>
        <div>
            <div></div>
            <div>
                <div>
                    <input type="text" name="add_doc_titles[]" value="" />
                    <input type="file" name="add_doc_files[]" />
                    <span>Добавить</span>
                    <span>Удалить</span>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div><input type="hidden" name="id" value="0" /></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</div>
</form>

<!-- Конец шаблона view/example/backend/template/catalog/addprd/center.php -->
