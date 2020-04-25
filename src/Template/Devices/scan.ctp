<div class="box box-info">
   <div class="box-header">
     <h3 class="box-title"><?= __('Scan and add devices')?></h3>
   </div>
   <!-- /.box-header -->
   <div class="box-body">
     <div class="row bottom-buffer">
       <!-- actions -->
       <div class="col-sm-2">
         <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-list",
              'aria-hidden' => "true",
              'title' => __("List devices"),
              ]),
            ['controller' => 'devices', 'action' => 'index'],
            ['escape' => false]
          )
         ?>
       </div>
       <!-- actions -->
     </div><!-- /.row -->

    <?= $this->Form->create('action', [ 
        'class' => 'form-horizontal',
        'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
      ])
    ?>
  
    <table class="table table-bordered table-striped" id="scroll_table">
	  <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= __('IP address') ?></th>
          <th><?= __('Name') ?></th>
          <th><?= __('MAC address') ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
	  </thead>

	  <tbody>
        <?php if(isset($devices)): ?> 
        <?php foreach ($devices as $device): ?>
        <tr>
          <td>
		    <?php if($device['declared'] == false): ?>
              <input class="checkbox" type="checkbox" name="check[]" value="<?= $device['mac'].";".$device['name'] ?>">
			<?php else: ?>
			  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			<?php endif ?>
          </td>
          <td><?= h($device['ip']) ?></td>
          <td><?= h($device['name']) ?></td>
          <td><?= h($device['mac']) ?></td>
          <td class="actions">
		    <?php if($device['declared'] == false): ?>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-plus",
                  'aria-hidden' => "true",
                  'title' => __("Add this device"),
                ]),
                ['controller' => 'devices', 'action' => 'add', $device['mac'], $device['name']],
                ['escape' => false]
              ) 
            ?>
			<?php endif ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
	  </tbody>
    </table>
  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
      <div class="col-lg-3">
        <?= $this->Form->control('profile_id', [
            'type' => 'select',
            'label' => false,
            'options' => $profiles,
            'empty' => __('(select a profile)'),
            'id' => "inputProfile",
            'class' => "form-control",
            'required' => "required",
          ]);
        ?>
      </div>
      <div class="col-lg-2">
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-ok",
              'aria-hidden' => "true",
              'title' => __("Add devices"),
              ])."&nbsp;".__('Add devices'),
            ['class' => "btn btn-default pull-right", 'escape' => false]) 
        ?>
      </div>
    </div>
    <?= $this->Form->end() ?>
    
    <div class="paginator pull-right">
      <ul class="pagination">
      </ul>
    </div>
  </div><!-- /.box-footer -->

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
