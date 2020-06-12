<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Wi-Fi Access Point settings')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('config') ?>
      <div class="box-body">
	    
        <div class="checkbox">
          <label>
            <?= $this->Form->control('hostapd_enabled', [
                  'type' => 'checkbox',
                  'label' => __('Enable Access Point'),
                  'default' => $hostapd_enabled
                ])
            ?>
          </label>
        </div>

        <legend><?= __('General')?></legend>

        <div class="form-group">
          <label for="input_hostapd_ssid"><?= __('SSID') ?></label>
          <?= $this->Form->control('hostapd_ssid',[
              'label' => false,
              'default' => $hostapd_ssid,
              'id' => "input_hostapd_ssid",
              'placeholder' => __("WIFI_SSID"),
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_hostapd_ssid')?>
		</div>


        <div class="form-group">
          <label for="input_hostapd_country_code"><?= __('Country') ?></label>
          <?= $this->Form->control('hostapd_country_code',[
              'type' => 'select',
              'label' => false,
              'default' => $hostapd_country_code,
              'options' => $country_list,
              'id' => "input_hostapd_country_code",
              'class' => "form-control select2",
            ])
          ?>
		</div>

        <div class="form-group">
          <label for="input_hostapd_channel"><?= __('Channel') ?></label>
          <?= $this->Form->control('hostapd_channel',[
              'type' => 'select',
              'label' => false,
              'default' => $hostapd_channel,
              'options' => $channel_list,
              'id' => "input_hostapd_channel",
              'class' => "form-control select2",
            ])
          ?>
		</div>

        <div class="form-group">
          <label for="input_hostapd_hw_mode"><?= __('Operation mode') ?></label>
          <?= $this->Form->control('hostapd_hw_mode',[
              'type' => 'select',
              'label' => false,
              'default' => $hostapd_hw_mode,
              'options' => $hw_mode_list,
              'id' => "input_hostapd_hw_mode",
              'class' => "form-control select2",
            ])
          ?>
		</div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('hostapd_wmm_enabled', [
                  'type' => 'checkbox',
                  'label' => __('Enable Wi-Fi Multimedia'),
                  'default' => $hostapd_wmm_enabled
                ])
            ?>
          </label>
        </div>

        <legend><?= __('Bridge') ?></legend>
        <div class="form-group">
          <label for="input_hostapd_interface"><?= __('Wi-Fi Interface') ?></label>
          <?= $this->Form->control('hostapd_interface',[
              'type' => 'select',
              'label' => false,
              'default' => $hostapd_interface,
              'options' => $wifi_interfaces,
              'id' => "input_hostapd_interface",
              'class' => "form-control",
            ])
          ?>
		</div>

        <div class="form-group">
          <label for="input_hostapd_bridge_ports"><?= __('Wired Interface') ?></label>
          <?= $this->Form->control('hostapd_bridge_ports',[
              'type' => 'select',
              'label' => false,
              'default' => $hostapd_bridge_ports,
              'options' => $wired_interfaces,
              'id' => "input_hostapd_bridge_ports",
              'class' => "form-control",
            ])
          ?>
		</div>

        <legend><?= __('Security')?></legend>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('hostapd_ignore_broadcast_ssid', [
                  'type' => 'checkbox',
                  'label' => __('Hide Access Point'),
                  'default' => $hostapd_ignore_broadcast_ssid
                ])
            ?>
          </label>
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('hostapd_ap_isolate', [
                  'type' => 'checkbox',
                  'label' => __('Clients Isolation'),
                  'default' => $hostapd_ap_isolate
                ])
            ?>
          </label>
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('hostapd_auth_algs', [
                  'type' => 'checkbox',
                  'label' => __('Enable Authentication'),
                  'default' => $hostapd_auth_algs
                ])
            ?>
          </label>
        </div>

        <div class="form-group">
          <label for="input_hostapd_wpa_passphrase"><?= __('Passphrase') ?></label>
          <div class="input-group" id="show_hide_password">
            <?= $this->Form->control('hostapd_wpa_passphrase',[
                'type' => 'password',
                'label' => false,
                'default' => $hostapd_wpa_passphrase,
                'id' => "input_hostapd_wpa_passphrase",
                'class' => "form-control",
              ])
            ?>
            <div class="input-group-addon">
              <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
            </div>
          </div>
		</div>

      </div>
      <!-- /.box-body -->

      <div class="box-footer">

        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-remove-sign",
              'aria-hidden' => "true",
              'title' => __("Cancel"),
              ])."&nbsp;".__('Cancel'),
            ['action' => 'index'], 
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
<script>
  $(function () {
		      //Initialize Select2 Elements
		      $('.select2').select2()
  })
</script>
<script>
$(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
});
</script>
