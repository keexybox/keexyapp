<?= $this->Flash->render('restart_dhcp') ?>
<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('DHCP settings')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('config') ?>
      <div class="box-body">

        <div class="box" id="internal_dhcp">
          <div class="box-header">
            <h5 class="box-title"><?= __('Address range for the network: {0}', $input_network_mask) ?></h5>
          </div>

          <div class="box-body">

            <div class="checkbox">
              <label>
                <?= $this->Form->control('dhcp_enabled_input', [
                      'type' => 'checkbox',
                      'id' => 'dhcp_enabled_input',
                      'label' => __('Enable DHCP'),
                      'default' => $dhcp_enabled_input->value
                    ])
                ?>
              </label>
            </div>

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

            <div class="checkbox">
              <label>
                <?= $this->Form->control('dhcp_enabled_output', [
                      'type' => 'checkbox',
                      'id' => 'dhcp_enabled_output',
                      'label' => __('Enable DHCP'),
                      'default' => $dhcp_enabled_output->value
                    ])
                ?>
              </label>
            </div>

            <?php if ($host_interface_input_value == $host_interface_output_value): ?>
		    <?php if($dhcp_enabled_input->value == 1 AND $dhcp_enabled_output->value == 1): ?>
            <div class="alert alert-warning" id="dhcp_external_info">
            <?php else: ?>
            <div class="alert alert-warning" id="dhcp_external_info" style="display: none">
            <?php endif ?>
              <i class="icon fa fa-info"></i>
	            <?= __('DHCP will work on subnet {0} only for devices that have an IP address reserved. However, defining the IP range here is required.', $output_network_mask) ?>
            </div>
            <?php endif ?>

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
            ['class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
<script>
$(function () {
        $("#dhcp_enabled_output").click(function () {
            if ($(this).is(":checked") && $("#dhcp_enabled_input").is(":checked")) {
                $("#dhcp_external_info").show();
            } else {
                $("#dhcp_external_info").hide();
            }
        });
        $("#dhcp_enabled_input").click(function () {
            if ($(this).is(":checked") && $("#dhcp_enabled_output").is(":checked")) {
                $("#dhcp_external_info").show();
            } else {
                $("#dhcp_external_info").hide();
            }
        });
    });
</script>
