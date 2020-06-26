<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Add a new user')?></h3>
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
          <label for="inputEmail"><?= __('Email') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-envelope"></i>
            </div>
            <?= $this->Form->control('email', [
              'label' => false,
              'class' => "form-control",
              'id' => "inputEmail",
              'placeholder' => __("@"),
            ]);
            ?>
          </div>
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
	    <label for="expiration_datepicker"><?=  __('Expiration').' '.__('(for no expiration, leave blank.)') ?></label>
        <div class="input-group">
          <div class="input-group-addon">
             <i class="fa fa-clock-o"></i>
          </div>
          <?= $this->Form->control('expiration', [
                 'id' => 'expiration_datepicker',
                 'type' => 'text',
                 'label' => false,
                 'class' => "form-control pull-right",
                 'placeholder' => null,
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
      <?= $this->Html->link(
        $this->Html->tag('span', '', [
            'class' => "glyphicon glyphicon-remove-sign",
            'aria-hidden' => "true",
            'title' => __("Cancel"),
            ])."&nbsp;".__('Cancel'),
        ['controller' => 'users', 'action' => 'index'], 
        [ 'class' => "btn btn-default", 'escape' => false]) 
      ?>
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
	$( function() {
	    $( "#expiration_datepicker" ).datetimepicker({
				format: "YYYY-MM-DD HH:mm:ss",
				locale: "<?= $datetime_picker_locale ?>",
				});
	} );
</script>
