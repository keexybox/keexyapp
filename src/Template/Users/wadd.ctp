<!-- Main content -->
<legend><?= __('KeexyBox’s configuration wizard').': '.__('Add a new user') ?></legend>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <p><?= __("To connect to the Internet, users or members of your family will need to authenticate on KeexyBox’s captive portal. Here you can create accounts for your users. Complete the form below by assigning a profile and click {0}. Renew the operation as many times as you want to create users.", '<b><i>"'.__("Save").'"</i></b>') ?></p>
	  <p><?= __("If you do not want to create them now, or you do not need to create any more, click {0}.", '<b><i>"'.__("Skip / Next").'"</i></b>') ?></p>
    </div>
  </div>
</div>
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <?= $this->Form->create($user) ?>
      <div class="box-body">
        <div class="form-group">
           <label for="inputDUser"><?= __('Display name') ?></label>
           <?= $this->Form->control('displayname', [
              'label' => false,
              'class' => "form-control",
              'id' => "inputDUser",
              'placeholder' => __("Display name"),
              ]);
           ?>
        </div>
        <div class="form-group">
          <label for="inputUser"><?= __('Login') ?></label>
          <?= $this->Form->control('username', [
              'label' => false,
              'class' => "form-control",
              'id' => "inputUser",
              'placeholder' => __("Name"),
              ]);
          ?>
        </div>
        <div class="form-group">
          <label for="inputPassword"><?= __('Password') ?></label>
          <?= $this->Form->control('password', [
              'type' => 'password',
              'class' => "form-control input",
              'label' => false,
              'id' => "inputPassword",
              'placeholder' => __("Password"),
            ])
          ?>
        </div>
        <div class="form-group">
          <label for="inputPasswordConfirm"><?= __('Confirm password') ?></label>
          <?= $this->Form->control('confirm_password', [
              'type' => 'password',
              'label' => false,
              'id' => "inputPasswordConfirm",
              'class' => "form-control input",
              'placeholder' => __("Confirm password"),
            ])
          ?>
        </div>
        <div class="form-group">
          <label for="inputProfile"><?= __('Profile') ?></label>
          <?= $this->Form->control('profile_id', [
              'type' => 'select',
              'label' => false,
              'options' => $profiles,
              'empty' => __('(select a profile)'),
              'id' => "inputProfile",
              'class' => "form-control input",
               ]);
          ?>
        </div>
        <div class="form-group">
          <label for="inputLang"><?= __('Language') ?></label>
          <?= $this->Form->control('lang', [
             'type' => 'select',
             'label' => false,
             'options' => $langs,
             'id' => "inputProfile",
             'class' => "form-control",
          ]);
          ?>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('enabled', [
              'type' => 'checkbox',
              'label' => __("Enable"),
              'checked' => true, 
              ])
            ?>
          </label>
          <label>
            <?= $this->Form->control('admin', [
              'type' => 'checkbox',
              'label' => " ".__("Admin"),
              'checked' => false, 
            ])
            ?>
          </label>
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
  window.location.href = "/profiles/wadd?install_type=" + install_type;
}
function skip_link() {
  window.location.href = "/devices/wscan?install_type=" + install_type;
}
</script>
