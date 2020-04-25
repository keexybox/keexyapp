<?= $this->Flash->render('reconnect') ?>
<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Devices')?></h3>
	<hr>
    <div class="row bottom-buffer">
       <!-- actions -->
      <div class="col-sm-2 col-xs-12">
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-plus",
              'aria-hidden' => "true",
              'title' => __("Add a device"),
            ]),
          ['controller' => 'devices', 'action' => 'add'],
          ['escape' => false])
        ?>
        <?= $this->Html->link(
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-export",
                'aria-hidden' => "true",
                'title' => __("Export devices to CSV"),
              ]),
              ['action' => 'export'],
              ['escape' => false]
            )
        ?>
        <?= $this->Html->link(
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-import",
                'aria-hidden' => "true",
                'title' => __("Import devices from CSV"),
              ]),
              ['action' => 'import'],
              ['escape' => false]
            )
        ?>
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-search",
              'aria-hidden' => "true",
              'title' => __("Scan and add devices"),
            ]),
          ['controller' => 'devices', 'action' => 'scan'],
          ['escape' => false])
        ?>
      </div>
      <!-- actions -->
      <!-- search -->
      <?= $this->element('search_general_form') ?>
    </div><!-- /.row -->

  </div>
  <?= $this->Form->create('action', [ 
        'class' => 'form-horizontal',
        'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
      ])
  ?>
  <!-- /.box-header -->
  <div class="box-body">
  
    <table class="table table-bordered table-striped" id="scroll_table">
	  <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('devicename', __('Name')) ?></th>
          <th><?= $this->Paginator->sort('mac', __('MAC address')) ?></th>
          <th><?= $this->Paginator->sort('profile_id', __('Profile')) ?></th>
          <th><?= $this->Paginator->sort('dhcp_reservation_ip', __('DHCP reservation IP address')) ?></th>
          <th><?= $this->Paginator->sort('enabled', __('Enabled')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
	  </thead>

	  <tbody>
        <?php foreach ($devices as $device): ?>
        <tr>
          <td>
            <input class="checkbox" type="checkbox" name="check[]" value="<?= $device->id ?>">
          </td>
          <td><?= $this->Html->link(h($device->devicename), ['action' => 'edit', $device->id]) ?></td>
          <td><?= h($device->mac) ?></td>
          <td>
            <?= $device->has('profile') ? $this->Html->link($device->profile->profilename, ['controller' => 'Profiles', 'action' => 'edit', $device->profile->id]) : '' ?>
          </td>
          <td><?= h($device->dhcp_reservation_ip) ?></td>
          <td>
            <?= h($device->enabled) ? 
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
    
          <td class="actions">
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-play",
                  'aria-hidden' => "true",
                  'title' => __("Connect the device to the Internet"),
                ]),
                ['action' => 'connect', $device->devicename],
                ['escape' => false, 'confirm' => __('Connect the device {0}?', h($device->devicename))]
              )
            ?>
        
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-edit",
                  'aria-hidden' => "true",
                  'title' => __("Edit"),
                ]),
                ['controller' => 'devices', 'action' => 'edit', $device->id],
                ['escape' => false]
              ) 
            ?>
        
            <?= $this->Form->postLink(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-trash",
                  'aria-hidden' => "true",
                  'title' => __("Delete"),
                ]),
                ['action' => 'delete', $device->id],
                ['escape' => false, 'block' => true, 'confirm' => __('Are you sure you want to delete {0}?', h($device->devicename))]
              );
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      
    </table>
  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
      <div class="col-lg-2">
          <?= $this->Form->control('action', [
              'type' => 'select',
              'label' => false,
              'options' => [
                'connect' => __('Connect'), 
                'disable' => __('Disable'),
                'enable' => __('Enable'),
                'setprofile' => __('Change the profile'),
                'delete' => __('Delete')
                ],
              'empty' => __('(select action)'),
              'class' => "form-control",
              'id' => "action",
              'required' => "required",
            ]);
          ?>
      </div>
  
      <div id="setprofile" class="dynform" style="display:none">
        <div class="col-lg-2" name="select-profile">
          <?= $this->Form->control('profile_id', [
              'type' => 'select',
              'label' => false,
              'options' => $profiles,
              'empty' => __('(select a profile)'),
              'id' => "inputProfile",
              'class' => "form-control",
            ]);
          ?>
        </div>
      </div>
  
      <div class="col-lg-1">
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-ok",
              'aria-hidden' => "true",
              'title' => __("Run"),
              ])."&nbsp;".__('Run'),
            [ 'class' => "btn btn-default pull-right", 'escape' => false]
          ) 
        ?>
      </div>
    </div>
  
    <?= $this->element('paginator') ?>
  
  </div><!-- /.box-footer -->
  <?= $this->Form->end() ?>
  <?= $this->fetch('postLink') ?>

</div><!-- /.box -->

<script src="/js/selectall.js"></script>
<script>
  $(document).ready(function() {
    $('select#action').change(function() {
      $('.dynform').hide();
      $('#' + $(this).val()).show();
    });
	$('#' + $("select#action").val()).show();
  });
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
