<?php
/**
 * Список брендов по алфавиту,
 * файл view/example/frontend/template/brand/center.php,
 * общедоступная часть сайта
 *
 * Переменные, которые приходят в шаблон:
 * $breadcrumbs - хлебные крошки
 * $latin - английский алфавит
 * $cyrillic - русский алфавит
 * $popular - массив популярных брендов
 * $brands - массив всех брендов
 */

defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/brand/center.php -->

<?php if ( ! empty($breadcrumbs)): // хлебные крошки ?>
    <div id="breadcrumbs">
        <?php foreach ($breadcrumbs as $item): ?>
            <a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a>&nbsp;&gt;
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h1>Бренды</h1>

<div id="all-brands">
    <div>
        <div>
        <?php foreach ($latin as $char): /* ангийский алфавит */ ?>
            <?php if (isset($brands['A-Z'][$char])): ?>
                <a href="#char-<?php echo $char; ?>"><?php echo $char; ?></a>
            <?php else: ?>
                <span><?php echo $char; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
        <div>
        <?php foreach ($cyrillic as $char): /* русский алфавит */ ?>
            <?php if (isset($brands['А-Я'][$char])): ?>
                <a href="#char-<?php echo $char; ?>"><?php echo $char; ?></a>
            <?php else: ?>
                <span><?php echo $char; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </div>
    <?php if ( ! empty($popular)): /* популярные бренды */ ?>
        <section>
            <div>
                <span><i class="fa fa-star" title="Популярные"></i></span>
                <ul>
                <?php foreach($popular as $item): ?>
                    <li>
                        <a href="<?php echo $item['maker']; ?>">
                            <img src="<?php echo $item['image']; ?>" alt="" />
                            <span><?php echo $item['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( ! empty($brands['A-Z'])): /* бренды A-Z */ ?>
        <section>
        <?php foreach ($brands['A-Z'] as $char => $items): ?>
            <div>
                <span id="char-<?php echo $char; ?>"><?php echo $char; ?></span>
                <ul>
                <?php foreach($items as $item): ?>
                    <li>
                        <a href="<?php echo $item['maker']; ?>">
                            <img src="<?php echo $item['image']; ?>" alt="" />
                            <span><?php echo $item['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
        </section>
    <?php endif; ?>
    
    <?php if ( ! empty($brands['А-Я'])): /* бренды А-Я */ ?>
        <section>
        <?php foreach ($brands['А-Я'] as $char => $items): ?>
            <div>
                <span id="char-<?php echo $char; ?>"><?php echo $char; ?></span>
                <ul>
                <?php foreach($items as $item): ?>
                    <li>
                        <a href="<?php echo $item['maker']; ?>">
                            <img src="<?php echo $item['image']; ?>" alt="" />
                            <span><?php echo $item['name']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
        </section>
    <?php endif; ?>
</div>

<!-- Конец шаблона view/example/frontend/template/brand/center.php -->
