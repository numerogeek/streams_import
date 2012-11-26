<?php echo form_open_multipart(uri_string(), 'class="streams_form"');

var_dump($form); ?>

Ok

<!--

<div class="form_inputs">

	<ul>

	<?php foreach( $fields as $field ) { ?>

		<li>
			<label for="<?php echo $field['input_slug'];?>"><?php echo $this->fields->translate_label($field['input_title']);?> <?php echo $field['required'];?>
			
			<?php if( $field['instructions'] != '' ): ?>
				<br /><small><?php echo $this->fields->translate_label($field['instructions']); ?></small>
			<?php endif; ?>
			</label>
			
			<div class="input"><?php echo $field['input']; ?></div>
		</li>

	<?php } ?>
	
	</ul>	

</div>

	<div class="float-right buttons">
		<button type="submit" name="btnAction" value="save" class="btn blue"><span><?php echo lang('buttons.save'); ?></span></button>	
		
	</div>

<?php echo form_close();?>-->