<section class="title">
	<h4><?php echo $title; ?></h4>
</section>
<section class="item">

	<div id="filter-stage">
		<table>
			<thead>
				<tr>
					<th><?php echo lang('streams_import:fields:profile_name') ?></th>
					<th><?php echo lang('streams_import:fields:namespace_stream_slug') ?></th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($entries['entries'] as $entry)
			{
				$stream = $this->db->get_where('data_streams', array('id'=> $entry['stream_identifier']))->row();
				echo '<tr><td>' . $entry['profile_name'] . '</td>';
				echo '<td>' . $stream->stream_namespace . '/' . $stream->stream_slug . '</td><td>'; ?>
			<a class='btn green' href='admin/<?php echo $namespace; ?>/<?php echo $section;?>/run/<?php echo $entry['id']; ?>'><?php echo lang('streams_import:button:run') ?></a>
			<a class='btn orange edit' href='admin/<?php echo $namespace; ?>/<?php echo $section;?>/edit/<?php echo $entry['id']; ?>'><?php echo lang('global:edit') ?></a>
			<a class='btn orange edit' href='admin/<?php echo $namespace; ?>/<?php echo $section;?>/mapping/<?php echo $entry['id']; ?>'><?php echo lang('streams_import:button:edit-mapping') ?></a>
			<a class='confirm btn red delete' href='admin/<?php echo $namespace; ?>/<?php echo $section;?>/delete/<?php echo $entry['id']; ?>'><?php echo lang('global:delete') ?></a>
				<?php echo '</td></tr>';
			}?>
			</tbody>
		</table>
	</div>

</section>
