<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Captive Portal settings')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->

      <?= $this->Form->create('config') ?>
      <div class="box-body">

        <div class="form-group">
          <label for="input_connection_default_time"><?= __('Default user connection time') ?></label>
          <?= $this->Form->control('connection_default_time', [
                'label' => false,
                'type' => 'select', 
                'value' => $connection_default_time / 60,
                'id' => "input_connection_default_time",
                'class' => "form-control",
                'options' => $avail_durations,
                ])
          ?>
          <?= $this->Flash->render('error_connection_default_time')?>
        </div>
    
        <div class="form-group">
          <label for="input_locale"><?= __('Default language for users and devices') ?></label>
          <?= $this->Form->control('locale', [
                'label' => false,
                'type' => 'select', 
                'options' => $avail_languages,
                'id' => "input_locale",
                'class' => "form-control",
                'default' => $locale,
                ])
          ?>
        </div>

        <label><?= __('Information to be collected on the client for internet access') ?></label>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('cportal_record_useragent', [
                  'type' => 'checkbox',
                  'label' => __('Record UserAgent'),
                  'default' => $cportal_record_useragent
                ])
            ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('cportal_record_mac', [
                  'type' => 'checkbox',
                  'label' => __('Record MAC Address'),
                  'default' => $cportal_record_mac
                ])
            ?>
          </label>
        </div>

        <div class="form-group">
          <label for="input_locale"><?= __('Internet access conditions') ?></label>
          <?= $this->Form->control('cportal_register_allowed', [
                'label' => false,
                'type' => 'select',
                'options' => [ 
                    0 => __('Private'), 
                    1 => __('Registration'),
                    2 => __('Free'),
                    ],
                'class' => "form-control",
                'id' => 'cportal_register_allowed',
                //'label' => __('Allow users to register'),
                'default' => $cportal_register_allowed
                ])
          ?>
        </div>

        <div id="reg_1" class="dynform" style="display:none">
          <div class="form-group">
            <label for="input_cportal_register_expiration"><?= __('Duration of registration (days)') ?></label>
            <?= $this->Form->control('cportal_register_expiration', [
                    'label' => false,
                    'type' => 'number', 
                    'min' => 1, 
                    'max' => 3650,
                    'value' => $cportal_register_expiration,
                    'id' => "input_cportal_register_expiration",
                    'class' => "form-control",
                    //'style' => "width: 5em",
                  ])
            ?>
            <?= $this->Flash->render('error_cportal_register_expiration')?>
          </div>
  
          <div class="form-group">
            <label for="input_cportal_register_code"><?= __('Registration code')." ".__('(leave blank if not required)') ?></label>
            <?= $this->Form->control('cportal_register_code',[
                'label' => false,
                'default' => $cportal_register_code,
                'id' => "cportal_register_code",
                'class' => "form-control",
              ])
            ?>
  		    <?= $this->Flash->render('error_cportal_register_code')?>
  		  </div>
  
          <div class="form-group">
            <label for="inputProfile"><?= __('Profile to be defined for new registrations') ?></label>
            <?= $this->Form->control('cportal_default_profile_id', [
                'type' => 'select',
                'label' => false,
                'options' => $profiles,
                'value' => $cportal_default_profile_id,
                'empty' => __('(select a profile)'),
                'id' => "inputProfile",
                'class' => "form-control input",
                 ]);
            ?>
          </div>
        </div>

        <div id="reg_2" class="dynform" style="display:none">
          <div class="form-group">
            <label for="inputProfile"><?= __('User account to use for free access') ?></label>
            <?= $this->Form->control('cportal_default_user_id', [
                'type' => 'select',
                'label' => false,
                'options' => $users,
                'value' => $cportal_default_user_id,
                'id' => "inputProfile",
                'class' => "form-control input",
                 ]);
            ?>
          </div>
        </div>
        <label for="inputProfile"><?= __('Internet access terms and conditions') ?>:</label>
        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "fa fa-edit ",
              'aria-hidden' => "true",
              'title' => __("Edit"),
              ])."&nbsp;".__('Edit'),
            '#',
            [ 'class' => "btn btn-sm btn-default", 'escape' => false, 'onclick' => "open_window_f('/config/editterms')"]) 
        ?>
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
            [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->

<!-- Script to open domains routing or firewall page in a new window  -->
<script>
function open_window_f() {
        window.open(arguments[0], "_blank", "location=no,status=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,top=500,left=500,width=600,height=600");
}
</script>
<script>
  $(document).ready(function() {
    $('select#cportal_register_allowed').change(function() {
      $('.dynform').hide();
      $('#reg_' + $(this).val()).show();
    });
	$('#reg_' + $("select#cportal_register_allowed").val()).show();
  });
</script>
