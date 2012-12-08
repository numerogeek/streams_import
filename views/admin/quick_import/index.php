<section class="title">
	<h4><?php echo $page_title; ?></h4>
</section>
<section class="item">
	<?php echo form_open_multipart('admin/streams_import/quick_import/mapping', 'class="streams_form"');	?>
	<ul>
	<?php foreach ($fields as $field) : ?>
		<li>
				<label for="<?php echo $field->field_slug;?>"> <?php echo $this->fields->translate_label($field->field_name); ?></label>
				<div class="input"><?php echo $this->fields->build_form_input($field); ?></div>
		</li>
	<?php endforeach; ?>
	</ul>
	
	<div class="buttons">
		<button type="submit" name="btnAction" value="import" class="button red"><?php echo lang('streams_import:button_next'); ?></button>
	</div>

	<?php echo form_close();?>
	
</section>