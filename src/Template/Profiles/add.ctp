<!-- Main content -->
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

        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-remove-sign",
              'aria-hidden' => "true",
              'title' => __("Cancel"),
              ])."&nbsp;".__('Cancel'),
            ['controller' => 'profiles', 'action' => 'index'], 
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
