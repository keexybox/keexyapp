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

        <div class="checkbox">
          <label>
            <?= $this->Form->control('dhcp_enabled', [
                  'type' => 'checkbox',
                  'label' => __('Enable DHCP'),
                  'default' => $dhcp_enabled->value
                ])
            ?>
          </label>
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('dhcp_external', [
                  'type' => 'checkbox',
                  'id' => 'check_dhcp_network',
                  'label' => __('Check this box if you want to use KeexyBox as DNS only and do not want to use it as gateway.'),
                  'default' => $dhcp_external->value
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
