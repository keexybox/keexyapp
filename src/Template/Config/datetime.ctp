<!-- Main content -->
<?= $this->Flash->render('restart_ntp') ?>
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Date and time settings')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('config') ?>
      <div class="box-body">
        <div class="form-group">
          <label><?= __('Date') ?></label>
          <div class="input-group date">
             <div class="input-group-addon">
               <i class="fa fa-calendar"></i>
             </div>
             <input type="text" name="date" class="form-control pull-right" id="datepicker" value="<?= date('Y')."-".date('m')."-".date('d')?>">
          </div>
        </div>

        <div class="form-group">
          <label><?= __('Time') ?></label>
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-clock-o"></i>
            </div>
            <input type="text" name="time" class="form-control timepicker" value="<?= date('H')."-".date('i')."-".date('s')?>">
          </div>
        </div>

        <div class="form-group">
           <label for="inputtime"><?= __('Time zone') ?></label>
           <?= $this->Form->control('tz', [
             'type' => 'select',
             'label' => false,
             'class' => "form-control select2",
             'options' => $tzlist,
             'default' => date_default_timezone_get(),
             ]);
           ?>
        </div>

        <div class="form-group">
            <label for="inputtime"><?= __('NTP server 1') ?></label>
                <?= $this->Form->control('host_ntp1', [
                        'label' => false,
                        'class' => "form-control input",
                        'default' => $host_ntp1->value,
                    ]);
                ?>
                <?= $this->Flash->render('error_host_ntp1')?>
        </div>

        <div class="form-group">
            <label for="inputtime"><?= __('NTP server 2') ?></label>
                <?= $this->Form->control('host_ntp2', [
                        'label' => false,
                        'class' => "form-control input",
                        'default' => $host_ntp2->value,
                    ]);
                ?>
                <?= $this->Flash->render('error_host_ntp2')?>
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

<?php if($short_lang != 'en'): ?>
<script src="<?= "/adminlte/bower_components/bootstrap-datepicker/js/locales/bootstrap-datepicker.".$short_lang.".js" ?>"></script>
<?php $datepiker_language = "language: '$short_lang'" ?>
<?php else: ?>
<?php $datepiker_language = null ?>
<?php endif ?>

<script>
  $(function () {
    //Date picker
    $('#datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
	  <?= $datepiker_language ?>
    })
    //Timepicker
    $('.timepicker').timepicker({
      showInputs: false,
      minuteStep: 1,
      showMeridian: false,
      showSeconds: true,
      secondStep: 1,
    })
  });
</script>
<script>
  $(function () {
		      //Initialize Select2 Elements
		      $('.select2').select2()
  })
</script>
