<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Query the Blacklist')?></h3>
	<hr>
    <div class="row bottom-buffer">
      <div class="col-sm-2"><!-- actions -->
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-plus",
              'aria-hidden' => "true",
              'title' => __("Add domains to the Blacklist"),
            ]),
          ['controller' => 'blacklist', 'action' => 'add'],
          ['escape' => false])
        ?>
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-list",
              'aria-hidden' => "true",
              'title' => __("List Blacklist categories"),
            ]),
            ['controller' => 'blacklist', 'action' => 'index'],
            ['escape' => false]
          )
        ?>
      </div><!-- actions -->
      <!-- search -->
      <?= $this->Form->create('search', array('type'=>'get')) ?>
      <div class="col-sm-2">
        <div class="input-group stylish-input-group">
          <?= $this->Form->control('results', [
               'type' => 'select',
               'label' => false,
               'class' => "form-control",
               'title' => __('Results/page'),
               'options' => ['25' => '25', '50' => '50', '100' => '100', '500' => '500', '1000' => '1000'],
               'value' => '25',
             ]);
           ?>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="input-group stylish-input-group">
          <?= $this->Form->control('category', [
              'type' => 'select',
              'label' => false,
              'class' => "form-control",
              'title' => __('Category'),
              'options' => $categories,
              'value' => $category,
            ]);
          ?>
        </div>
      </div>
      <div class="col-sm-5">
        <div class="input-group">
          <input type="text" name="query" class="form-control" value="<?= $search_query ?>" placeholder="<?= 'domain.com' ?>">
          <?= $this->Form->control('action', [
              'type' => 'hidden',
              'value' => 'search'])
          ?>
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-info"><i class="fa fa-search"></i>
            </button>
          </span>
        </div>
      </div>
      <?= $this->Form->end() ?>
    </div><!-- /.row -->

  </div>
  <!-- /.box-header -->

  <?= $this->Form->create('action', [ 
          'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
        ])
  ?>
  <div class="box-body">
  
	<?php if(isset($blacklists)): ?>
    <?= $this->element('paginator') ?>
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('zone', __('Domain')) ?></th>
          <th><?= $this->Paginator->sort('category', __('Category')) ?></th>
        </tr>
	  </thead>
      
	  <tbody>
        <?php foreach ($blacklists as $blacklist): ?>
        <tr>
          <td>
            <?php if($blacklist->id != 1): ?>
              <input class="checkbox" type="checkbox" name="check[]" value="<?= $blacklist->zone ?>">
            <?php endif ?>
          </td>
          <td><?= h($blacklist->zone) ?></td>
          <td><?= h($blacklist->category) ?></td>
        </tr>
        <?php endforeach; ?>
	  </tbody>
    </table>
    <?= $this->element('paginator') ?>
	<?php endif ?>

  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
	  <?php if(isset($blacklists)): ?>
      <div class="col-lg-2">
          <?= $this->Form->control('action', [
              'type' => 'select',
              'label' => false,
              'options' => [
                'setcategory' => __('Change the category'), 
                'delete' => __('Delete')
                ],
              'empty' => __('(select action)'),
              'id' => "action",
              'class' => "form-control",
              'required' => "required",
            ]);
          ?>
      </div>
      <div id="setcategory" class="dynform" style="display:none">
        <div class="col-lg-2" name="select-category">
          <?= $this->Form->control('category', [
            'type' => 'select',
            'label' => false,
            'options' => $categories,
            'empty' => __('(Select existing category)'),
            'class' => "form-control",
            ]);
        ?>
        </div>
        <div class="col-lg-2" name="select-category">
          <?= $this->Form->control('newcategory', [
            'label' => false,
            'class' => "form-control",
            'placeholder' => __('Set a new category'),
            ]);
          ?>
        </div>
      </div>
      <div class="col-lg-1">
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-ok",
              'aria-hidden' => "true",
              'title' => __("Run"),
              ])."&nbsp;".__('Run'),
            [ 'class' => "btn btn-default pull-right", 'escape' => false]) 
        ?>
      </div>
	  <?php endif ?>
    </div><!-- /.row -->
  </div><!-- /.box-footer -->
  <?= $this->Form->end() ?>

</div><!-- /.box -->


<script src="/js/selectall.js"></script>
<script>
  $(document).ready(function() {
    $('#' + $("#action").val()).show();
    $('select#action').change(function() {
        $('.dynform').hide();
      $('#' + $(this).val()).show();
      });
  });
</script>
<script>
$(document).ready(function() {
  $('#scroll_table').DataTable( {
    "scrollX": true,
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false,
    "searching": false,

  } );
});
</script>
