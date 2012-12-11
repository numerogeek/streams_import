<?php Asset::css('module::streams_import_admin.css', 'late_header'); ?>
<?php Asset::js('module::streams_import.js', null, 'late_header'); ?>
<div id="sim_mapping">
<section class="title">
	<h4><?php echo $page_title; ?></h4>
</section>
<section class="item">
	<?php echo form_open_multipart('admin/streams_import/quick_import/import', 'id="mapping_form" class="streams_form"');	?>
	<table>
		<tr>
			<th><?php echo form_checkbox('include_all', true, true, 'id="mapping_all_checkbox" title="'.lang('streams_import:fields:include_all').'"') ?></th>
			<th>Source</th>
			<th>Destination</th>
		</tr>
	<?php foreach ($fields as $field) : ?>
		<tr>
			<td><?php echo $field['include'] ?></td>
			<td><?php echo $field['source'] ?></td>
			<td><?php echo $field['destination'] ?><span class="row_screen"><span class="shade"></span></span></td>
		</tr>
	<?php endforeach; ?>
	</table>
	
	<div class="float-right buttons">
		<button type="submit" name="btnAction" value="save" class="btn blue">
			<span><?php echo lang('streams_import:button:run') ?></span></button>

	</div>

	<?php echo form_close();?>
	
</section>
</div>