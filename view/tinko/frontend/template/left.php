<?php
/**
 * Левая колонка, файл view/example/frontend/template/left.php,
 * общедоступная часть сайта
 *
 * Переменные, доступные в шаблоне:
 * $catalogMenu - меню каталога (дерево каталога с одной раскрытой веткой)
 * $makers = массив 10 производителей
 * $allMakersUrl - URL ссылки на страницу со списком всех производителей
 *
 * $catalogMenu = Array (
 *   [0] => Array (
 *     [id] => 3
 *     [name] => Средства и системы охранно-пожарной сигнализации
 *     [level] => 0
 *     [url] => /catalog/category/3
 *   )
 *   [1] => Array (
 *     [id] => 185
 *     [name] => Средства и системы охранного телевидения
 *     [level] => 0
 *     [url] => /catalog/category/185
 *     [opened] => true
 *   )
 *   [2] => Array (
 *     [id] => 186
 *     [name] => Телекамеры
 *     [level] => 1
 *     [url] => /catalog/category/186
 *     [current] = true
 *   )
 *   [3] => Array (
 *     [id] => 394
 *     [name] => Объективы
 *     [level] => 1
 *     [url] => /catalog/category/394
 *   )
 *   [4] => Array (
 *     [id] => 437
 *     [name] => Видеорегистраторы
 *     [level] => 1
 *     [url] => /catalog/category/437
 *   )
 *   [5] => Array (
 *     [id] => 651
 *     [name] => Средства и системы контроля и управления доступом
 *     [level] => 0
 *     [url] => /catalog/category/651
 *   )
 * )
 *
 * $makers = Array (
 *   [0] => Array (
 *     [id] => 2252
 *     [name] => ABB
 *     [count] => 13
 *     [url] => /catalog/maker/2252
 *   )
 *   [1] => Array (
 *     [id] => 2249
 *     [name] => ABRON
 *     [count] => 12
 *     [url] => /catalog/maker/2249
 *   )
 *   [2] => Array (
 *     [id] => 318
 *     [name] => AccordTec
 *     [count] => 48
 *     [url] => /catalog/maker/318
 *   )
 * )
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/left.php -->

<div class="side-block">
    <div>Каталог оборудования</div>
    <div class="no-padding">
        <?php if (!empty($catalogMenu)): ?>
            <div id="catalog-menu">
            <?php $selected = false; /* выделяем текущую категорию */ ?>
            <?php foreach ($catalogMenu as $item): ?>
                <div class="item-level-<?php echo $item['level']; ?>">
                    <?php if (isset($item['current']) && $item['level']) $selected = true; /* выделяем текущую категорию */ ?>
                    <a href="<?php echo $item['url']; ?>"<?php if($selected) echo ' class="selected"'; ?>>
                    <span><?php echo $item['name']; ?></span>
                    </a>
                    <?php $selected = false; /* выделяем текущую категорию */ ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="side-block">
    <div>Каталог оборудования</div>
    <div class="no-padding">
        <?php if (!empty($catalogMenu2)): ?>
            <div id="catalog-menu-2">
                <ul>
                <?php foreach ($catalogMenu2 as $item1): ?>
                    <?php
                        $class = 'empty';
                        if ($item1['count']) {
                            if (isset($item1['opened'])) {
                                $class = 'opened';
                            } else {
                                $class = 'closed';
                            }
                        }
                    ?>
                    <li class="item-<?php echo $class; ?>">
                        <div>
                            <span><span class="bullet-<?php echo $class; ?>"></span></span>
                            <a href="<?php echo $item1['url']; ?>"><span><?php echo $item1['name']; ?></span></a>
                        </div>
                        <?php if (isset($item1['childs'])): ?>
                            <ul>
                            <?php foreach ($item1['childs'] as $item2): ?>
                                <?php
                                    $class = 'empty';
                                    if ($item2['count']) {
                                        if (isset($item2['opened'])) {
                                            $class = 'opened';
                                        } else {
                                            $class = 'closed';
                                        }
                                    }
                                ?>
                                <li class="item-<?php echo $class; ?>">
                                    <div>
                                        <span><span class="bullet-<?php echo $class; ?>"></span></span>
                                        <a href="<?php echo $item2['url']; ?>"><span><?php echo $item2['name']; ?></span></a>
                                    </div>
                                    <?php if (isset($item2['childs'])): ?>
                                        <ul>
                                        <?php foreach ($item2['childs'] as $item3): ?>
                                            <?php
                                                $class = 'empty';
                                                if ($item3['count']) {
                                                    if (isset($item3['opened'])) {
                                                        $class = 'opened';
                                                    } else {
                                                        $class = 'closed';
                                                    }
                                                }
                                            ?>
                                            <li class="item-<?php echo $class; ?>">
                                                <div>
                                                    <span><span class="bullet-<?php echo $class; ?>"></span></span>
                                                    <a href="<?php echo $item3['url']; ?>"><span><?php echo $item3['name']; ?></span></a>
                                                </div>
                                                <?php if (isset($item3['childs'])): ?>
                                                    <ul>
                                                    <?php foreach ($item3['childs'] as $item4): ?>
                                                        <?php
                                                            $class = 'empty';
                                                            if ($item4['count']) {
                                                                if (isset($item4['opened'])) {
                                                                    $class = 'opened';
                                                                } else {
                                                                    $class = 'closed';
                                                                }
                                                            }
                                                        ?>
                                                        <li class="item-<?php echo $class; ?>">
                                                            <div>
                                                                <span><span class="bullet-<?php echo $class; ?>"></span></span>
                                                                <a href="<?php echo $item4['url']; ?>"><span><?php echo $item4['name']; ?></span></a>
                                                            </div>
                                                            <?php if (isset($item4['childs'])): ?>
                                                                <ul>
                                                                <?php foreach ($item4['childs'] as $item5): ?>
                                                                    <?php
                                                                        $class = 'empty';
                                                                        if ($item5['count']) {
                                                                            if (isset($item5['opened'])) {
                                                                                $class = 'opened';
                                                                            } else {
                                                                                $class = 'closed';
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <li class="item-<?php echo $class; ?>">
                                                                        <div>
                                                                            <span><span class="bullet-<?php echo $class; ?>"></span></span>
                                                                            <a href="<?php echo $item5['url']; ?>"><span><?php echo $item5['name']; ?></span></a>
                                                                        </div>
                                                                    </li>
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
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="side-block">
    <div>Производители</div>
    <div>
        <div id="makers-list-right">
            <ul>
            <?php foreach ($makers as $item): ?>
                <li>
                    <span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
                </li>
            <?php endforeach; ?>
            </ul>
            <p><a href="<?php echo $allMakersUrl; ?>">Все производители</a></p>
        </div>
        </div>
</div>

<!-- Конец шаблона view/example/frontend/template/left.php -->
