<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Add a firewall rule')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create($profilesIpfilter) ?>
      <div class="box-body">

        <?php if(isset($profile['id'])): ?>
        
            <div class="form-group">
              <label for="inputProfile"><?= __('Source profile') ?></label>
              <?= $this->Form->control('profile', [
                    'type' => 'select',
                    'label' => false,
                    'id' => "inputProfile",
                    'class' => "form-control",
                    'value' => $profile['id'],
                    'disabled' => 'disabled',
                  ]);
              ?>
              <?= $this->Form->control('profile_id', [
                    'type' => 'hidden',
                    'label' => false,
                    'id' => "inputProfile",
                    'class' => "form-control",
                    'value' => $profile['id'],
                  ]);
              ?>
            </div>
        
        <?php else: ?>
        
            <div class="form-group">
              <label for="inputProfile"><?= __('Source profile') ?></label>
              <?= $this->Form->control('profile_id', [
                  'type' => 'select',
                  'label' => false,
                  'options' => $profiles,
                  'empty' => __('(select a profile)'),
                  'id' => "inputProfile",
                  'class' => "form-control",
                  'required' => 'required',
                ]);
              ?>
			</div>
        
        <?php endif ?>

          <div class="form-group">
            <label for="inputDestIpType"><?= __("Destination type") ?></label>
            <?= $this->Form->control('dest_ip_type', [
                'type' => 'select',
                'label' => false,
                'options' => ['net' => __('Network or single IP'), 'range' => __('IP range') ],
                'default' => 'net',
                'id' => "inputDestIpType",
                'class' => "form-control",
                'required' => 'required',
              ]);
            ?>
          </div>
      
        <div class="form-group">
          <div class="dynform" id="net" style="display:none">
            <label><?= __('Destination IP / MASK') ?></label>
			<table class="col-xs-12">
			  <tr><td>
              <?= $this->Form->control('dest_ip',[
                  'label' => false,
                  'id' => "inputIp",
                  'placeholder' => '10.10.10.10',
                  'class' => "form-control",
                  'required' => 'false',
                ])
              ?>
			  </td><td>
              <?= $this->Form->control('dest_ip_mask', [
                  'type' => 'select',
                  'label' => false,
                  'options' => $mask_range,
                  'id' => "inputDestIpMask",
                  'class' => "form-control",
                  'required' => 'false',
                ]);
              ?>
			  </td></tr>
			</table>
          </div>
        </div>
      
        <div class="form-group">
          <div class="dynform" id="range" style="display:none">
            <label for="inputIpRange"><?= __('Destination IP Range') ?></label>
			<table class="col-xs-12">
			<tr><td>
            <?= $this->Form->control('dest_iprange_first',[
                  'label' => false,
                  'id' => "inputStartIpRange",
                  'placeholder' => "10.10.10.1",
                  'class' => "form-control",
                  'required' => 'false',
                ])
            ?>
			</td><td>
            <?= $this->Form->control('dest_iprange_last', [
                  'label' => false,
                  'id' => "inputEndIpRange",
                  'placeholder' => "10.10.10.254",
                  'class' => "form-control",
                  'required' => 'false',
                ]);
            ?>
			</td></tr>
			</table>
            <?= $this->Flash->render('error_iprange') ?>
          </div>
        </div>

        <div class="form-group">
          <label><?= __('Destination port range') ?></label>
          <?php $dest_ports_count = 0 ?>
          <?php foreach($profilesIpfilter['dest_ports'] as $dest_port): ?>
          <div id="div_<?= $dest_ports_count ?>" class="row bottom-buffer">
            <div class="col-xs-12">
              <table>
                <tr><td>
                  <?= $this->Form->control("dest_ports[$dest_ports_count][port]",[
                      'type' => 'text',
                      'label' => false,
                      'id' => "dest_port_".$dest_ports_count,
                      'value' => $dest_port['port'],
                      'placeholder' => __("port"),
                      'class' => "form-control",
                      'required' => 'false',
                    ])
                  ?>
                </td><td>
                  <?= $this->Form->control("dest_ports[$dest_ports_count][last_port]",[
                      'type' => 'text',
                      'label' => false,
                      'id' => "dest_last_port_".$dest_ports_count,
                      'value' => $dest_port['last_port'],
                      'placeholder' => __("last port"),
                      'class' => "form-control",
                    ])
                  ?>
                </td><td>
                  <button class="btn btn-default btn-link" id="remove_link_<?= $dest_ports_count?>" onclick="remove_field(<?= $dest_ports_count ?>); return false;">
                    <span class="glyphicon glyphicon-trash" title="<?= __('Remove the port range') ?>"></span>
                  </button>
                </td></tr>
              </table>
            </div>
          </div>
        
          <?php $dest_ports_count++ ?>
          <?php endforeach ?>
          <div class="row bottom-buffer" id="new_links_div_0">
            <div class="col-xs-12">
              <button class="btn btn-default btn-link" onclick="add_new_field(1); return false;">
                <span class="glyphicon glyphicon-plus" title="<?= __("Add ports") ?>"></span>&nbsp;<?= __('Add ports') ?>
              </button>
            </div>
          </div>

          <?php 
          $ports_count = 1;
          $num_of_ports = 10;
          while ($ports_count <= $num_of_ports) : ?>
            <div class="row bottom-buffer" id="new_div_<?= $ports_count ?>" style="display:none">
              <div class="col-xs-12">
                <table>
                  <tr><td>
                    <?= $this->Form->control("new_dest_ports[$ports_count][port]",[
                        'type' => 'text',
                        'label' => false,
                        'id' => "new_dest_port_$ports_count",
                        'placeholder' => __("port"),
                        'class' => "form-control",
                        'required' => 'false',
                      ])
                    ?>
                  </td><td>
                    <?= $this->Form->control("new_dest_ports[$ports_count][last_port]",[
                        'type' => 'text',
                        'label' => false,
                        'id' => "new_dest_port_last_$ports_count",
                        'placeholder' => __("last port"),
                        'class' => "form-control",
                        ])
                    ?>
                  </td><td>
                  </td></tr>
                </table>
              </div>
              <div class="col-sm-12" id="new_links_div_<?= $ports_count ?>">
                <?php if($ports_count == $num_of_ports): ?>
                  <button class="btn btn-default btn-link" onclick="remove_new_field(<?= $ports_count ?>); return false;">
                    <span class="glyphicon glyphicon-remove" title="<?= __("Remove the port range") ?>"></span><?= __('Remove') ?>
                  </button>
                <?php else: ?>
                  <button class="btn btn-default btn-link" onclick="add_new_field(<?= $ports_count + 1 ?>); return false;">
                    <span class="glyphicon glyphicon-plus" title="<?= __("Add ports") ?>"></span><?= __('Add') ?>
                  </button>
                  <button class="btn btn-default btn-link" onclick="remove_new_field(<?= $ports_count ?>); return false;">
                    <span class="glyphicon glyphicon-remove" title="<?= __("Remove the port range") ?>"></span><?= __('Remove') ?>
                  </button>
                <?php endif ?>
              </div>
        
              <?php $ports_count++; ?>
            </div>
          <?php endwhile ?>
		</div>

        <div class="form-group">
          <label for="inputProtocol"><?= __("Protocol") ?></label>
          <?= $this->Form->control('protocol', [
                'type' => 'select',
                'label' => false,
                'options' => ['tcp' => 'TCP', 'udp' => 'UDP', 'both' => __('TCP and UDP')],
                'default' => 'both',
                'id' => "inputProtocol",
                'class' => "form-control",
                'required' => 'required',
              ]);
          ?>
        </div>

        <div class="form-group">
          <label for="inputTarget"><?= __("Action") ?></label>
          <?= $this->Form->control('target', [
                'type' => 'select',
                'label' => false,
                'options' => ['ACCEPT' => __('Accept'), 'DROP' => __('Drop')],
                'default' => 'network',
                'id' => "inputTarget",
                'class' => "form-control",
                'required' => 'required',
              ]);
          ?>
        </div>

        <div class="checkbox">
          <label>
			<?=$this->Form->control('enabled', [
					'type' => 'checkbox',
					'label' => __('Rule enabled'),
					'checked' => 'checked',
				])
			?>
          </label>
		</div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">

        <?php if(isset($profile['id'])) : ?>
        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-remove-sign",
              'aria-hidden' => "true",
              'title' => __("Cancel"),
              ])."&nbsp;".__('Cancel'),
            ['controller' => 'profiles-ipfilters', 'action' => 'index', $profile['id']], 
            [ 'class' => "btn btn-default", 'escape' => false])
        ?>
    
        <?php else: ?>
    
        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-remove-sign",
              'aria-hidden' => "true",
              'title' => __("Cancel"),
              ])."&nbsp;".__('Cancel'),
            ['controller' => 'profiles-ipfilters', 'action' => 'index'], 
            [ 'class' => "btn btn-default", 'escape' => false])
        ?>
    
        <?php endif ?>
    
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
$(document).ready(function() {
	$('#' + $("#inputDestIpType").val()).show();
	$('select#inputDestIpType').change(function() {
		$('.dynform').hide();
		$('#' + $(this).val()).show();
	});
});

