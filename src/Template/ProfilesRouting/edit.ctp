<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Edit routing')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create($profilesRouting) ?>
      <div class="box-body">

        <div class="form-group">
          <label for="inputProfilename"><?= __('Address')?></label>
            <?= $this->Form->control('address', [
                'label' => false,
                'class' => "form-control",
                'id' => "inputProfilename",
                'placeholder' => "http://www.domain.com www.domain2.com",
                'required' => 'required',
                'value' => $profilesRouting->address,
              ]);
            ?>
            <?= $this->Flash->render('error_address') ?>
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('enabled', [
                'type' => 'checkbox',
                'label' => " ".__('Enable routing'),
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
                'value' => $profilesRouting->routing,
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
          <!--<label for="inputCategory" class="col-sm-2 control-label"><?= __('Category (Optional)')?></label>-->
            <?= $this->Form->control('category', [
                //'type' => 'select',
                'type' => 'hidden',
                'label' => false,
                'class' => "form-control",
                'options' => $categories,
                'empty' => __('(Select existing category)'),
                'id' => "inputCategory",
                'value' => $profilesRouting->category,
              ]);
            ?>
        </div>
        <div class="form-group">
          <!--<label for="inputCategoryNew" class="col-sm-1 control-label"><?= __('Set a new category')?></label>-->
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
				[ 'controller' => 'profiles-routing', 'action' => 'index', $profile['id']], 
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
