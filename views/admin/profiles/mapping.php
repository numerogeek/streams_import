<?php
/*echo "<pre>";
print_r($array);
    echo "</pre>";*/
//    var_dump($csv_dropdown);
//    die();
?>

<div class="one_full">
	<section class="title">
		<h2>Mapping</h2>
	</section>
	<section class="item">
		<?php echo form_open_multipart(uri_string());

		?>
		<table>
			<tr>
				<td>Source</td>
				<td>Destination</td>
			</tr>
			<?php
			for ($i = 0; $i < $field_count; $i++)
			{
				echo "<tr><td>" . form_dropdown("source[$i]", $csv_dropdown, get_current_value($this->uri->segment(5), $i, $mode = 2)) . "</td><td>" . form_dropdown("destination[$i]", $fields, get_current_value($this->uri->segment(5), $i, $mode = 1)) . "</td></tr>";
			}
			?>
		</table>

		<?php
		echo form_hidden('profileID', $this->uri->segment(5));
		echo form_hidden('counter', $field_count); ?>
		<div class="float-right buttons">

			<button type="submit" name="btnAction" value="save" class="btn blue">
				<span><?php echo lang('buttons.save'); ?></span></button>

		</div>
		<?php  echo form_close();?>


	</section>

</div>