function add_field(field) {
	var scroll = $(window).scrollTop();
	$(document).ready(function() {
		$('#div_' + field).show();
		$('#links_div_' + (field - 1)).hide();
		return false;	
	});
	$("html").scrollTop(scroll);
}

function remove_field(field) {
	var scroll = $(window).scrollTop();
	$(document).ready(function() {
		$('#div_' + field).hide();
		$('#dest_port_' + field).removeAttr('value');
		$('#dest_port_last_' + field).removeAttr('value');
		$('#links_div_' + (field - 1)).show();
		return false;
	});
	$("html").scrollTop(scroll);
}

function add_new_field(field) {
	var scroll = $(window).scrollTop();
	$(document).ready(function() {
		$('#new_div_' + field).show();
		$('#new_links_div_' + (field - 1)).hide();
		return false;	
	});
	$("html").scrollTop(scroll);
}

function remove_new_field(field) {
	var scroll = $(window).scrollTop();
	$(document).ready(function() {
		$('#new_div_' + field).hide();
		//$('#new_dest_port_' + field).removeAttr('value');
		//$('#new_dest_port_last_' + field).removeAttr('value');
		$('#new_dest_port_' + field).val('');
		$('#new_dest_port_last_' + field).val('');
		$('#new_links_div_' + (field - 1)).show();
		return false;
	});
	$("html").scrollTop(scroll);
}
</script>
