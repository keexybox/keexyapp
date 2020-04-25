<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Manage firewall rules for the profile: {0}', h($profile['profilename'])) ?></h3>
	<hr>
    <div class="row bottom-buffer">
      <div class="col-sm-2 col-xs-12"><!-- actions -->
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-plus",
              'aria-hidden' => "true",
              'title' => __("Add rule on top"),
              ]),
            ['controller' => 'profiles-ipfilters', 'action' => $links['add']],
            ['escape' => false]
          )
        ?>
		<!--
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-export",
              'aria-hidden' => "true",
              'title' => __("export rules"),
              ]),
            ['controller' => 'profiles', 'action' => 'download_export', $profile['id'], '?' => ['profile_id' => $profile['id'], 'export_firewall' => true, ]],
            ['escape' => false]
          )
        ?>
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-import",
              'aria-hidden' => "true",
              'title' => __("import rules"),
            ]),
            ['controller' => 'profiles', 'action' => 'upload_import', '?' => ['profile_id' => $profile['id'], 'import_firewall' => true, ]],
            ['escape' => false]
          )
        ?>
		-->
    
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-repeat",
              'aria-hidden' => "true",
              'title' => __("Reload profile rules"),
            ]),
            ['controller' => 'profiles', 'action' => 'reload', $profile['id']],
            ['escape' => false, 'confirm' => __('Reload rules for this profile?')]
          )
        ?>
      </div><!-- actions -->
      <!-- search -->
      <?= $this->element('search_general_form') ?>
    </div><!-- /.row -->
  </div>
  <!-- /.box-header -->

  <?= $this->Form->create('action', [ 
        'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
      ])
  ?>
  <div class="box-body">
  
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <th><input type="checkbox" id="select_all"/></th>
        <th><?= __('Position') ?></th>
        <th><?= __('Source Profile') ?></th>
        <th><?= __('Destination IP') ?></th>
        <th><?= __('Protocol') ?></th>
        <th><?= __('Ports') ?></th>
        <th><?= __('Target') ?></th>
        <th><?= __('Enabled') ?></th>
        <th class="actions"><?= __('Actions') ?></th>
      </thead>
        
      <tbody id="sortable">
        <?php foreach ($ProfilesIpfilters as $ProfileIpfilter): ?>
        <tr id="o_<?= $ProfileIpfilter->rule_number?>:<?= $ProfileIpfilter->id ?>" class="ui-state-default">
          <td>
            <input class="checkbox" type="checkbox" name="check[]" value="<?= $ProfileIpfilter->id ?>">
          </td>
          <td>
            <?= h($ProfileIpfilter->rule_number) ?>
            <span class="glyphicon glyphicon-resize-vertical"></span>
          </td>
          <td><?= h($ProfileIpfilter->profile->profilename) ?></td>
  
          <?php if($ProfileIpfilter->dest_ip_type == 'net') : ?>
          <td><?= h($ProfileIpfilter->dest_ip."/".$ProfileIpfilter->dest_ip_mask) ?></td>
  
          <?php elseif($ProfileIpfilter->dest_ip_type == 'range') : ?>
          <td><?= h($ProfileIpfilter->dest_iprange_first." - ".$ProfileIpfilter->dest_iprange_last) ?></td>
  
          <?php elseif($ProfileIpfilter->dest_ip_type == 'hostname') : ?>
          <td><?= h($ProfileIpfilter->dest_hostname) ?></td>
  
          <?php endif ?>
  
          <td><?= h($ProfileIpfilter->protocol) ?></td>
  
          <td>
          <?php if($ProfileIpfilter->dest_ports != ''): ?>
            <?= h($ProfileIpfilter->dest_ports) ?>
          <?php else: ?>
            <?= 'ANY' ?>
          <?php endif ?>
          </td>
  
          <td><?= h($ProfileIpfilter->target) ?></td>
    
          <td><?= h($ProfileIpfilter->enabled) ? 
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("Yes"),
                ]) : 
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-ban-circle",
                  'aria-hidden' => "true",
                  'title' => __("No"),
                ])
            ?>
          </td>
        
          <td>
    
          <?= $this->Html->link(
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-plus",
                'aria-hidden' => "true",
                'title' => __("Insert rule after this one"),
                ]),
              ['controller' => 'ProfilesIpfilters', 'action' => 'add', $ProfileIpfilter->profile->id, "?" => ['rule_number' => $ProfileIpfilter->rule_number]],
              ['escape' => false]
            )
          ?>
          <?= $this->Html->link(
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-edit",
                'aria-hidden' => "true",
                'title' => __("Edit"),
                ]),
              ['controller' => 'ProfilesIpfilters', 'action' => 'edit', $ProfileIpfilter->id, $ProfileIpfilter->profile->id],
              ['escape' => false]
            )
          ?>
          <?= $this->Form->postLink(
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-trash",
                'aria-hidden' => "true",
                'title' => __("Delete"),
                ]),
              ['controller' => 'ProfilesIpfilters', 'action' => 'delete', $ProfileIpfilter->id],
              ['escape' => false, 'block' => true, 'confirm' => __('Are you sure to delete the rule {0}?', $ProfileIpfilter->rule_number)]
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
      <div class="col-xs-4">
        <?= $this->Form->control('action', [
            'type' => 'select',
            'label' => false,
            'options' => [ 
              'disable' => __('Disable') , 
              'enable' => __('Enable'), 
              'move_before' => __('Move before position'), 
              'move_after' => __('Move after position'), 
              'copyprofile' => __('Copy to a profile'), 
              'delete' => __('Delete')],
            'empty' => __('(select action)'),
            'id' => "action",
            'class' => "form-control",
            'required' => "required",
          ]);
        ?>
      </div>
    
      <div id="copyprofile" class="dynform" style="display:none">
        <div class="col-xs-2 " name="select-profile">
          <?= $this->Form->control('profile_id', [
              'type' => 'select',
              'label' => false,
              'options' => $profiles,
              'empty' => __('(select a profile)'),
              'class' => "form-control",
            ]);
          ?>
        </div>
      </div>
    
      <div id="move_before" class="dynform" style="display:none">
        <div class="col-xs-2" name="select-position">
          <?= $this->Form->control('move_before_rule_id', [
              'type' => 'select',
              'label' => false,
              'options' => $rules_list,
              'empty' => __('(chose a position)'),
              'class' => "form-control",
            ]);
          ?>
        </div>
      </div>
    
      <div id="move_after" class="dynform" style="display:none">
        <div class="col-xs-2" name="select-position">
          <?= $this->Form->control('move_after_rule_id', [
              'type' => 'select',
              'label' => false,
              'options' => $rules_list,
              'empty' => __('(chose a position)'),
              'class' => "form-control",
            ]);
          ?>
        </div>
      </div>
    
      <div class="col-xs-2">
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
    </div><!-- /.row -->
  
  
    <?= $this->element('paginator') ?>
  </div><!-- /.box-footer -->
  <?= $this->Form->end() ?>
  <?= $this->fetch('postLink') ?>

</div><!-- /.box -->

<script src="/js/jquery-ui.js"></script>
<script src="/js/selectall.js"></script>

<script>
$(document).ready(function() {
	$('#' + $("#action").val()).show();
	$('select#action').change(function() {
		$('.dynform').hide();
		$('#' + $(this).val()).show();
	});
});

$(document).ready(function() {
	$('#' + $("#routing").val()).show();
	$('select#routing').change(function() {
		$('#' + $(this).val()).show();
	});
});

var data;

$(function() {
	$('table tbody#sortable').sortable({
		start: function(event, ui) {
			var start_pos = ui.item.index();
			ui.item.data('start_pos', start_pos);
		},
		update: function(event, ui) {
			data = $(this).sortable('serialize');
			//alert(document.location "&" + "?" + data);
			document.location = "?" + data;
			
		},
		helper: fixWidthHelper
	}).disableSelection();
});

// function that allow to drag and drop full tr in table
function fixWidthHelper(e, ui) {
	ui.children().each(function() {
		$(this).width($(this).width());
	});
	return ui;
}

function sort() {
	document.location = "?" + data;
}

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
