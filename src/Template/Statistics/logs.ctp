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
    <h3 class="box-title"><?= __('Access log')?></h3>
    <?= $this->element('paginator') ?>
  </div>

  <?= $this->Form->create('action', [ 
          'type' => 'get',
          'url' => ['controller' => 'blacklist', 'action' => 'add' ],
          'class' => 'form-horizontal',
          'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
          ])
  ?>
  <!-- /.box-header -->
  <div class="box-body">
    <table class="table table-bordered table-striped" id="scroll_table">
	  <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('blocked', __('Filter status')) ?></th>
          <th><?= $this->Paginator->sort('domain', __('Domain')) ?></th>
          <th><?= $this->Paginator->sort('client_ip', __('Client IP')) ?></th>
          <th><?= $this->Paginator->sort('profile_id', __('Profile')) ?></th>
          <th><?= $this->Paginator->sort('date_time', __('Time')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
	  </thead>
  
	  <tbody>

        <?php foreach ($dnslogs as $dnslog): ?>
        <tr>
          <td>
            <input class="checkbox" type="checkbox" name="urls[]" value="<?= $dnslog->domain ?>">
          </td>
          <td>
          <?= h($dnslog->blocked) ? 
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-ban-circle critical-status-color",
                'aria-hidden' => "true",
                'title' => __('Blocked by {0} category', $dnslog->category),
              ]) : 
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-ok-circle success-status-color",
                'aria-hidden' => "true",
                'title' => __('Accepted'),
              ])
          ?>
          </td>
          <td><a href="<?= "http://$dnslog->domain" ?>" target="blank" data-toggle="tooltip" title="<?= $dnslog->domain ?>"><?= $dnslog->domain ?></a></td>
          <td><?= $this->Html->link($dnslog->client_ip, ['controller' => 'connections', 'action' => 'adminview', '?' => [
              'client_ip' => $dnslog->client_ip, 
              'date_time' => $dnslog->date_time->format('Y-m-d H:i:s')
            ]]) ?>
          </td>
          <td>
            <?= $this->Html->link($dnslog->keexybox_profiles['profilename'], ['controller' => 'profiles', 'action' => 'edit', $dnslog->profile_id]) ?>
          </td>
          <td><?= $dnslog->date_time->timezone($timezone) ?></td>
          <td>
          <?= $this->Html->link(
            $this->Html->tag('span', "", [
              'class' => "glyphicon glyphicon-eye-open",
              'aria-hidden' => "true",
              'title' => __("View related connection"),
              ]),
              ['controller' => 'connections', 'action' => 'adminview', '?' => [
                'client_ip' => $dnslog->client_ip, 
                'date_time' => $dnslog->date_time->format('Y-m-d H:i:s')
              ]],
              ['escape' => false]
            )
          ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div><!-- /.box-body -->

  <div class="box-footer">
    <div class="row">
      <div class="col-lg-2">
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
