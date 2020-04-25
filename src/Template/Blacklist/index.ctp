<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Blacklist categories')?></h3>
	<hr>
    <div class="row bottom-buffer">
      <div class="col-sm-2 col-xs-12"><!-- actions -->
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
              'class' => "glyphicon glyphicon-search",
              'aria-hidden' => "true",
              'title' => __("Query the Blacklist"),
            ]),
            ['controller' => 'blacklist', 'action' => 'query'],
            ['escape' => false]
          )
        ?>
        <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-refresh",
              'aria-hidden' => "true",
              'title' => __("Refresh the Blacklist"),
            ]),
            ['controller' => 'blacklist', 'action' => 'refresh'],
            ['escape' => false]
          )
        ?>
      </div><!-- /.col actions -->
    </div><!-- /.row -->
  </div>
  <!-- /.box-header -->

  <?php if (isset($categories_count) and is_array($categories_count)): ?>
  <?= $this->Form->create('action', [ 
          'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
        ])
  ?>

  <div class="box-body">
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= __('Category') ?></th>
          <th><?= __('Domains') ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories_count as $category): ?>
        <tr>
          <td><input class="checkbox" type="checkbox" name="check[]" value="<?= $category->category ?>"></td>
          <td><?= h($category->category) ?></td>
          <td>
            <?= $this->Html->link(number_format($category->websites, null, ',', ' '),
                [ 'action' => 'query', '?' => ['results' => 25, 'category' => $category->category, 'query' => '', 'action' => 'search']],
                ['escape' => false]
              )
            ?>
          </td>
          <td>
            <?= $this->Html->link(
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-export",
                'aria-hidden' => "true",
                'title' => __("Export all domains of this category"),
                ]),
                ['action' => 'export', $category->category],
                ['escape' => false]
              )
            ?>
            <?= $this->Form->postLink(
              $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-trash",
                  'aria-hidden' => "true",
                  'title' => __("Delete"),
                ]),
                ['action' => 'deleteCategory', $category->category],
                ['escape' => false, 'block' => true, 'confirm' => __("Are you sure you want to delete the category {0} and all the domains it contains?", $category->category)]
              )
            ?>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
      <div class="col-lg-2">
          <?= $this->Form->control('action', [
              'type' => 'select',
              'label' => false,
              'options' => [
                'setcategory' => __('Change the category'), 
                'export' => __('Export categories'),
                'delete' => __('Delete categories'),
                ],
              'empty' => __('(select action)'),
              'class' => "form-control",
              'id' => 'action',
              'required' => "required",
            ]);
          ?>
      </div>
	  <!-- 
	    It will be nice if someone can help to debug why jquery is unable 
	    to display this div while it works for other views.
		It works only after POST request! Impossible to understand the behavior.
      -->
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
        <div class="col-lg-2" name="new-category">
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
    </div><!-- /.row -->
  </div><!-- /.box-footer -->
  <?= $this->Form->end() ?>

  <?php else: ?>
    <div class="box-body">
      <div class="alert alert-info">
        <?= __('The Blacklist is empty.') ?>
        <br>
        <?= __('You can download lists on Internet and import them to the Blacklist.') ?>
      </div>
	</div>
	<div class="box-footer">
	</div>
  <?php endif ?>

</div><!-- /.box -->

<?= $this->fetch('postLink') ?>


<script src="/js/selectall.js"></script>
<!-- 
    It will be nice if someone can help to debug why jquery is unable to display setcategory div above.
	It works only after POST request!  Impossible to understand the behavior.
-->
<script>
  $(document).ready(function() {
    $('select#action').change(function() {
      $('.dynform').hide();
      $('#' + $(this).val()).show();
    });
	$('#' + $("select#action").val()).show();
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
