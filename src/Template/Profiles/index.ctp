<?= $this->Flash->render('reload_profile') ?>
<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Profiles')?></h3>
	<hr>
    <div class="row bottom-buffer">
      <div class="col-sm-2 col-xs-12"><!-- actions -->
			<?= $this->Html->link(
					$this->Html->tag('span', "", [
						'class' => "glyphicon glyphicon-plus",
						'aria-hidden' => "true",
						'title' => __("Add a profile"),
					]),
					['controller' => 'profiles', 'action' => 'add'],
					['escape' => false]
				)
			?>
			<?= $this->Html->link(
					$this->Html->tag('span', "", [
						'class' => "glyphicon glyphicon-import",
						'aria-hidden' => "true",
						'title' => __("Import a profile"),
					]),
					['controller' => 'profiles', 'action' => 'upload_import', '?' => [
						'import_profile' => true, 
						'import_times' => true, 
						'import_websites' => true, 
						'import_firewall' => true]
					],
					['escape' => false]
				)
			?>
      </div><!-- actions -->
      <!-- search -->
      <?= $this->element('search_general_form') ?>
    </div><!-- /.row -->
  </div>
  <!-- /.box-header -->

  <div class="box-body">
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <tr>
            <!--<th><input type="checkbox" id="select_all"/></th>-->
            <th><?= $this->Paginator->sort('profilename', __('Name')) ?></th>
            <th><?= $this->Paginator->sort('default_routing', __('Default connection type')) ?></th>
            <th><?= $this->Paginator->sort('default_ipfilter', __('Default firewall rule')) ?></th>
            <th><?= $this->Paginator->sort('log_enabled', __('Statistics')) ?></th>
            <th><?= $this->Paginator->sort('use_ttdnsd', __('DNS to TOR')) ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
	  <tbody>
        <?php foreach ($profiles as $profile): ?>
        <tr>
            <!--
            <td>
                <input class="checkbox" type="checkbox" name="check[]" value="<?= $profile->id ?>">
            </td> -->
            <td><?= $this->Html->link($profile->profilename, ['action' => 'edit', $profile->id]) ?></td>
            <td>
            <?php 
                if($profile->default_routing == 'direct') { $routing = __('Direct'); }
                elseif($profile->default_routing == 'tor') { $routing = __('Tor'); }
                else {$routing = $profile->default_routing;}
                echo $routing;
            ?>
            </td>
            <td>
                <?= $profile->default_ipfilter ?>
            </td>
            <td>
                <?= h($profile->log_enabled) ? 
                    $this->Html->tag('span', "", [
                            'class' => "glyphicon glyphicon-ok",
                            'aria-hidden' => "true",
                            'title' => __("Yes"),
                        ]) : 
                        $this->Html->tag('span', "", [
                            'class' => "none",
                            'aria-hidden' => "true",
                            'title' => __("No"),
                        ])
                ?>
            </td>
            <td>
                <?= h($profile->use_ttdnsd) ? 
                        $this->Html->tag('span', "", [
                            'class' => "glyphicon glyphicon-ok",
                            'aria-hidden' => "true",
                            'title' => __("Yes"),
                        ]) : 
                        $this->Html->tag('span', "", [
                            'class' => "no",
                            'aria-hidden' => "true",
                            'title' => __("No"),
                        ])
                ?>
            </td>
            <td>
                <?= $this->Html->link(
                        $this->Html->tag('span', "", [
                            'class' => "glyphicon glyphicon-edit",
                            'aria-hidden' => "true",
                            'title' => __("Edit"),
                        ]),
                        ['controller' => 'profiles', 'action' => 'edit', $profile->id],
                        ['escape' => false]
                    )
                ?>

                <?= $this->Html->link(
                        $this->Html->tag('span', "", [
                            'class' => "glyphicon glyphicon-random",
                            'aria-hidden' => "true",
                            'title' => __("Manage domains routing"),
                        ]),
                        [""],
                        ['escape' => false, 'onclick' => "open_window_f('/profiles-routing/index/$profile->id')"]
                    )
                ?>

                <?= $this->Html->link(
                        $this->Html->tag('span', "", [
                            'class' => "glyphicon glyphicon-fire",
                            'aria-hidden' => "true",
                            'title' => __("Manage firewall"),
                        ]),
                        [""],
                        ['escape' => false, 'onclick' => "open_window_f('/profiles-ipfilters/index/$profile->id')"]
                    )
                ?>

                <?= $this->Html->link(
                        $this->Html->tag('span', "", [
                            'class' => "glyphicon glyphicon-repeat",
                            'aria-hidden' => "true",
                            'title' => __("Reload the profile"),
                        ]),
                        ['controller' => 'profiles', 'action' => 'reload', $profile->id],
                        ['escape' => false, 'confirm' => __('Reload the profile?')]
                    )
                ?>
                <?= $this->Html->link(
                        $this->Html->tag('span', "", [
                            'class' => "glyphicon glyphicon-export",
                            'aria-hidden' => "true",
                            'title' => __("Export the profile"),
                        ]),
                        ['controller' => 'profiles', 'action' => 'export', $profile->id],
                        ['escape' => false]
                    )
                ?>

                <?php if($profile->id != 1): ?>
                    <?= $this->Form->postLink(
                            $this->Html->tag('span', "", [
                                'class' => "glyphicon glyphicon-trash",
                                'aria-hidden' => "true",
                                'title' => __("Delete"),
                            ]),
                            ['action' => 'delete', $profile->id],
                            ['escape' => false, 'block' => true, 'confirm' => __("Users and devices attached to this profile will be assigned to the default profile. Do you confirm the deletion of the profile {0}?", h($profile->profilename))]
                        );
                    ?>
                <?php endif ?>
            </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">

      <!-- footer massive actions -->

    </div><!-- /.row -->
  
  
    <?= $this->element('paginator') ?>
  </div><!-- /.box-footer -->
  <?= $this->fetch('postLink') ?>

</div><!-- /.box -->


<script src="/js/selectall.js"></script>
<script>
  $(document).ready(function() {
    $('#' + $("#action").val()).show();
    $('select#action').change(function() {
        $('.dynform').hide();
      $('#' + $(this).val()).show();
      });
  });
</script>
<script>
function open_window_f() {
        window.open(arguments[0], "_blank", "location=no,status=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,top=500,left=500,width=600,height=600");
}
</script>
<script>
$(document).ready(function() {
  $('#scroll_table').DataTable( {
    "scrollX": true,
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false,
    "searching": false,

  } );
});
</script>
