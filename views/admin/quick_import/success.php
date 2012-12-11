<?php Asset::css('module::streams_import_admin.css', 'late_header'); ?>
<div id="sim_success">
<section class="title">
	<h4><?php echo $page_title; ?></h4>
</section>
<section class="item">

	<h3>Successfully imported <big><?php echo $total_inserted ?></big> rows of data!</h3>

	<h4>Here is some sample data:</h4>
	
	<table>
		<tr>
			<th>Column</th>
			<th>Data</th>
		</tr>
		<?php foreach (array_slice($inserted_rows, 0, 5) as $sample) : ?>
		<tr>
			<th colspan="2" class="row_sep">Row Data</th>
		</tr>
		<?php foreach ($sample as $key => $value) : ?>
		<tr>
			<td class="key"><strong><?php echo $key ?></strong></td>
			<td class="preformatted"><?php echo $value; ?></td>
		</tr>
		<?php endforeach; // each row column ?>
	<?php endforeach; // each row ?>
	</table>
	
</section>
</div>