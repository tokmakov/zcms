<?php
defined('ZCMS') or die('Access denied');
?>

<!-- Начало шаблона view/example/frontend/template/catalog/xhr/center.php -->

<div id="product">

    <div class="product-heading">
        <h1><?php echo $name; ?></h1>
        <?php if (!empty($title)): ?>
            <h2><?php echo $title; ?></h2>
        <?php endif; ?>
    </div>

    <div class="product-main">
        <div class="product-image">
            <?php if ($hit): ?><span class="hit-product">Лидер продаж</span><?php endif; ?>
            <?php if ($new): ?><span class="new-product">Новинка</span><?php endif; ?>
            <img src="<?php echo $image['medium']; ?>" alt="<?php echo htmlspecialchars($name); ?>" />
        </div>
        <div class="product-info">
            <div>
                <span>Цена, <i class="fa fa-rub"></i>/<?php echo $units[$unit]; ?></span>
                <span>
                    <span>
                        <span><?php echo number_format($price, 2, '.', ' '); ?></span> <span>розничная</span>
                    </span>
                    <span>
                        <span><?php echo number_format($price2, 2, '.', ' '); ?></span> <span>мелкий опт</span>
                    </span>
                    <span>
                        <span><?php echo number_format($price3, 2, '.', ' '); ?></span> <span>оптовая</span>
                    </span>
                </span>
            </div>
            <div><span>Код</span> <span><?php echo $code; ?></span></div>
            <div><span>Производитель</span> <span><a href="<?php echo $maker['url']; ?>"><?php echo $maker['name']; ?></a></span></div>
            <div><span>Функционал</span> <span><a href="<?php echo $group['url']; ?>"><?php echo $group['name']; ?></a></span></div>
        </div>
    </div>

    <div class="product-others">

        <?php if (!empty($techdata)): ?>
            <div class="center-block">
                <div><h3>Технические характеристики</h3></div>
                <div class="no-padding">
                    <table>
                    <?php foreach($techdata as $item): ?>
                        <tr>
                            <td><?php echo $item[0]; ?></td>
                            <td><?php echo $item[1]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="product-descr"><?php echo $shortdescr; ?></div>
        
    </div>

</div>

<!-- Конец шаблона view/example/frontend/template/catalog/xhr/center.php -->
