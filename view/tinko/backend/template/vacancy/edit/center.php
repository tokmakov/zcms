<?php
/**
 * Форма для редактирования вакансии,
 * файл view/example/backend/template/vacancy/edit/center.php,
 * административная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $action - содержимое атрибута action тега form
 * $id - уникальный идентификатор вакансии
 * $name - название вакансии
 * $details - подробная информация о вакансии
 * $savedFormData - сохраненные данные формы. Если при заполнении формы были допущены ошибки, мы должны
 * снова предъявить форму, заполненную уже отредактированными данными и вывести сообщение об ошибках.
 * $errorMessage - массив сообщений об ошибках, допущенных при заполнении формы
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/backend/template/vacancy/edit/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Редактирование вакансии</h1>

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
    $name    = htmlspecialchars($name);

    if (isset($savedFormData)) {
        $name    = htmlspecialchars($savedFormData['name']);
        $details = $savedFormData['details'];
        $visible = $savedFormData['visible'];
    }
?>

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="add-edit-vacancy">
    <div>
        <div>Название вакансии</div>
        <div><input type="text" name="name" maxlength="100" value="<?php echo $name; ?>" /></div>
    </div>
    <div>
        <div>Условия и требования</div>
        <div>
            <?php foreach ($details as $key => $value): ?>
                <div>
                    <p>
                        <input type="text" name="names[<?php echo $key; ?>]" maxlength="100" value="<?php echo htmlspecialchars($value['name']); ?>" />
                    </p>
                    <?php if ( ! empty($value['items'])): ?>
                        <?php foreach ($value['items'] as $k => $item): ?>
                            <p>
                                <input type="text" name="items[<?php echo $key; ?>][]" maxlength="100" value="<?php echo htmlspecialchars($item); ?>" />
                                <span>добавить</span> <span>удалить</span>
                            </p>
                        <?php endforeach; ?>
                        <?php while ($k < 2): ?>
                            <?php $k++; ?>
                            <p>
                                <input type="text" name="items[<?php echo $key; ?>][]" maxlength="100" value="" />
                                <span>добавить</span> <span>удалить</span>
                            </p>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <?php for ($i = 0; $i < 3; $i++): ?>
                            <p>
                                <input type="text" name="items[<?php echo $key; ?>][]" maxlength="100" value="" />
                                <span>добавить</span> <span>удалить</span>
                            </p>
                        <?php endfor; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php while ($key < 3): ?>
                <?php $key++; ?>
                <div>
                    <p>
                        <input type="text" name="names[<?php echo $key; ?>]" maxlength="100" value="" />
                    </p>
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <p>
                            <input type="text" name="items[<?php echo $key; ?>][]" maxlength="100" value="" />
                            <span>добавить</span> <span>удалить</span>
                        </p>
                    <?php endfor; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div>
        <div></div>
        <div><input type="checkbox" name="visible" value="1"<?php echo ($visible) ? ' checked="checked"' : ''; ?> /> показывать</div>
    </div>
    <div>
        <div></div>
        <div><input type="submit" name="submit" value="Сохранить" /></div>
    </div>
</form>

<!-- Конец шаблона view/example/backend/template/vacancy/edit/center.php -->
