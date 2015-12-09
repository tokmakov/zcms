<?php if (!empty($results)): ?>
    <span></span>
    <?php foreach($results as $product): ?>
        <div><a href="<?php echo $product['url']; ?>"><?php echo $product['code']; ?> <strong><?php echo $product['name']; ?></strong> <?php echo $product['title']; ?></a></div>
    <?php endforeach; ?>
<?php endif; ?>