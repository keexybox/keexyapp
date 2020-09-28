<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Register to the Access Point')?></h3>
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
        <div class="form-group">
          <label for="inputRegCode"><?= __('Registration code')." ".__('(leave blank if not required)') ?></label>
          <?= $this->Form->control('registration_code', [
             'label' => false,
             'id' => "inputRegCode",
             'class' => "form-control",
          ]);
          ?>
        </div>
        <label>
          <!-- this button is enabled if terms_text fully scrolled -->
          <?= $this->Form->control('accept_checkbox', [
             'for' => 'link_to_terms',
             'type' => 'checkbox',
             'id' => 'accept_checkbox',
             //'label' => __("I have read and accept the {0}", $this->Html->link(__('terms and conditions'), ['controller' => 'users', 'action' => 'terms']) ),
             'label' => '',
             'onchange' => 'document.getElementById("register_button").disabled = !this.checked;'
             ])
          ?>
        </label>
        <?= __("I have read and accept the {0}", 
          $this->Html->link(__('terms and conditions'), 
          '#', 
          ['id' => 'link_to_terms', 'escape' => false, 'onclick' => "open_window_f('/users/terms')" ], 
          ))?>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      <?= $this->Html->link(
        $this->Html->tag('span', '', [
            'class' => "glyphicon glyphicon-remove-sign",
            'aria-hidden' => "true",
            'title' => __("Cancel"),
            ])."&nbsp;".__('Cancel'),
        ['controller' => 'users', 'action' => 'login'], 
        [ 'class' => "btn btn-default", 'escape' => false]) 
      ?>
      <?= $this->Form->button(
        $this->Html->tag('span', '', [
            'class' => "glyphicon glyphicon-save",
            'aria-hidden' => "true",
            'title' => __("Register"),
            ])."&nbsp;".__('Register'),
        [ 'id' => 'register_button', 'class' => "btn btn-info pull-right float-vertical-align", 'disabled' => 'disabled', 'escape' => false]) 
      ?>
      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
<script>
function open_window_f() {
        window.open(arguments[0], "_blank", "location=no,status=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,top=500,left=500,width=600,height=600");
}
</script>
