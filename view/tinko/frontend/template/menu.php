<?php
/**
 * Главное меню сайта, файл view/example/frontend/template/menu.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $menu - массив элементов меню
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/menu.php -->

<div id="menu">
    <ul>
    <?php foreach ($menu as $item): ?>
        <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>
            <?php if (isset($item['childs'])): ?>
                <ul>
                <?php foreach ($item['childs'] as $child): ?>
                    <li><a href="<?php echo $child['url']; ?>"><?php echo $child['name']; ?></a>
                        <?php if (isset($child['childs'])): ?>
                            <ul>
                            <?php foreach ($child['childs'] as $value): ?>
                                <li><a href="<?php echo $value['url']; ?>"><?php echo $value['name']; ?></a></li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
</div>

<!-- Конец шаблона view/example/frontend/template/menu.php -->

