<?php if (!empty($childs)): ?>
    <ul>
    <?php foreach($childs as $item): ?>
        <li<?php if ($item['count']) echo ' class="parent closed"'; ?>>
            <div>
                <span><span<?php if ($item['count']) echo ' data-id="' . $item['id'] . '"'; ?>></span></span>
                <a href="<?php echo $item['url']; ?>"><span><?php echo $item['name']; ?></span></a>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>