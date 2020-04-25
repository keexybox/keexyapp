<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Import users')?></h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
        <?= $this->Form->create('fileupload', [ 'type' => 'file']) ?>

        <div class="box-body">
          <div class="form-group">
             <label for="inputDUser"><?= __('Select csv file') ?></label>
             <?= $this->Form->control('file', [
				'type' => 'file',
                'label' => false,
	          	'class' => "form-control-file",
	          	'id' => "inputProfilename",
		  		'required' => 'required',
		  		'value' => ''
	          ]);
            ?>
		    <?= $this->Flash->render('error_address') ?>
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
