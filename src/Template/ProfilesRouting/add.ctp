<?php 
// We have to check if we can remove this
$prefillurls = null;
if(isset($prefill_urls)) {
  foreach($prefill_urls as $prefill_url) {
    $prefillurls .= $prefill_url." "; 
  }
}
?>

<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Add domain routing')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('urls') ?>
      <div class="box-body">
        <div class="form-group">
          <label for="inputProfilename"><?= __('Insert multiple domains line per line or separated by a space')?></label>
          <?= $this->Form->control('urls', [
                'type' => 'textarea',
                'label' => false,
                'class' => "form-control",
                'id' => "inputProfilename",
                'value' => $prefillurls,
                'placeholder' => "http://www.domain.com www.domain2.com",
                'required' => 'required',
              ]);
          ?>
        </div>
      
        <div class="checkbox">
          <label>
            <?= $this->Form->control('replace', [
                'type' => 'checkbox',
                'label' => " ".__('Overwrite already existing addresses'),
              ])
            ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('enabled', [
                'type' => 'checkbox',
                'label' => " ".__('Enable routing'),
                'checked' => 'checked',
              ])
            ?>
          </label>
        </div>
      
        <div class="form-group">
          <label for="inputAccesstype"><?= __('Connection type')?></label>
            <?= $this->Form->control('routing', [
                'type' => 'select',
                'label' => false,
                'class' => "form-control",
                'options' => ['direct' => __('Direct'), 'tor' => __('Tor')],
                'empty' => __('(Select a connection type)'),
                'id' => "inputAccesstype",
                'required' => 'required',
              ]);
            ?>
        </div>
      
        <div class="form-group">
          <label for="inputProfile"><?= __('Profile') ?></label>
            <?= $this->Form->control('profile_id', [
                'type' => 'select',
                'label' => false,
                'id' => "inputProfile",
                'class' => "form-control",
                'value' => $profile['id'],
                'disabled' => 'disabled',
              ]);
            ?>
            <?= $this->Form->control('profile_id', [
                'type' => 'hidden',
                'label' => false,
                'id' => "inputProfile",
                'class' => "form-control",
                'value' => $profile['id'],
              ]);
            ?>
        </div>
      
        <div class="form-group">
            <?= $this->Form->control('category', [
                //'type' => 'select',
                'type' => 'hidden',
                'label' => false,
                'class' => "form-control",
                'options' => $categories,
                'empty' => __('(Select existing category)'),
                'id' => "inputCategory",
              ]);
            ?>
        </div>
        <div class="form-group">
            <?= $this->Form->control('newcategory', [
                'type' => 'hidden',
                'label' => false,
                'class' => "form-control",
                'id' => "inputCategoryNew",
                'placeholder' => __('Set a new category'),
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
				['controller' => 'profiles-routing', 'action' => 'index', $profile['id']], 
				[ 'class' => "btn btn-default", 'escape' => false]
			)
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
  </div><!-- /.col -->
</div><!-- /.row -->

