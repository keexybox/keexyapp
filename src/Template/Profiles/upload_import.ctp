<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Import a profile')?></h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
        <?= $this->Form->create('fileupload', [ 'type' => 'file']) ?>

        <div class="box-body">
          <div class="form-group">
             <label for="input_file_bl"><?= __('Select tar.gz file') ?></label>
				<?= $this->Form->control('file', [
						'type' => 'file',
						'id' => 'file',
						'label' => false,
						'class' => "custom-file-input",
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
					[ 'action' => 'index'], 
					[ 'class' => "btn btn-default", 'escape' => false]) 
			?>
			<?= $this->Form->button(
					$this->Html->tag('span', '', [
						'class' => "glyphicon glyphicon-upload",
						'aria-hidden' => "true",
						'title' => __("Upload"),
						])."&nbsp;".__('Upload'),
					[ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
			?>
        </div><!-- /.box-footer -->

        <?= $this->Form->end() ?>

	 </div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
