<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->

    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Wi-fi settings')?></h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <?= $this->Form->create('wpa') ?>
      <div class="box-body">

        <div class="form-group">
          <label for="inputWPA" class="col-sm-12 control-label"><?= __('Edit')." ".h($wpa_config_file) ?></label>
          <?= $this->Form->control('wpa_config', [
				'style' => 'font-family: monospace',
                'rows' => '15',
                'type' => 'textarea',
                'label' => false,
                'class' => "form-control",
                'id' => "inputWPA",
                'value' => $wpa_config_file_contents,
                'placeholder' => "",
                //'required' => 'required',
              ]);
          ?>
        </div>

      </div>

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
            [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]
          ) 
        ?>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div>
</div>
