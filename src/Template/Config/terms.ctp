<link rel="stylesheet" href="/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">

    <?= $this->Form->create('config') ?>
    <div class="box">
      <div class="box-header">
        <h3 class="box-title"><?= __('Edit the Terms and Conditions') ?></h3>
      </div>

      <div class="box-body pad">
          <textarea class="textarea" name="terms" placeholder="<?= __('Your Terms and Conditions for Internet access') ?>"
                    style="width: 100%; height: 400px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?= h($cportal_terms) ?></textarea>
      </div>

      <div class="box-footer">
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-save",
              'aria-hidden' => "true",
              'title' => __("Save"),
              ])."&nbsp;".__('Save'),
            [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>
      </div><!-- /.box-footer -->
    </div>
    <?= $this->Form->end() ?>
  </div>
</div>
<script src="/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script>
  $(function () {
    //bootstrap WYSIHTML5 - text editor
    $('.textarea').wysihtml5()
  })
</script>
