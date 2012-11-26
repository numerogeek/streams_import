
<section class="item">
<?php echo form_open_multipart(uri_string(), 'class="streams_form"'); 
?>
<div class="form_inputs">

	<ul>
		<li>
			<label for="<?php echo $fields->profile_name->field_slug;?>"> <?php echo $this->fields->translate_label( $fields->profile_name->field_name); ?></label>
			<div class="input"><?php echo $this->fields->build_form_input($fields->profile_name); ?></div>
		</li>
		<li>
			<label for="<?php echo $fields->example_file->field_slug;?>"> <?php echo $this->fields->translate_label( $fields->example_file->field_name); ?></label>
			<div class="input"><?php echo $this->fields->build_form_input($fields->example_file); ?></div>
		</li>
		<li>
			<label for="<?php echo $fields->delimiter->field_slug;?>"> <?php echo $this->fields->translate_label( $fields->delimiter->field_name); ?></label>
			<div class="input"><?php echo $this->fields->build_form_input($fields->delimiter); ?></div>
		</li>
		<li>
			<label for="<?php echo $fields->eol->field_slug;?>"> <?php echo $this->fields->translate_label( $fields->eol->field_name); ?></label>
			<div class="input"><?php echo $this->fields->build_form_input($fields->eol); ?></div>
		</li>
		<li>
			<label for="<?php echo $fields->stream_identifier->field_slug;?>"> <?php echo $this->fields->translate_label( $fields->stream_identifier->field_name); ?></label>
			<div class="input"><?php echo form_dropdown('$fields->stream_identifier->field_slug', $stream_dropdown); ?></div>
		</li>
	</ul>
</div>

		


	<div class="float-right buttons">
		<button type="submit" name="btnAction" value="save" class="btn blue"><span><?php echo lang('buttons.save'); ?></span></button>	
		
	</div>

<?php echo form_close();?>
</section>