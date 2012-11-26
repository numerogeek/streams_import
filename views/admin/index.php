<section class="title">
    <h4><?php echo $title; ?></h4>
</section>
<section class="item">

    <div id="filter-stage">
        <table>
        <thead>
            <tr>
                <th><?php echo lang('streams_import:fields:profile_name') ?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries['entries'] as $entry) {
                //var_dump($entry);
                echo '<tr><td>'.$entry['profile_name'].'</td><td>'; ?>    
                <a class='btn orange edit' href='admin/<?php echo $namespace; ?>/<?php echo $section;?>/edit/<?php echo $entry['id']; ?>'><?php echo lang('global:edit') ?></a>
                <a class='confirm btn red delete' href='admin/<?php echo $namespace; ?>/<?php echo $section;?>/delete/<?php echo $entry['id']; ?>'><?php echo lang('global:delete') ?></a>
            <?php echo '</td></tr>';
            }?>
        </tbody>
    </table>    
    </div>

</section>
