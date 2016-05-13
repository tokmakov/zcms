<?php
/**
 * Форма для редактирования бренда,
 * файл view/example/backend/template/brand/edit/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $id - уникальный идентификатор бренда
 * $name - наименование бренда
 * $letter - первая буква бренда
 * $maker - идентификатор производителя
 * $popular - популярный бренд?
 * $image - URL файла изображения
 * $letters - все буквы, для возможности выбора
 * $makers - все производители, для возможности выбора
 *
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были
 * допущены ошибки, мы должны снова предъявить форму, заполненную уже введенными
 * данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/brand/edit/center.php -->

<?php if (!empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование бренда</h1>

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
    $name = htmlspecialchars($name);

    if (isset($savedFormData)) {
        $name    = htmlspecialchars($savedFormData['name']);
        $letter  = $savedFormData['letter'];
        $maker   = $savedFormData['maker'];
        $popular = $savedFormData['popular'];
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="add-edit-brand">
    <div>
        <div>Наименование</div>
        <div><input type="text" name="name" maxlength="32" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Буква, производитель</div>
        <div>
            <select name="letter">
                <option value="">Выберите</option>
                <optgroup label="Латиница A-Z">
                <?php foreach ($letters['A-Z'] as $item): ?>
                    <option value="<?php echo $item; ?>"<?php echo ($item == $letter) ? ' selected="selected"' : ''; ?>><?php echo $item; ?></option>
                <?php endforeach; ?>
                </optgroup>
                <optgroup label="Кириллица А-Я">
                <?php foreach ($letters['А-Я'] as $item): ?>
                    <option value="<?php echo $item; ?>"<?php echo ($item == $letter) ? ' selected="selected"' : ''; ?>><?php echo $item; ?></option>
                <?php endforeach; ?>
                </optgroup>
            </select>
            
            <select name="maker">
                <option value="0">Выберите</option>
                <?php foreach ($makers as $item): ?>
                    <option value="<?php echo $item['id']; ?>"<?php echo ($item['id'] == $maker) ? ' selected="selected"' : ''; ?>>
                        <?php echo htmlspecialchars($item['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="checkbox" name="popular" value="1"<?php echo $popular ? ' checked="checked"' : ''; ?> /> популярный
        </div>
    </div>
    <div>
        <div>Изображение</div>
        <div>
            <input type="file" name="image" />
            <?php if (!empty($image)): ?>
                <a href="<?php echo $image; ?>" class="zoom">изображение</a>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/backend/template/brand/edit/center.php -->
