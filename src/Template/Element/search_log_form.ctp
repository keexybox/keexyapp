    <div class="row bottom-buffer">
      <!-- search -->
      <?= $this->Form->create('search', array('type'=>'get')) ?>

      <div class="form-group col-sm-3">
	      <label for="begin_datepicker"><?=  __("Start date") ?></label>
          <div class="input-group">
            <div class="input-group-addon">
               <i class="fa fa-clock-o"></i>
            </div>
            <?= $this->Form->control('begin_date', [
                   'id' => 'begin_datepicker',
                   'type' => 'text',
                   'label' => false,
                   'class' => "form-control pull-right",
                   'placeholder' => null,
                   'value' => $begin_date,
                   //'value' => $display_begin_date->timezone('Indian/Reunion'),
                   ]);
            ?>
          </div>
      </div>
      <div class="form-group col-sm-3">
	      <label for="end_datepicker"><?=  __("End date") ?></label>
          <div class="input-group">
            <div class="input-group-addon">
               <i class="fa fa-clock-o"></i>
            </div>
          <?= $this->Form->control('end_date', [
            'id' => 'end_datepicker',
            'type' => 'text',
            'label' => false,
            'class' => "form-control",
            'placeholder' => null,
            'value' => $end_date,
            ]);
          ?>
	    </div>
      </div>
      <div class="form-group col-sm-3">
	      <label for="begin_datepicker"><?=  __("View") ?></label>
          <?= $this->Form->control('view_type', [
            'id' => 'begin_datepicker',
            'type' => 'select',
            'label' => false,
            'class' => "form-control",
            'title' => __('View type'),
            'options' => [
              'index' => __('Charts'),
              'logs' => __('Log'),
              'most_queried' => __('Most queried domains'),
              'most_active_clients' => __('Most active users and devices'),
              ],
            'value' => $view_type,
            ]);
          ?>
      </div>
      <div class="form-group col-sm-3">
	      <label for="results"><?=  __("Results/page / Max top values") ?></label>
          <?= $this->Form->control('results', [
			'id' => 'results',
            'type' => 'select',
            'label' => false,
            'class' => "form-control",
            'title' => __('Results/page'),
            'options' => ['10' => '10', '15' => '15', '20' => '20', '25' => '25', '50' => '50', '100' => '100', '500' => '500', '1000' => '1000', '5000' => '5000'],
            'value' => $results,
            ]);
          ?>
      </div>
      <div class="form-group col-sm-3">
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
      <div class="from-group col-sm-3">
	      <label for="client_ip"><?=  __("Connected User/Device") ?></label>
          <div class="input-group">
            <div class="input-group-addon">
               <i class="fa fa-user"></i>
            </div>
            <?= $this->Form->control('client_name', [
              'id' => 'client_name',
              'type' => 'select',
              'label' => false,
              'empty' => __('(Select a user/device)'),
              'options' => $active_client_options,
              'class' => "form-control select2",
              'placeholder' => null,
              'value' => $search_client_ip,
              ]);
            ?>
	    </div>
      </div>
      <div class="from-group col-sm-3">
	      <label for="client_ip"><?=  __("User/Device IP") ?></label>
          <div class="input-group">
            <div class="input-group-addon">
               <i class="fa fa-laptop"></i>
            </div>
            <?= $this->Form->control('client_ip', [
              'id' => 'client_ip',
              'type' => 'text',
              'label' => false,
              'class' => "form-control",
              'placeholder' => null,
              'value' => $search_client_ip,
              ]);
            ?>
	    </div>
      </div>
      <div class="form-group col-sm-3">
	      <label for="filter_status"><?=  __("Filter status") ?></label>
            <?= $this->Form->control('filter_status', [
              'id' => 'filter_status',
              'type' => 'select',
              'label' => false,
              'class' => "form-control",
              'title' => __('Filter status'),
              'options' => [
                '' => __('All'),
                '1' => __('Blocked'),
                '0' => __('Accepted'),
                ],
              'value' => $filter_status,
              ]);
            ?>
      </div>
      <div class="form-group col-sm-12">
            <?= $this->Form->button(
              $this->Html->tag('span', '', [
                'class' => "glyphicon glyphicon-search",
                'aria-hidden' => "true",
                'title' => __("Filter"),
                ]).'&nbsp;'.__('Filter'),
              [ 'class' => "btn btn-default pull-right", 'escape' => false]) 
            ?>
        <?= $this->Form->control('action', [
          'type' => 'hidden',
          'value' => 'search'])
        ?>
      </div>
      <?= $this->Form->end() ?>
    </div><!-- /.row -->

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
  $(function () {
              //Initialize Select2 Elements
              $('.select2').select2()
  })
</script>
<script>
$(function() {
    $("#client_name option").filter(function() {
        return $(this).val() == $("#client_ip").val();
    }).attr('selected', true);

    $("#client_name").on("change", function() {
        $("#client_ip").val($(this).find("option:selected").attr("value"));
    });
});
</script>
