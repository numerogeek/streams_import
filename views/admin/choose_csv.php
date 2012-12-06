<section class="title">
	<h4><?php echo lang('streams_import:title_import_csv'); ?></h4>
</section>

<section class="item">

	<p><?php echo lang('streams_import:misc_example_csv'); ?></p>

	<?php echo form_open(uri_string(), 'class="crud"');?>

	<div class="form_inputs">
		<fieldset>
			<ul>

				<li class="odd">
					<label>
						<?php echo lang('streams_import:misc_csv_file'); ?> <span>*</span>
						<small><?php echo lang('streams_import:misc_instructions_csv_file'); ?></small>
					</label>

					<div class="input dropdown">
						<?php echo form_dropdown('file_id', $files, $this->uri->segment(5)); ?>
					</div>
					<span class="move-handle"></span>
				</li>

			</ul>
		</fieldset>
	</div>

	<div class="buttons">
		<button type="submit" name="btnAction" value="import" class="button red"><?php echo lang('streams_import:button_next'); ?></button>
		<a href="<?php echo site_url('/admin/streams_import/run/' . $profile_id); ?>" class="button"><?php echo lang('streams_import:button:cancel'); ?></a>
	</div>

	<?php echo form_close(); ?>
</section>