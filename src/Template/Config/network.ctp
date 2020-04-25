<?= $this->Flash->render('restart_network') ?>
<legend><?= __('Network settings') ?></legend>
<!-- Main content -->
<?= $this->Form->create('config') ?>
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Input network')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
        <div class="alert alert-info">
          <i class="icon fa fa-info"></i>
	      <?= __('The input network is where the users and devices are located.') ?>
        </div>

        <div class="form-group">
          <label for="inputinterfaceinput"><?= __('Interface') ?></label>
          <?= $this->Form->control('host_interface_input', [
			  'id' => 'host_interface_input',
              'type' => 'select',
              'label' => false,
              'class' => "form-control",
              'options' => $nic_devices,
              'default' => $host_interface_input,
            ]);
          ?>
        </div>

        <div id="wifi_input" class="form-group <?= $wifi_class ?>" style="display:none">
          <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "fa fa-wifi",
              'aria-hidden' => "true",
              'title' => __("Wi-fi settings"),
              ])."&nbsp;".__('Wi-fi settings'),
            '#',
            [ 'class' => "btn btn-default", 'escape' => false, 'onclick' => "open_window_f('/config/wpa')"]) 
          ?>
		</div>


    
        <div class="form-group">
          <label for="inputHostipinput"><?= __('IP address') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('host_ip_input', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputHostipinput",
                'placeholder' => '192.168.2.2',
                'default' => $host_ip_input->value,
                'data-inputmask' => "'alias': 'ip'",
                'data-mask' => "",
              ]);
            ?>
		  </div>
          <?= $this->Flash->render('error_host_ip_input') ?>
        </div>
    
        <div class="form-group">
          <label for="inputHostmaskinput"><?= __('Netmask') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('host_netmask_input', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputHostmaskinput",
                'placeholder' => '255.255.255.0',
                'default' => $host_netmask_input->value,
                'data-inputmask' => "'alias': 'ip'",
                'data-mask' => "",
              ]);
            ?>
		  </div>
          <?= $this->Flash->render('error_host_netmask_input')?>
        </div>
    
      </div>
      <div class="box-footer">
      </div><!-- /.box-footer -->
      <!-- /.box-body -->
    </div><!-- /.box -->
  </div><!-- /.col -->
</div>

  <!-- right column -->
<div class="row">
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Output network')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">

        <div class="alert alert-info">
          <i class="icon fa fa-info"></i>
	      <?= __('The output network is where your internet router is located.') ?>
        </div>

        <div class="form-group">
          <label for="inputinterfaceoutput"><?= __('Interface') ?></label><small><?= " ".__('(It can be the same as input network)') ?></small>
          <?= $this->Form->control('host_interface_output', [
		      'id' => 'host_interface_output',
              'type' => 'select',
              'label' => false,
              'class' => "form-control",
              'options' => $nic_devices,
              'default' => $host_interface_output,
            ]);
          ?>
        </div>

        <div id="wifi_output" class="form-group <?= $wifi_class ?>" style="display:none">
          <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "fa fa-wifi",
              'aria-hidden' => "true",
              'title' => __("Wi-fi settings"),
              ])."&nbsp;".__('Wi-fi settings'),
            '#',
            [ 'class' => "btn btn-default", 'escape' => false, 'onclick' => "open_window_f('/config/wpa')"]) 
          ?>
		</div>
    
        <div class="form-group">
          <label for="inputHostipoutput"><?= __('IP address') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('host_ip_output', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputHostipoutput",
                'placeholder' => '192.168.1.2',
                'default' => $host_ip_output->value,
                'data-inputmask' => "'alias': 'ip'",
                'data-mask' => "",
              ]);
            ?>
		  </div>
          <?= $this->Flash->render('error_host_ip_output') ?>
        </div>
    
        <div class="form-group">
          <label for="inputHostmaskoutput"><?= __('Netmask') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('host_netmask_output', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputHostmaskoutput",
                'placeholder' => '255.255.255.0',
                'default' => $host_netmask_output->value,
                'data-inputmask' => "'alias': 'ip'",
                'data-mask' => "",
              ]);
            ?>
		  </div>
          <?= $this->Flash->render('error_host_netmask_output')?>
        </div>
    
  
      </div>
      <div class="box-footer">
      </div><!-- /.box-footer -->
      <!-- /.box-body -->
    </div><!-- /.box -->
  </div><!-- /.col -->
</div>

<div class="row">
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('DNS and Gateway')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">

        <div class="form-group">
          <label for="inputHostgw"><?= __('Gateway') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('host_gateway', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputHostgw",
                'placeholder' => '192.168.1.1',
                'default' => $host_gateway->value,
                'data-inputmask' => "'alias': 'ip'",
                'data-mask' => "",
              ]);
            ?>
		  </div>
          <?= $this->Flash->render('error_host_gateway')?>
        </div>
    
        <div class="form-group">
          <label for="inputHostdns1"><?= __('DNS 1') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('host_dns1', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputHostdns1",
                'placeholder' => '8.8.8.8',
                'default' => $host_dns1->value,
                'data-inputmask' => "'alias': 'ip'",
                'data-mask' => "",
              ]);
            ?>
		  </div>
          <?= $this->Flash->render('error_host_dns1')?>
        </div>
    
        <div class="form-group">
          <label for="inputHostdns2"><?= __('DNS 2') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('host_dns2', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputHostdns2",
                'placeholder' => '8.8.4.4',
                'default' => $host_dns2->value,
                'data-inputmask' => "'alias': 'ip'",
                'data-mask' => "",
              ]);
            ?>
		  </div>
          <?= $this->Flash->render('error_host_dns2')?>
        </div>
  
      </div>
      <div class="box-footer">
      </div><!-- /.box-footer -->
      <!-- /.box-body -->
    </div><!-- /.box -->
  </div><!-- /.col -->
</div>
  
<div class="row">
  <div class="col-md-6">
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
  </div>
</div><!-- /.row -->
<?= $this->Form->end() ?>
<script>
function open_window_f() {
        window.open(arguments[0], "_blank", "location=no,status=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,top=500,left=500,width=600,height=600");
}
</script>

<script>
  $(document).ready(function() {
    //$('.wlan0').show();
	//alert($('#host_interface_input').val());
	$('#wifi_input.' + $('#host_interface_input').val()).show();

    $('select#host_interface_input').change(function() {
        $('#wifi_input').hide();
        $('#wifi_input.' + $(this).val()).show();
    });
  });

</script>
<script>
  $(document).ready(function() {
	$('#wifi_output.' + $('#host_interface_output').val()).show();

    $('select#host_interface_output').change(function() {
        $('#wifi_output').hide();
        $('#wifi_output.' + $(this).val()).show();
    });
  });
</script>
