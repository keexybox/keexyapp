<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Current connections')?></h3>
	<hr>
    <div class="row bottom-buffer">
      <div class="col-sm-2 col-xs-12"><!-- actions -->
        <?= $this->Html->link(
             $this->Html->tag('li', "", [
               //'class' => "glyphicon glyphicon-plus",
               'class' => "fa fa-lg fa-clock-o",
               //'aria-hidden' => "true",
               'title' => __("Ended connections"),
             ]),
             ['controller' => 'connections', 'action' => 'history'],
             ['escape' => false])
        ?>
        <?= $this->Html->link(
             $this->Html->tag('li', "", [
               //'class' => "glyphicon glyphicon-plus",
               'class' => "fa fa-lg fa-laptop",
               //'aria-hidden' => "true",
               'title' => __("Connect a device to the Internet"),
             ]),
             ['controller' => 'devices', 'action' => 'index'],
             ['escape' => false])
        ?>
      </div><!-- actions -->
      <!-- search -->
      <?= $this->element('search_general_form') ?>
    </div><!-- /.row -->
  </div><!-- /.box-header -->

  <?= $this->Form->create('action', [ 
        'class' => 'form-horizontal',
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
          <th><?= $this->Paginator->sort('status', __('Status')) ?></th>
          <th><?= $this->Paginator->sort('profile_id', __('Profile')) ?></th>
          <th><?= $this->Paginator->sort('type', __('Type')) ?></th>
          <th><?= $this->Paginator->sort('display_start_time', __('Start Time')) ?></th>
          <th><?= $this->Paginator->sort('display_end_time', __('End Time')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($activesConnections as $ActiveConnection): ?>
        <tr>
          <td>
            <input class="checkbox" type="checkbox" name="check[]" value="<?= $ActiveConnection->name.";".$ActiveConnection->ip.";".$ActiveConnection->type ?>">
          </td>
          <td><?= h($ActiveConnection->name) ?></td>

          <?php if($ActiveConnection->type == 'dev'): ?>
          <td>
            <?= $this->Html->link($ActiveConnection->device->devicename, ['controller' => 'Devices', 'action' => 'edit', $ActiveConnection->device->id]) ?>
          </td>
          <?php endif; ?>
          <?php if($ActiveConnection->type == 'usr'): ?>
          <td>
            <?= $this->Html->link($ActiveConnection->user->username, ['controller' => 'Users', 'action' => 'edit', $ActiveConnection->user->id]) ?>
          </td>
          <?php endif; ?>

          <td>
            <?php $q_begin_time = $ActiveConnection->display_start_time->timezone($timezone); ?>
            <?= $this->Html->link($ActiveConnection->ip, ['controller' => 'Statistics', 'action' => 'index', '?' => [
            'begin_date' => $q_begin_time->format('Y-m-d H:i:s'),
            'end_date' => '',
            'view_type'=> 'index',
            'client_ip' => $ActiveConnection->ip,
            'domain' => '',
            'filter_status' => '',
            'results' => '25',
            'action' => 'search',
            ]]) ?>
          </td>
          <td>
            <?php if($ActiveConnection->status == 'running') : ?>
              <font color="green">
              <?= $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-play",
                  'aria-hidden' => "true",
                  'title' => __("Connected"),
                  ]);
              ?>
              </font>
            <?php elseif($ActiveConnection->status == 'pause') : ?>
              <font color="red">
              <?= $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-pause",
                  'aria-hidden' => "true",
                  'title' => __("Paused"),
                  ]);
              ?>
              </font>
              
            <?php endif ?>
          </td>
          <td>
            <?= $ActiveConnection->has('profile') ? $this->Html->link($ActiveConnection->profile->profilename, ['controller' => 'Profiles', 'action' => 'edit', $ActiveConnection->profile->id]) : '' ?>
          </td>
    
          <?php if($ActiveConnection->type == 'dev'): ?>
          <td><?= __('Device') ?></td>
          <td><?= $ActiveConnection->display_start_time->timezone($timezone) ?></td>
          <td><?= __('None') ?></td>
          <?php endif; ?>

          <?php if($ActiveConnection->type == 'usr'): ?>
          <td><?= __('User') ?></td>
          <td><?= $ActiveConnection->display_start_time->timezone($timezone) ?></td>
          <td><?= $ActiveConnection->display_end_time->timezone($timezone) ?></td>
          <?php endif; ?>
      

          <td class="actions">
          <!-- Active Devices actions -->
          <?php if($ActiveConnection->type == 'dev'): ?>
            <?php if($ActiveConnection->status == 'running') : ?>
              <?= $this->Html->link(
                  $this->Html->tag('span', "", [
                    'class' => "glyphicon glyphicon-pause",
                    'aria-hidden' => "true",
                    'title' => __("Pause the connection"),
                    ]),
                  ['controller' => 'devices', 'action' => 'pause', $ActiveConnection->name, $ActiveConnection->ip],
                  ['escape' => false, 'confirm' => __('Pause the connection of {0}?', h($ActiveConnection->name))]
                ) 
              ?>
            <?php elseif($ActiveConnection->status == 'pause') : ?>
              <?= $this->Html->link(
                  $this->Html->tag('span', "", [
                    'class' => "glyphicon glyphicon-play",
                    'aria-hidden' => "true",
                    'title' => __("Resume the connection"),
                    ]),
                  ['controller' => 'devices', 'action' => 'run', $ActiveConnection->name, $ActiveConnection->ip],
                  ['escape' => false, 'confirm' => __('Resume the connection of {0}?', h($ActiveConnection->name))]
              )
            ?>
            <?php endif ?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-stop",
                  'aria-hidden' => "true",
                  'title' => __("Disconnect"),
                  ]),
                ['controller' => 'devices', 'action' => 'disconnect', $ActiveConnection->name, $ActiveConnection->ip],
                ['escape' => false, 'confirm' => __('Disconnect {0}?', h($ActiveConnection->name))]
              )
            ?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-repeat",
                  'aria-hidden' => "true",
                  'title' => __("Reconnect"),
                  ]),
                ['controller' => 'devices', 'action' => 'reconnect', $ActiveConnection->name, $ActiveConnection->ip],
                ['escape' => false, 'confirm' => __('Reconnect {0}?', h($ActiveConnection->name))]
              )
            ?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-eye-open",
                  'aria-hidden' => "true",
                  'title' => __("View the connection details"),
                  ]),
                ['action' => 'adminview', '?' => [
                  'client_ip' => $ActiveConnection->ip, 
                  'date_time' => $ActiveConnection->display_start_time->timezone($timezone)->format('Y-m-d H:i:s')
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
                  'begin_date' => $ActiveConnection->display_start_time->timezone($timezone)->format('Y-m-d H:i:s'),
                  'end_date' => '',
                  'view_type' => 'index', 'client_ip' => $ActiveConnection->ip,
                  'filter_status' => '',
                  'domain' => '', 
                  'results' => 25, 
                  'action' => 'search',
                ]],
                ['escape' => false]
              )
            ?>
          <?php endif ?>
          <!-- END Active Devices actions -->

          <!-- Active Users actions -->
          <?php if($ActiveConnection->type == 'usr'): ?>
            <?php if($ActiveConnection->status == 'running') : ?>
              <?= $this->Html->link(
                  $this->Html->tag('span', "", [
                    'class' => "glyphicon glyphicon-pause",
                    'aria-hidden' => "true",
                    'title' => __("Pause the connection"),
                    ]),
                  ['controller' => 'users', 'action' => 'pauseuser', $ActiveConnection->name, $ActiveConnection->ip],
                  ['escape' => false, 'confirm' => __('Pause the connection of {0}?', h($ActiveConnection->name))]
                )
              ?>
            <?php elseif($ActiveConnection->status == 'pause') : ?>
              <?= $this->Html->link(
                  $this->Html->tag('span', "", [
                    'class' => "glyphicon glyphicon-play",
                    'aria-hidden' => "true",
                    'title' => __("Resume the connection"),
                    ]),
                  ['controller' => 'users', 'action' => 'runuser', $ActiveConnection->name, $ActiveConnection->ip],
                  ['escape' => false, 'confirm' => __('Resume the connection of {0}?', h($ActiveConnection->name))]
                )
              ?>
            <?php endif ?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-stop",
                  'aria-hidden' => "true",
                  'title' => __("Disconnect"),
                  ]),
                ['controller' => 'users', 'action' => 'disconnectuser', $ActiveConnection->name, $ActiveConnection->ip],
                ['escape' => false, 'confirm' => __('Disconnect {0}?', h($ActiveConnection->name))]
              )
            ?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-repeat",
                  'aria-hidden' => "true",
                  'title' => __("Reconnect"),
                  ]),
                ['controller' => 'users', 'action' => 'reconnectuser', $ActiveConnection->name, $ActiveConnection->ip],
                ['escape' => false, 'confirm' => __('Reconnect {0}?', h($ActiveConnection->name))]
              )
            ?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-eye-open",
                  'aria-hidden' => "true",
                  'title' => __("View the connection details"),
                  ]),
                ['action' => 'adminview', '?' => [
                  'client_ip' => $ActiveConnection->ip, 
                  'date_time' => $ActiveConnection->display_start_time->timezone($timezone)->format('Y-m-d H:i:s')
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
                  'begin_date' => $ActiveConnection->display_start_time->timezone($timezone)->format('Y-m-d H:i:s'),
                  'end_date' => '',
                  'view_type' => 'index', 'client_ip' => $ActiveConnection->ip,
                  'filter_status' => '',
                  'domain' => '', 
                  'results' => 25, 
                  'action' => 'search',
                ]],
                ['escape' => false]
              )
            ?>
          <?php endif ?>
          <!-- Active Users actions -->
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
          'options' => [ 'run' => __('Resume the connection'), 'pause' => __('Pause the connection'), 'disconnect' => __('Disconnect') , 'reconnect' => __('Reconnect')],
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
