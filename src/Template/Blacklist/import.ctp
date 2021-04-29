<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Import domains to the Blacklist')?></h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
        <?= $this->Form->create('fileupload', [ 'type' => 'file']) ?>

        <div class="box-body">
          <div class="form-group">
             <label for="input_file_bl"><?= __('Select tar.gz file') ?></label>
               <?= $this->Form->control('file', [
				'type' => 'file',
                'label' => false,
	          	'class' => "form-control-file",
	          	'id' => "input_file_bl",
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
              'title' => __("Close"),
              ])."&nbsp;".__('Close'),
          '#', 
          [ 'class' => "btn btn-default", 'escape' => false, 'onclick' => "window.open('', '_self', ''); window.close();"]) 
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
