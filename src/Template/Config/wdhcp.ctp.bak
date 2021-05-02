<?= $this->Flash->render('restart_dhcp') ?>
<legend><?= __('KeexyBox’s configuration wizard').': '.__('DHCP settings') ?></legend>
<div class="row" id="info_install_1">
  <div class="col-md-6">
    <div class="callout callout-info">
      <p><?= __('The DHCP allows you to dynamically assign an IP address and other network settings to each device on your network.') ?></p>
	  <ul>
	    <li><?= __('Disable the DHCP on your Internet router to avoid conflict with KeexyBox DHCP.') ?></li>
	    <li><?= __('Define a range of IP addresses to use for your devices in the {0} network.', $input_network_mask) ?></li>
	    <li><?= __('Define a range of IP addresses in the {0} network.', $output_network_mask) ?></li>
	  </ul>
    </div>
  </div>
</div>
<div class="row" id="info_install_2">
  <div class="col-md-6">
    <div class="callout callout-info">
      <p><?= __('The DHCP allows you to dynamically assign an IP address and other network settings to each device on your network.') ?></p>
	  <ul>
	    <li><?= __('Disable the DHCP on your Internet router to avoid conflict with KeexyBox DHCP.') ?></li>
	    <li><?= __('Define a range of IP addresses to use for your devices in the {0} network.', $output_network_mask) ?></li>
	  </ul>
    </div>
  </div>
</div>
<div class="row" id="info_install_3">
  <div class="col-md-6">
    <div class="callout callout-info">
      <p><?= __('The DHCP allows you to dynamically assign an IP address and other network settings to each device on your network.') ?></p>
	  <ul>
	    <li><?= __('Log in to the management interface of your Internet router.') ?></li>
	    <li><?= __('In the DHCP settings, specify the IP address {0} for the DNS.', $output_network_mask) ?></li>
	  </ul>
    </div>
  </div>
