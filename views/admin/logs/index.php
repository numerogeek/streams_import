<!--
<?php 
$date = date("d-m-Y");
$heure = date("H:i");
Print("Nous sommes le $date et il est $heure");
 ?>
--><section class="title">
    <h4><?php echo $title; ?></h4>
</section>
<section class="item">
    <fieldset id="filters">

    <legend><?php echo lang('global:filters'); ?></legend>

    <?php echo form_open(); ?>

    <?php echo form_hidden('f_module', $namespace); ?>
        <ul>  
            <li>Recherche rapide : <?php echo form_input('keyword'); ?></li><br />
            <li>Start date : <?php echo form_input('start_date',date("Y-m-d")); ?></li>
            <li>End date : <?php echo form_input('end_date'); ?></li><br />
            <li>Profile : <?php echo form_dropdown('profile',$profiles,'0', '  class="skip" '); ?></li><br />

            <li><?php echo anchor(current_url() . '#', lang('buttons.cancel'), 'class="cancel"'); ?></li>
        </ul>
    <?php echo form_close(); ?>

    </fieldset>

    <?php echo $this->load->view('admin/'.$section.'/table', array('entries' => $entries)); ?>
</section>
