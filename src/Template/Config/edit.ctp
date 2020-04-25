<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Edit setting: {0}', $config->param)?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create($config) ?>
      <div class="box-body">

	    
        <div class="form-group">
			<?= $this->Form->control('value', ['class' => "form-control"]) ?>
        </div>

	  <!--  BODY -->

      </div>
      <!-- /.box-body -->

      <div class="box-footer">
		<?= $this->Html->link(
				$this->Html->tag('span', '', [
					'class' => "glyphicon glyphicon-remove-sign",
					'aria-hidden' => "true",
					'title' => __("Cancel"),
					])."&nbsp;".__('Cancel'),
				$this->request->referer(),
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
