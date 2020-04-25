<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Edit device: {0}', h($device->devicename))?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create($device) ?>
      <div class="box-body">

        <div class="form-group">
          <label for="inputDevice"><?= __('Name')?></label>
          <?= $this->Form->control('devicename', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputDevice",
                'placeholder' => __("Name"),
              ]);
           ?>
        </div>
    
        <div class="form-group">
          <label for="inputMac"><?= __('MAC address')?></label>
          <?= $this->Form->control('mac', [
                        'label' => false,
                        'class' => "form-control",
                        'id' => "inputMac",
                        'placeholder' => "00:00:00:00:00:00",
                    ]);
          ?>
        </div>
    
        <div class="form-group">
          <label for="inputProfile" class="col-sm-2 control-label"><?= __('Profile')?></label>
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

        <div class="form-group">
          <label for="inputLang"><?= __('Language') ?></label>
          <?= $this->Form->control('lang', [
                        'type' => 'select',
                        'label' => false,
                        'options' => $langs,
                        'id' => "inputProfile",
                        'class' => "form-control",
                    ]);
          ?>
        </div>

        <div class="form-group">
          <label for="inputMac"><?= __('DHCP reservation IP address')." ".__('(optional)')?></label>
          <div class="alert alert-info" id="dhcp_info">
            <i class="icon fa fa-info"></i>
			<?= $dhcp_info ?>
          </div>
          <?= $this->Form->control('dhcp_reservation_ip', [
                        'label' => false,
                        'class' => "form-control",
                        'id' => "inputDHCPresip",
                        'placeholder' => '192.168.1.50',
                    ]);
          ?>
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('enabled', [
                 'type' => 'checkbox',
                        'label' => __("Enable"),
                    ])
            ?>
          </label>
        </div>

      </div><!-- /.box-body -->

      <div class="box-footer">
            <?= $this->Html->link(
                    $this->Html->tag('span', '', [
                        'class' => "glyphicon glyphicon-remove-sign",
                        'aria-hidden' => "true",
                        'title' => __("Cancel"),
                        ])."&nbsp;".__('Cancel'),
                    ['controller' => 'devices', 'action' => 'index'], 
                    [ 'class' => "btn btn-default", 'escape' => false]) 
            ?>
            <?= $this->Form->button(
                    $this->Html->tag('span', '', [
                        'class' => "glyphicon glyphicon-save",
                        'aria-hidden' => "true",
                        'title' => __("Save"),
                        ])."&nbsp;".__('Save'),
                    [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
            ?>

      </div><!-- /.box-footer -->

      <?= $this->Form->end() ?>
    </div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
