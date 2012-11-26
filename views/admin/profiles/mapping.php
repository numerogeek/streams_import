<div class="one_full">
	<section class="title">
			<h2>MAPPING</h2>
	</section>
	<section class="item">
		<table>
			<tr><td>SOURCE</td><td>DESTINATION</td></tr>
			<?php
				for ($i=0;$i<$field_count;$i++) {
					echo "<tr><td>".form_dropdown("source[$i]", $field_dropdown)."</td><td>".form_dropdown("destination[$i]", $csv_dropdown)."</td></tr>";
				}
			?>
		</table>
	</section>

</div>
