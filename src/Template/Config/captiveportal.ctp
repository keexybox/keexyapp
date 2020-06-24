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
                'value' => $connection_default_time['value'] / 60,
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
                'default' => $locale->value,
                ])
          ?>
        
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('cportal_fast_login', [
                  'type' => 'checkbox',
                  'label' => __('Allow users to connect to the Internet just by accepting the terms of use. No registration required.'),
                  'default' => $cportal_fast_login->value,
                ])
            ?>
          </label>
        </div>

        <legend><?= __('Registration options') ?></legend>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('cportal_register_allowed', [
                  'type' => 'checkbox',
                  'label' => __('Allow users to register'),
                  'default' => $cportal_register_allowed
                ])
            ?>
          </label>
        </div>

        <div class="form-group">
          <label for="input_connection_default_time"><?= __('Duration of registration') ?></label>
              <?= $this->Form->control('log_db_retention', [
                  'label' => false,
                  'type' => 'number', 
                  'min' => 1, 
                  //'max' => 8760,
                  //'value' => $log_db_retention['value'],
                  'id' => "input_connection_default_time",
                  'class' => "form-control",
                  //'style' => "width: 5em",
                ])
              ?>
          <?= $this->Flash->render('error_connection_default_time')?>
		</div>

        <div class="form-group">
          <label for="input_cportal_register_code"><?= __('Registration Code') ?></label>
          <?= $this->Form->control('cportal_register_code',[
              'label' => false,
              'default' => $cportal_register_code->value,
              'id' => "cportal_register_code",
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_cportal_register_code')?>
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
