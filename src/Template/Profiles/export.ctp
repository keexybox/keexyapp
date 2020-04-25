<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Export the profile').": ".$profile['profilename'] ?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('export', array('type'=>'get', 'class' => 'form-horizontal')) ?>
      <div class="box-body">

        <div class="checkbox">
          <label>
            <?= $this->Form->control('export_profile', [
                  'type' => 'checkbox',
                  'label' => __('Export settings'),
                  'checked' => 'checked',
                  ]);
            ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('export_times', [
                  'type' => 'checkbox',
                  'label' => __('Export connection schedules'),
                  'checked' => 'checked',
                  ]);
            ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('export_blacklists', [
                  'type' => 'checkbox',
                  'label' => __('Export Blacklist categories'),
                  'checked' => 'checked',
                  ]);
            ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('export_websites', [
                  'type' => 'checkbox',
                  'label' => __('Export domain routing'),
                  'checked' => 'checked',
                  ]);
            ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('export_firewall', [
                  'type' => 'checkbox',
                  'label' => __('Export firewall rules'),
                  'checked' => 'checked',
                  ]);
            ?>
          </label>
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
                  'class' => "glyphicon glyphicon-export",
                  'aria-hidden' => "true",
                  'title' => __("Export"),
                  ])."&nbsp;".__('Export'),
                 [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
