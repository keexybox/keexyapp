<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title"><?= __('Search filters')?></h3>
    <div class="box-tools pull-right">
      <!-- Collapse Button -->
      <button type="button" class="btn btn-box-tool" data-widget="collapse">
        <i class="fa fa-minus"></i>
      </button>
     </div>
    </div>

    <div class="box-body">
      <?= $this->element('search_log_form') ?>
    </div>
    <!-- /.box-body -->

    <div class="box-footer">
  </div><!-- /.box-footer -->
</div><!-- /.box -->

<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Most queried domains')?></h3>
  </div>
  <!-- /.box-header -->

  <?= $this->Form->create('action', [ 
          'type' => 'get',
          'url' => ['controller' => 'blacklist', 'action' => 'add' ],
          'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
          ])
  ?>
  <div class="box-body">
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('domain', __('Domain')) ?></th>
          <th><?= $this->Paginator->sort('queries_count', __('Number of queries')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
	  <tbody>
        <?php $websites2add = null  ?>
        <?php foreach ($dnslogs as $dnslog): ?>
        <tr>
          <td>
            <input class="checkbox" type="checkbox" name="urls[]" value="<?= $dnslog->domain ?>">
          </td>
          <td><?= $this->Html->link($dnslog->domain, 'http://'.$dnslog->domain, ['target' => '_blank']) ?></a></td>
          <td><?= $this->Html->link($dnslog->queries_count, ['controller' => 'statistics', 'action' => 'index', '?' => ['begin_date' => $begin_date, 'end_date' => $end_date, 'client_ip' => '', 'view_type' => 'index', 'domain' => $dnslog->domain, 'results' => 25, 'action' => 'search', 'filter_status' => $filter_status]]) ?></td>
          <td></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
      <div class="col-lg-6">
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-ok",
              'aria-hidden' => "true",
              'title' => __("Add selected domains to blacklist"),
              ])."&nbsp;".__('Add selected domains to blacklist'),
            [ 'class' => "btn btn-default", 'escape' => false]) 
        ?>
      </div>
    </div><!-- /.row -->
  
  
    <?= $this->element('paginator') ?>
  </div><!-- /.box-footer -->
  <?= $this->Form->end() ?>
  <?= $this->fetch('postLink') ?>

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
    $( function() {
        $( "#begin_datepicker" ).datetimepicker({
                format: "YYYY-MM-DD HH:mm:ss",
                locale: "<?= $datetime_picker_locale ?>",
                });
        $( "#end_datepicker" ).datetimepicker({
                format: "YYYY-MM-DD HH:mm:ss",
                locale: "<?= $datetime_picker_locale ?>",
                });
    } );
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
