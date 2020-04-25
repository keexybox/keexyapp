<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Services and power management')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
        <legend><?= __('Power management')?></legend>
        <table>
          <tr>
            <td>
              <?= $this->Form->create('halt', ['onsubmit' => "return confirm('".__('Are you sure you want to power off?')."');"]) ?>
                <?= $this->Form->control('action', [
                  'type' => 'hidden',
                  'value' => 'halt'])
                ?>
                <?= $this->Form->button(
                    $this->Html->tag('span', "", [
                      'class' => "glyphicon glyphicon-off",
                      'aria-hidden' => "true",
                      'title' => __("Power off"),
                    ])."  ".__('Power off'),
                     ['escape' => false, 'id' => 'halt', 'class' => 'btn btn-lg btn-danger']
                );
                ?>
              <?= $this->Form->end() ?>
            </td>
            <td>&nbsp;&nbsp;</td>
            <td>
              <?= $this->Form->create('reboot', ['onsubmit' => "return confirm('".__('Are you sure you want to reboot?')."');"]) ?>
                <?= $this->Form->control('action', [
                  'type' => 'hidden',
                  'value' => 'reboot'])
                ?>
      
                <?= $this->Form->button(
                    $this->Html->tag('span', "", [
                      'class' => "glyphicon glyphicon-repeat",
                      'aria-hidden' => "true",
                      'title' => __("Reboot"),
                    ])."  ".__('Reboot'),
                       ['escape' => false, 'id' => 'reboot', 'class' => 'btn btn-lg btn-warning']
                  );
                ?>
              <?= $this->Form->end() ?>
            </td>
          </tr>
        </table>
        </div>

      <?= $this->Form->create('action', [ 
            'class' => 'form-horizontal',
            'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
          ])
      ?>
      <div class="box-body">
        <legend><?= __('Services')?></legend>
        <table class="table table-striped" id="scroll_table">
          <thead>
            <tr>
              <th><input type="checkbox" id="select_all"/></th>
              <th><?= __('Service') ?></th>
              <th><?= __('Description') ?></th>
              <th><?=  __('Status') ?></th>
              <th class="actions"><?= __('Actions') ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($services_status as $service_status): ?>
            <tr>
              <td>
                <input class="checkbox" type="checkbox" name="check[]" value="<?= $service_status['name'] ?>">
              </td>
              <td><?= $service_status['name'] ?></td>
              <td><?= $service_status['description'] ?></td>
              <?php if($service_status['status'] == 0): ?>
                <td><font color="green"><?= __('started') ?></font></td>
              <?php else: ?>
                <td><font color="red"><?= __('stopped') ?></font></td>
              <?php endif ?>
              <td class="actions">
                <?= $this->Html->link(
                    $this->Html->tag('span', "", [
                      'class' => "glyphicon glyphicon-repeat",
                      'aria-hidden' => "true",
                      'title' => __("Reload"),
                    ]),
                    ['controller' => 'tools', 'action' => 'launch_service', $service_status['name'], 'reload'],
                    ['escape' => false, 'confirm' => __('Reload {0}?', h($service_status['name']))]
                  )
                ?>
                <?= $this->Html->link(
                    $this->Html->tag('span', "", [
                      'class' => "glyphicon glyphicon-stop",
                      'aria-hidden' => "true",
                      'title' => __("Stop"),
                    ]),
                    ['controller' => 'tools', 'action' => 'launch_service', $service_status['name'], 'stop'],
                    ['escape' => false, 'confirm' => __('Stop {0}?', h($service_status['name']))]
                  )
                ?>
                <?= $this->Html->link(
                    $this->Html->tag('span', "", [
                      'class' => "glyphicon glyphicon-play",
                      'aria-hidden' => "true",
                      'title' => __("Start"),
                    ]),
                    ['controller' => 'tools', 'action' => 'launch_service', $service_status['name'], 'start'],
                    ['escape' => false, 'confirm' => __('Start {0}?', h($service_status['name']))]
                  )
                ?>
              </td>
            </tr>
        
          <?php endforeach; ?>
          </tbody>
        </table>

      <!--  BODY -->

      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <div class="row">
          <div class="col-lg-2">
              <?= $this->Form->control('action', [
                  'type' => 'select',
                  'label' => false,
                  'options' => [
                    'reload' => __('Reload'), 
                    'stop' => __('Stop'),
                    'start' => __('Start'),
                    ],
                  'empty' => __('(select action)'),
                  'class' => "form-control",
                  'id' => "action",
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
                [ 'class' => "btn btn-default pull-right", 'escape' => false]
              ) 
            ?>
          </div>
        </div>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
    </div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->

<script src="/js/selectall.js"></script>
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