</div>
<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('config') ?>
      <div class="box-body">

        <div class="checkbox" id="checkbox_dhcp_enabled">
          <label>
            <?= $this->Form->control('dhcp_enabled', [
                  'type' => 'checkbox',
				  'id' => 'dhcp_enabled',
                  'label' => __('Enable DHCP'),
                  //'default' => $dhcp_enabled->value
                ])
            ?>
          </label>
        </div>

        <div class="checkbox" id="checkbox_dhcp_external">
          <label>
            <?= $this->Form->control('dhcp_external', [
                  'type' => 'checkbox',
                  'id' => 'check_dhcp_network',
                  'label' => __('Check this box if you want to use KeexyBox as DNS only and do not want to use it as gateway.'),
                  //'default' => $dhcp_external->value
                ])
            ?>
          </label>
        </div>
    
		<?php if($dhcp_external->value == 0): ?>
        <div class="box" id="internal_dhcp">
		<?php else: ?>
        <div class="box" id="internal_dhcp" style="display: none">
		<?php endif ?>
          <div class="box-header">
            <h5 class="box-title"><?= __('Address range for the network: {0}', $input_network_mask) ?></h5>
          </div>

          <div class="box-body">
            <div class="form-group col-md-6">
              <label for="inputipstart"><?= __('DHCP start IP') ?></label>
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-laptop"></i>
                </div>
                <?= $this->Form->control('dhcp_start_ip_input',[
                      'label' => false,
                      'default' => $dhcp_start_ip_input->value,
                      'id' => "inputipstart",
                      'placeholder' => "",
                      'class' => "form-control",
                      'data-inputmask' => "'alias': 'ip'",
                      'data-mask' => "",
                    ])
                ?>
              </div>
              <?= $this->Flash->render('error_dhcp_start_ip_input')?>
            </div>
          
            <div class="form-group col-md-6">
              <label for="inputpend"><?= __('DHCP end IP') ?></label>
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-laptop"></i>
                </div>
                <?= $this->Form->control('dhcp_end_ip_input',[
                      'label' => false,
                      'default' => $dhcp_end_ip_input->value,
                      'id' => "inputpend",
                      'placeholder' => "",
                      'class' => "form-control",
      				  'data-inputmask' => "'alias': 'ip'",
      				  'data-mask' => "",
                    ])
                ?>
              </div>
              <?= $this->Flash->render('error_dhcp_end_ip_input')?>
            </div>
          </div>
          <div class="box-footer">
		  </div>
		</div>

        <div class="box" id="external_dhcp">
          <div class="box-header">
            <h5 class="box-title"><?= __('Address range for the network: {0}', $output_network_mask)?></h5>
          </div>

          <div class="box-body">

		    <?php if($dhcp_external->value == 0): ?>
            <div class="alert alert-info" id="dhcp_external_info">
		    <?php else: ?>
            <div class="alert alert-info" id="dhcp_external_info" style="display: none">
		    <?php endif ?>
              <i class="icon fa fa-info"></i>
	            <?= __('DHCP will work on subnet {0} only for devices that have an IP address reserved. However, defining the IP range here is required.', $output_network_mask) ?>
            </div>

            <div class="form-group col-md-6">
              <label for="inputipstart"><?= __('DHCP start IP') ?></label>
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-laptop"></i>
                </div>
                <?= $this->Form->control('dhcp_start_ip_output',[
                      'label' => false,
                      'default' => $dhcp_start_ip_output->value,
                      'id' => "inputipstart",
                      'placeholder' => "",
                      'class' => "form-control",
                      'data-inputmask' => "'alias': 'ip'",
                      'data-mask' => "",
                    ])
                ?>
              </div>
              <?= $this->Flash->render('error_dhcp_start_ip_output')?>
            </div>
          
            <div class="form-group col-md-6">
              <label for="inputpend"><?= __('DHCP end IP') ?></label>
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-laptop"></i>
                </div>
                <?= $this->Form->control('dhcp_end_ip_output',[
                      'label' => false,
                      'default' => $dhcp_end_ip_output->value,
                      'id' => "inputpend",
                      'placeholder' => "",
                      'class' => "form-control",
      				  'data-inputmask' => "'alias': 'ip'",
      				  'data-mask' => "",
                    ])
                ?>
              </div>
              <?= $this->Flash->render('error_dhcp_end_ip')?>
            </div>
          </div>
          <div class="box-footer">
          </div>
		</div>

      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <a onclick="back_link()" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" title="<?= __('Back') ?>"></span>&nbsp;<?= __('Back') ?></a>
        <?= $this->Form->button(__('Next')."&nbsp;".
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-chevron-right",
              'aria-hidden' => "true",
              'title' => __("Next"),
              ]),
            ['class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>
		<a onclick="skip_link()" class="btn btn-danger pull-right float-vertical-align"><?= __('Skip') ?></a>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
<script>
$(function () {
        $("#check_dhcp_network").click(function () {
            if ($(this).is(":checked")) {
                $("#external_dhcp").show();
                $("#internal_dhcp").hide();
                $("#dhcp_external_info").hide();
            } else {
                $("#external_dhcp").show();
                $("#internal_dhcp").show();
                $("#dhcp_external_info").show();
            }
        });
    });
</script>
<script>
var urlParams = new URLSearchParams(location.search);
var install_type = urlParams.get('install_type');
if (install_type == 1) {
  $("#info_install_1").show();
  $("#info_install_2").hide();
  $("#info_install_3").hide();
  $("#checkbox_dhcp_enabled").hide();
  $("#checkbox_dhcp_external").hide();

  document.getElementById("dhcp_enabled").checked = true;
  document.getElementById("dhcp_external").checked = false;
  document.getElementById("check_dhcp_network").checked = false;

} else if (install_type == 2) {
  $("#internal_dhcp").hide();
  $("#checkbox_dhcp_enabled").hide();
  $("#checkbox_dhcp_external").hide();

  $("#info_install_1").hide();
  $("#info_install_2").show();
  $("#info_install_3").hide();
  $("#dhcp_external_info").hide();

  document.getElementById("dhcp_enabled").checked = true;
  document.getElementById("check_dhcp_network").checked = true;

} else if (install_type == 3) {
  $("#internal_dhcp").hide();
  $("#external_dhcp").hide();
  $("#checkbox_dhcp_enabled").hide();
  $("#checkbox_dhcp_external").hide();

  $("#info_install_1").hide();
  $("#info_install_2").hide();
  $("#info_install_3").show();

  document.getElementById("dhcp_enabled").checked = false;
  document.getElementById("check_dhcp_network").checked = false;
}

function back_link() {
  window.location.href = "/config/wnetwork?install_type=" + install_type;
}
function skip_link() {
  window.location.href = "/blacklist/wadd?install_type=" + install_type;
}
</script>
