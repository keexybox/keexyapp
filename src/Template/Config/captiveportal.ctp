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

        <legend><?= __('Internet access options') ?></legend>
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


        <legend><?= __('Registration') ?></legend>
        <div class="form-group">
          <label for="input_locale"><?= __('Allow registration') ?></label>
          <?= $this->Form->control('cportal_register_allowed', [
                'label' => false,
                'type' => 'select',
                'options' => [ 
                    0 => __('Disable'), 
                    1 => __('Enable'),
                    2 => __('Internet access without registration'),
                    ],
                'class' => "form-control",
                //'label' => __('Allow users to register'),
                'default' => $cportal_register_allowed
                ])
          ?>
        </div>

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
          <label for="input_cportal_register_code"><?= __('Registration Code') ?></label>
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
          <label for="inputProfile"><?= __('Default profile for new users') ?></label>
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
