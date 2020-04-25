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
    <h3 class="box-title"><?= __('Most active users and devices')?></h3>
  </div><!-- /.box-header -->

  <div class="box-body">
    <table class="table table-bordered table-striped" id="scroll_table">
	  <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('client_ip', __('Client IP')) ?></th>
          <th><?= $this->Paginator->sort('queries_count', __('Number of queries')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
	  </thead>
	  <tbody>
        <?php $websites2add = null  ?>
        <?php foreach ($dnslogs as $dnslog): ?>
        <tr>
          <td>
            <input class="checkbox" type="checkbox" name="urls[]" value="<?= $dnslog->DOMAIN ?>">
          </td>
          <td><?= $this->Html->link($dnslog->client_ip, ['controller' => 'connections', 'action' => 'index', '?' => [
              'action' => 'search', 
              'results' => 25, 
              'query' => $dnslog->client_ip, 
            ]]) ?>
          </td>
          <td><?= $dnslog->queries_count ?></td>
          <td></td>
        </tr>
        <?php endforeach; ?>
      <tbody>
    </table>

  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
    </div><!-- /.row -->
  
    <?= $this->element('paginator') ?>
  </div><!-- /.box-footer -->

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
