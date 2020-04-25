<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Ended connections')?></h3>
	<hr>
  <!-- /.box-header -->
    <div class="row bottom-buffer">
      <div class="col-sm-2 col-xs-12"><!-- actions -->
        <?= $this->Html->link(
             $this->Html->tag('span', "", [
               //'class' => "glyphicon glyphicon-plus",
               'class' => "fa fa-users",
               'aria-hidden' => "true",
               'title' => __("Current connections"),
             ]),
             ['controller' => 'connections', 'action' => 'index'],
             ['escape' => false])
        ?>
      </div><!-- actions -->
      <!-- search -->
      <?= $this->element('search_general_form') ?>
    </div><!-- /.row -->
  </div>

  <?= $this->Form->create('action', [ 
      'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
    ])
  ?>
  <div class="box-body">
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('name', __('Connection name')) ?></th>
          <th><?= __('Device/User') ?></th>
          <th><?= $this->Paginator->sort('ip', __('IP address')) ?></th>
          <th><?= $this->Paginator->sort('profile_id', __('Profile')) ?></th>
          <th><?= $this->Paginator->sort('type', __('Type')) ?></th>
          <th><?= $this->Paginator->sort('duration', __('Duration')) ?></th>
          <th><?= $this->Paginator->sort('display_start_time', __('Start Time')) ?></th>
          <th><?= $this->Paginator->sort('display_end_time', __('End Time')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($connectionsHistory as $ConnectionHistory): ?>
        <tr>
          <td>
            <input class="checkbox" type="checkbox" name="check[]" value="<?= $ConnectionHistory->id ?>">
          </td>
          <td><?= h($ConnectionHistory->name) ?></td>
          <?php if($ConnectionHistory->type == 'dev'): ?>
          <td>
            <?= $this->Html->link($ConnectionHistory->device->devicename, ['controller' => 'Devices', 'action' => 'edit', $ConnectionHistory->device->id]) ?>
          </td>
          <?php endif; ?>
          <?php if($ConnectionHistory->type == 'usr'): ?>
          <td>
            <?= $this->Html->link($ConnectionHistory->user->username, ['controller' => 'Users', 'action' => 'edit', $ConnectionHistory->user->id]) ?>
          </td>
          <?php endif; ?>
          <td>
            <?php 
              $q_begin_time = $ConnectionHistory->display_start_time->timezone($timezone); 
              $q_end_time = $ConnectionHistory->display_end_time->timezone($timezone); 
            ?>
            <?= $this->Html->link($ConnectionHistory->ip, ['controller' => 'Statistics', 'action' => 'index', '?' => [
            'begin_date' => $q_begin_time->format('Y-m-d H:i:s'),
            'end_date' => $q_end_time->format('Y-m-d H:i:s'),
            'view_type'=> 'index',
            'client_ip' => $ConnectionHistory->ip,
            'domain' => '',
            'filter_status' => '',
            'results' => '25',
            'action' => 'search',
            ]]) ?>
          </td>
          <td>
            <?= $ConnectionHistory->has('profile') ? $this->Html->link($ConnectionHistory->profile->profilename, ['controller' => 'Profiles', 'action' => 'edit', $ConnectionHistory->profile->id]) : '' ?>
          </td>

          <?php if($ConnectionHistory->type == 'dev'): ?>
          <td><?= __('Device') ?></td>
          <?php endif; ?>

          <?php if($ConnectionHistory->type == 'usr'): ?>
          <td><?= __('User') ?></td>
          <?php endif; ?>
          <?php
            $nb_days = $ConnectionHistory->duration / 86400;
            $nb_hours = gmdate("H", $ConnectionHistory->duration);
            $nb_mins = gmdate("i", $ConnectionHistory->duration);
            $nb_secs = gmdate("s", $ConnectionHistory->duration);
          ?>
          <td><?= h((int)$nb_days."d ".$nb_hours."h ".$nb_mins."m ".$nb_secs."s") ?></td>

          <td><?= $ConnectionHistory->display_start_time->timezone($timezone) ?></td>
          <td><?= $ConnectionHistory->display_end_time->timezone($timezone) ?></td>
      

          <td class="actions">
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-eye-open",
                  'aria-hidden' => "true",
                  'title' => __("Connection information"),
                ]),
			    ['controller' => 'connections', 'action' => 'adminview', '?' => [
                   //'client_ip' => $dnslog->client_ip, 
                   //'date_time' => $dnslog->date_time->format('Y-m-d H:i:s')
                   'client_ip' => $ConnectionHistory->ip,
                   //'date_time' => gmdate("Y-m-d H:i:s", $ConnectionHistory['start_time'])
                   'date_time' => $ConnectionHistory->display_start_time->timezone($timezone)->format('Y-m-d H:i:s'),
                ]],
                ['escape' => false]
		      )
			?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-stats",
                  'aria-hidden' => "true",
                  'title' => __("Visited domains"),
                ]),
                ['controller' => 'statistics', 'action' => 'index', '?' => [
                  //'begin_date' => gmdate("Y-m-d H:i:s", $ConnectionHistory['start_time']),
                  //'end_date' => gmdate("Y-m-d H:i:s", $ConnectionHistory['end_time']),
                  'begin_date' => $ConnectionHistory->display_start_time->timezone($timezone)->format('Y-m-d H:i:s'),
                  'end_date' => $ConnectionHistory->display_end_time->timezone($timezone)->format('Y-m-d H:i:s'),
                  'view_type' => 'index', 'client_ip' => $ConnectionHistory->ip,
                  'filter_status' => '',
                  'domain' => '', 
                  'results' => 25, 
                  'action' => 'search',
                ]],
                ['escape' => false]
              )
            ?>
            <?= $this->Form->postLink(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-trash",
                  'aria-hidden' => "true",
                  'title' => __("Delete"),
                  ]),
                ['action' => 'deleteHistory', $ConnectionHistory->id],
                ['escape' => false, 'block' => true, 'confirm' => __('Confirm?')]
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
              'options' => [ 'delete' => __('Delete')],
              'empty' => __('(select action)'),
              'class' => "form-control",
              'required' => "required",
            ]);
          ?>
      </div>
      <div class="col-lg-1">
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-ok",
              'aria-hidden' => "true",
              'title' => __("Run"),
              ])."&nbsp;".__('Run'),
            [ 'class' => "btn btn-default pull-right", 'escape' => false]) 
        ?>
      </div>
    </div><!-- /.row -->
  

    <?= $this->element('paginator') ?>
  
  </div><!-- /.box-footer -->

  <?= $this->Form->end() ?>
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
