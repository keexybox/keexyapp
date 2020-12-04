<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Check blocked domain')?></h3>
        <hr>
        <div class="alert alert-info">
          <i class="icon fa fa-info"></i>
            <?= __('In some cases a domain can be filtered while it is not in the blacklist. In most cases, it comes from a target CNAME record which is in the Blacklist. This form allows you to check the domains causing the blocking.') ?>
        </div>
        <?= $this->Form->create('search', array('type'=>'get')) ?>
        <div class="form-group">
          <label for="domain"><?=  __("Domain") ?></label>
          <div class="input-group">
            <div class="input-group-addon">
               <i class="fa fa-globe"></i>
            </div>
            <?= $this->Form->control('domain', [
              'id' => 'domain',
              'type' => 'text',
              'label' => false,
              'class' => "form-control",
              'placeholder' => null,
              'value' => $search_domain,
              ]);
            ?>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-12">
            <?= $this->Form->button(
              $this->Html->tag('span', '', [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("Check"),
                  ])."&nbsp;".__('Check'),
              [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
            ?>
          </div>
        </div>
        <?= $this->Form->end() ?>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <?= $this->Form->create('action', [ 
        'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
        ])
      ?>
      <div class="box-body">
        <?php if(isset($bl_domains)): ?>
        <div class="alert alert-warning">
          <i class="icon fa fa-info"></i>
            <?= __('The domains listed below could block the domain {0}.', $search_domain).' '.__('You can delete them or change their category to fix the issue.') ?>
        </div>
        <table class="table table-bordered table-striped" id="scroll_table">
          <thead>
            <tr>
              <th><input type="checkbox" id="select_all"/></th>
              <th><?= $this->Paginator->sort('zone', __('Domain')) ?></th>
              <th><?= $this->Paginator->sort('category', __('Category')) ?></th>
            </tr>
          </thead>
          
          <tbody>
            <?php foreach ($bl_domains as $bl_domain): ?>
            <tr>
              <td>
                <input class="checkbox" type="checkbox" name="check[]" value="<?= $bl_domain->zone ?>">
              </td>
              <td><?= h($bl_domain->zone) ?></td>
              <td><?= h($bl_domain->category) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php elseif (isset($search_domain)): ?>
        <div class="row">
          <div class="col-sm-12">
            <div class="alert alert-success">
              <i class="icon fa fa-info"></i>
                <?= __('No domain in the blacklist should block {0}.', $search_domain) ?>
            </div>
          </div>
        </div>
        <?php endif ?>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <div class="row">
          <?php if(isset($bl_domains)): ?>
          <div class="col-lg-3">
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
            <div class="col-lg-3" name="select-category">
              <?= $this->Form->control('category', [
                'type' => 'select',
                'label' => false,
                'options' => $categories,
                'empty' => __('(Select existing category)'),
                'class' => "form-control",
                ]);
            ?>
            </div>
            <div class="col-lg-3" name="select-category">
              <?= $this->Form->control('newcategory', [
                'label' => false,
                'class' => "form-control",
                'placeholder' => __('Set a new category'),
                ]);
              ?>
            </div>
          </div>
          <div class="col-lg-3">
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
  </div><!-- /.col -->
</div><!-- /.row -->

<script src="/js/selectall.js"></script>
<script>
  $(document).ready(function() {
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
