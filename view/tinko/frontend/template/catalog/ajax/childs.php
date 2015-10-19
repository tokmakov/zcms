<?php
defined('ZCMS') or die('Access denied');
?>
<ul>
	<?php
		$divide = 0;
		$count = count($childs);
		if ($count > 5) {
			$divide = ceil($count/2);
		}
	?>
	<?php foreach($childs as $key => $item): ?>
		<li>
			<?php if ($item['count']): // есть товары в категории? ?>
				<span><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a> <span><?php echo $item['count']; ?></span></span>
			<?php else: ?>
				<span><span><?php echo $item['name']; ?></span> <span>0</span></span>
			<?php endif; ?>
		</li>
		<?php if ($divide && $divide == ($key+1)): ?>
			</ul><ul>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>