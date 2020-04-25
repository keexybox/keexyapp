<legend><?= __('KeexyBox’s configuration wizard').': '.__('Scan and add devices') ?></legend>
<div class="row">
  <div class="col-md-12">
    <div class="callout callout-info">
      <p><?= __("By default a device can be connected to the Internet only if its user has authenticated on KeexyBox’s captive portal. However, if you have to connect devices such as a voice assistant or IP camera, you will need to connect them to the Internet without using the captive portal.") ?><p>
      <p><?= __("The devices listed below have been detected on your network. You can declare them to KeexyBox by assigning a profile. If you do not see them all, turn them on and click {0}.", '<b><i>"'.__("Rescan").'"</i></b>') ?></p>
	  <p><?= __("When KeexyBox is set up, remember to connect the devices you have declared to the Internet. To do this, you will have to go to {0}.", '<b><i>"'.__("Connection Settings").'->'.__('Devices').'"</i></b>') ?></p>
      <p><?= __("If you do not want to declare them now, or you do not need to declare any more, click {0}.", '<b><i>"'.__("Skip / Next").'"</i></b>') ?></p>
    </div>
  </div>
</div>
<div class="box box-info">
   <div class="box-header">
   </div>
   <!-- /.box-header -->
   <div class="box-body">

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
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
	  </tbody>
    </table>
  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
      <div class="col-lg-8">
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
      <div class="col-lg-4">
        <a onclick="back_link()" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" title="<?= __('Back') ?>"></span>&nbsp;<?= __('Back') ?></a>
		<a onclick="skip_link()" class="btn btn-success pull-right float-vertical-align"><?= __('Skip / Next') ?><span class="glyphicon glyphicon-chevron-right" aria-hidden="true" title="<?= __('Skip / Next') ?>"></span></a>
        <?= $this->Form->button(__('Add')."&nbsp;".
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-save",
              'aria-hidden' => "true",
              'title' => __("Add"),
              ]),
            ['class' => "btn btn-info pull-right", 'escape' => false]) 
        ?>
        <a onclick="rescan_link()" class="btn btn-default pull-right"><span class="glyphicon glyphicon-refresh" aria-hidden="true" title="<?= __('Rescan') ?>"></span>&nbsp;<?= __('Rescan') ?></a>
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
<script>
var urlParams = new URLSearchParams(location.search);
var install_type = urlParams.get('install_type');
function rescan_link() {
  window.location.href = "/devices/wscan?install_type=" + install_type;
}
function back_link() {
  window.location.href = "/users/wadd?install_type=" + install_type;
}
function skip_link() {
  window.location.href = "/config/wend?install_type=" + install_type;
}
</script>
