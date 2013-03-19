 <div id="filter-stage">
    <table class="table-list">
        <thead>
            <tr>
                                                <th>Créé le</th>
                                <th>Mis à jour le</th>
                                <th>Profil</th>
                                <th>ID / Nom de fichier </th>
                                <th></th>
            </tr>
        </thead>
        <tbody>
<?php

foreach($entries as $entry)
{

?>
    <tr><td><?= date('M j Y g:i a', $entry['created'])?></td>
        <td><?= date('M j Y g:i a', $entry['updated'])?></td>
        <td><?= $entry['profile_rel_logs']['profile_name']?></a></td>
        <td><?= $entry['filename']; ?></td>
        <td class="actions">
            <a href="admin/streams_import/logs/delete/<?=$entry['id'];?>" class="button confirm"><?= lang('global:delete') ?></a>&nbsp;
            <a href="admin/streams_import/logs/view/<?=$entry['id'];?>" class="button"><?= lang('global:view') ?></a>         
        </td>
    </tr>
        
    <?php

    }

    ?>
</tbody>
</table>
</div>
