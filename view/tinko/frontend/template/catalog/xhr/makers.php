<?php if (!empty($result)): ?>
    <span></span>
    <?php foreach($result as $item): ?>
        <div><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></div>
    <?php endforeach; ?>
<?php endif; ?>