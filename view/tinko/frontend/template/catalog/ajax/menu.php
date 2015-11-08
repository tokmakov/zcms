<?php if (!empty($childs)): ?>
    <ul>
    <?php foreach($childs as $item): ?>
        <li class="item-empty">
            <div>
                <span><span class="bullet-empty"></span></span>
                <a href="<?php echo $item['url']; ?>"><span><?php echo $item['name']; ?></span></a>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>