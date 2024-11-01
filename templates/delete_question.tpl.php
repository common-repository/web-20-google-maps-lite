<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php echo $heading; ?>
</h2>

<form action="" method="POST">
	<p>
		<?php echo $question; ?>
	</p>

	<?php submit_button(__('Delete', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>