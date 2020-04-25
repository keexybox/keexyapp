<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Miscellaneous settings')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->

      <div class="box-body">

        <legend><?= __('Domains routing and DNS options') ?></legend>
        <div class="form-group">
          <label for="clear_dns_cache"><?= __('Clear the domain routing cache') ?></label>
          <?= $this->Form->postLink(
               __('Clear'),
               ['controller' => 'config', 'action' => 'clear_dns_cache'],
               ['escape' => false, 'id' => 'clear_dns_cache', 'class' => 'btn btn-default', 'confirm' => __('Are you sure to clear the domain routing cache?')]
               );
          ?>
        </div>
      </div>

      <?= $this->Form->create('config') ?>
      <div class="box-body">

        <div class="form-group">
          <label for="input_dns_expiration_delay"><?= __('Domain routing cache expiration (days)') ?></label>
          <?= $this->Form->control('dns_expiration_delay', [
                  'label' => false,
                  'type' => 'number', 
                  'min' => 1, 
                  'max' => 15,
                  'value' => $dns_expiration_delay['value'] / 86400,
                  'id' => "input_dns_expiration_delay",
                  'class' => "form-control",
                  //'style' => "width: 5em",
                ])
          ?>
          <?= $this->Flash->render('error_dns_expiration_delay')?>
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('bind_use_redirectors', [
                  'type' => 'checkbox',
                  'label' => __('Use defined DNS as redirectors'),
                  'default' => $bind_use_redirectors->value
                ])
            ?>
          </label>
        </div>

        <legend><?= __('User and device options') ?></legend>
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

        <legend><?= __('Log options') ?></legend>
        <div class="form-group">
          <label for="input_connection_default_time"><?= __('Maximum retention of logs in database (days)') ?></label>
              <?= $this->Form->control('log_db_retention', [
                  'label' => false,
                  'type' => 'number', 
                  'min' => 1, 
                  //'max' => 8760,
                  'value' => $log_db_retention['value'],
                  'id' => "input_connection_default_time",
                  'class' => "form-control",
                  //'style' => "width: 5em",
                ])
              ?>
          <?= $this->Flash->render('error_connection_default_time')?>
		</div>
        <div class="form-group">
          <label for="input_connection_default_time"><?= __('Maximum retention of logs on the hard drive (days)') ?></label>
              <?= $this->Form->control('log_retention', [
                  'label' => false,
                  'type' => 'number', 
                  'min' => 1, 
                  //'max' => 8760,
                  'value' => $log_retention['value'],
                  'id' => "input_connection_default_time",
                  'class' => "form-control",
                  //'style' => "width: 5em",
                ])
              ?>
          <?= $this->Flash->render('error_connection_default_time')?>
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
