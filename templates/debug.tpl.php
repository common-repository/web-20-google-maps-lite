<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Maps Debug', 'W2GM'); ?>
</h2>

<textarea style="width: 100%; height: 700px">
<?php if (isset($w2gm_instance->submit_page_id)): ?>
$w2gm_instance->submit_page_id = <?php echo $w2gm_instance->submit_page_id; ?>
<?php endif; ?>

<?php if (isset($w2gm_instance->dashboard_page_id)): ?>
$w2gm_instance->dashboard_page_id = <?php echo $w2gm_instance->dashboard_page_id; ?>
<?php endif; ?>


geolocation response = <?php var_dump($geolocation_response); ?>


<?php foreach ($rewrite_rules AS $key=>$rule)
echo $key . '
' . $rule . '

';
?>


<?php foreach ($settings AS $setting)
echo $setting['option_name'] . ' = ' . $setting['option_value'] . '

';
?>


<?php var_dump($levels); ?>


<?php var_dump($content_fields); ?>
</textarea>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>