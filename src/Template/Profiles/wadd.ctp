<!-- Main content -->
<legend><?= __('KeexyBox’s configuration wizard').': '.__('Add a new profile') ?></legend>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <p><?= __("Profiles are used to define connection settings such as access times, domain categories to block, type of connection. Profiles will be assigned to users or your devices. Here you can create profiles.") ?></p>
	  <ul>
	    <li><?= '<b>'.__('Name').'</b>: '.__('Set the name of the profile.') ?></li>
	    <li><?= '<b>'.__('Default connection type').'</b>: '.__('Use anonymous connection (Tor) or not (Direct).') ?></li>
	    <li><?= '<b>'.__('Default Firewall rule').'</b>: '.__('Drop or allow connections to the Internet by default.') ?></li>
	  </ul>
	  <p><?= __("Click {0} if you want to create profiles later or you do not have other profiles to create.", '<b><i>"'.__('Skip / Next').'"</i></b>' ) ?></p>
    </div>
  </div>
</div>
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Add a new profile')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create($profile) ?>
      <div class="box-body">

        <div class="form-group">
          <label for="inputProfilename"><?= __('Name')?></label>
          <?= $this->Form->control('profilename', [
              'label' => false,
              'class' => "form-control",
              'id' => "inputProfilename",
              'placeholder' => __("Name"),
            ]);
          ?>
        </div>

        <div class="form-group">
          <label for="inputConnType"><?= __('Default connection type')?></label>
          <?= $this->Form->control('default_routing', [
              'type' => 'select',
              'label' => false,
              'class' => "form-control",
              'options' => ['direct' => __('Direct'), 'tor' => __('Tor')],
			  'default' => 'direct',
              'empty' => __('(Select a connection type)'),
              'id' => "inputConnType",
            ]);
          ?>
        </div>

        <div class="form-group">
          <label for="inputFirewallRule"><?= __('Default Firewall rule')?></label>
          <?= $this->Form->control('default_ipfilter', [
              'type' => 'select',
              'label' => false,
              'class' => "form-control",
              'options' => ['ACCEPT' => __('Accept'), 'DROP' => __('Drop')],
			  'default' => 'ACCEPT',
              'empty' => __('(Select a rule)'),
              'id' => "inputFirewallRule",
            ]);
          ?>
        </div>

      </div>
      <!-- /.box-body -->

      <div class="box-footer">

        <a onclick="back_link()" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" title="<?= __('Back') ?>"></span>&nbsp;<?= __('Back') ?></a>
		<a onclick="skip_link()" class="btn btn-success pull-right float-vertical-align"><?= __('Skip / Next') ?>&nbsp;<span class="glyphicon glyphicon-chevron-right" aria-hidden="true" title="<?= __('Skip / Next') ?>"></span></a>
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
var urlParams = new URLSearchParams(location.search);
var install_type = urlParams.get('install_type');
function back_link() {
  window.location.href = "/blacklist/wadd?install_type=" + install_type;
}
function skip_link() {
  window.location.href = "/users/wadd?install_type=" + install_type;
}
</script>
