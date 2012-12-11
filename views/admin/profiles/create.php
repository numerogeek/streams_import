<section class="title">
	<h4><?php echo lang('streams_import:title:profiles:create') ?></h4>
</section>
<section class="item">
	<?php echo form_open_multipart(uri_string(), 'class="streams_form"');	?>
	<div class="form_inputs">

		<ul>
		<?php foreach ($fields as $field) : ?>
			<li>
					<label for="<?php echo $field->field_slug;?>"> <?php echo $this->fields->translate_label($field->field_name); ?></label>
					<div class="input"><?php echo $this->fields->build_form_input($field); ?></div>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>


	<div class="float-right buttons">
		<button type="submit" name="btnAction" value="save" class="btn blue">
			<span><?php echo lang('buttons.save'); ?></span></button>

	</div>

	<?php echo form_close();?>
</section>