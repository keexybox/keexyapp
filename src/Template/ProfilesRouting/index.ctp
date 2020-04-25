<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Domain routing for profile: {0}', h($profile['profilename']))?></h3>
	<hr>
    <div class="row bottom-buffer">
      <div class="col-sm-2 col-xs-12"><!-- actions -->
		<?= $this->Html->link(
				$this->Html->tag('span', "", [
					'class' => "glyphicon glyphicon-plus",
					'aria-hidden' => "true",
					'title' => __("Add domain routing"),
					]),
				['controller' => 'profiles-routing', 'action' => $links['add']],
				['escape' => false]
			)
		?>
		<!--
		<?= $this->Html->link(
				$this->Html->tag('span', "", [
					'class' => "glyphicon glyphicon-export",
					'aria-hidden' => "true",
					'title' => __("Export domain routing"),
					]),
				['controller' => 'profiles', 'action' => 'download_export', $profile['id'], '?' => ['profile_id' => $profile['id'], 'export_websites' => true, ]],
				['escape' => false]
			)
		?>
		<?= $this->Html->link(
				$this->Html->tag('span', "", [
					'class' => "glyphicon glyphicon-import",
					'aria-hidden' => "true",
					'title' => __("Import domain routing"),
					]),
				['controller' => 'profiles', 'action' => 'upload_import', '?' => ['profile_id' => $profile['id'], 'import_websites' => true, ]],
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
        'class' => 'form-horizontal',
        'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
      ])
  ?>
  <div class="box-body">
  
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('address', __('Address')) ?></th>
          <th><?= $this->Paginator->sort('routing', __('Connection type')) ?></th>
          <th><?= $this->Paginator->sort('profile_id', __('Profile')) ?></th>
          <th><?= $this->Paginator->sort('enabled', __('Enabled')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
          
      <tbody>
        <?php foreach ($ProfilesUrls as $ProfileUrls): ?>
        <tr>
          <td>
            <input class="checkbox" type="checkbox" name="check[]" value="<?= $ProfileUrls->id.";".$ProfileUrls->routing ?>">
          </td>
          <td>
            <?= $this->Html->link(
                $ProfileUrls->address, 
                  'http://'.$ProfileUrls->address,
                  ['target' => '_blank']
                ) 
            ?>
          </td>
          <td>
            <?php
            if($ProfileUrls->routing == 'direct') { $routing = __('Direct'); }
            elseif($ProfileUrls->routing == 'tor') { $routing = __('Tor'); }
            else {$ProfileUrls = $ProfileUrls->routing;}
            echo $routing;
            ?>
          </td>
          <td>
            <?= $this->Html->link($ProfileUrls->profile->profilename, ['controller' => 'profiles', 'action' => 'edit', $ProfileUrls->profile->id]) ?>
          </td>
          <td>
            <?= h($ProfileUrls->enabled) ? 
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("yes"),
                ])
                : 
                $this->Html->tag('span', "", [
                  'class' => "",
                  'aria-hidden' => "true",
                  'title' => __("no"),
                ])
            ?>
          </td>
          <td>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-edit",
                  'aria-hidden' => "true",
                  'title' => __("Edit"),
                  ]),
                ['controller' => 'ProfilesRouting', 'action' => 'edit', $ProfileUrls->id, $ProfileUrls->profile->id],
                ['escape' => false]
              )
            ?>
            <?= $this->Form->postLink(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-trash",
                  'aria-hidden' => "true",
                  'title' => __("Delete"),
                  ]),
                ['controller' => 'ProfilesRouting', 'action' => 'delete', $ProfileUrls->id],
                ['escape' => false, 'block' => true, 'confirm' => __('Are you sure you want to delete {0}?', h($ProfileUrls->address))]
              )
            ?>
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
            'options' => [ 
            'disable' => __('Disable') , 
            'enable' => __('Enable'), 
            'copyprofile' => __('Copy to a profile'), 
            //'setcategory' => __('change category'), 
            'setrouting' => __('Change connection type'), 
            'delete' => __('Delete')],
            'empty' => __('(select action)'),
            'id' => "action",
            'class' => "form-control",
            'required' => "required",
            ]);
        ?>
      </div>
      <div id="setcategory" class="dynform" style="display:none">
        <div class="col-lg-2" name="select-category">
          <?= $this->Form->control('category', [
              'type' => 'select',
              'label' => false,
              'options' => $categories,
              'empty' => __('(Select existing category)'),
              'class' => "form-control",
              ]);
          ?>
        </div>
        <div class="col-lg-2" name="select-category">
          <?= $this->Form->control('newcategory', [
              'label' => false,
              'class' => "form-control",
              'placeholder' => __('Set a new category'),
            ]);
          ?>
        </div>
      </div>
      <div id="setrouting" class="dynform" style="display:none">
        <div class="col-lg-2" name="select-routing">
          <?= $this->Form->control('routing', [
              'type' => 'select',
              'label' => false,
              'options' => ['direct' => __('Direct'), 'tor' => __('Tor')],
              'empty' => __('(Select a connection type)'),
              'class' => "form-control",
            ]);
          ?>
        </div>
      </div>
      <div id="copyprofile" class="dynform" style="display:none">
        <div class="col-lg-2" name="select-profile">
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

$(document).ready(function() {
	$('#' + $("#routing").val()).show();
	$('select#routing').change(function() {
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
