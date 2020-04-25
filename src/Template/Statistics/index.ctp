<!-- Main content -->
<div class="row">
  <!-- Memory BOX -->
  <div class="col-sm-12">
    <!--<div class="box box-info collapsed-box">-->
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
  </div><!-- /.col -->

  <!-- DOUGHNUT BLOCKED -->
  <div class="col-sm-4">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Accepted and blocked domains')?></h3>
      </div>

      <div class="box-body">
	    <canvas id="FilteringChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->

  <!-- DOUGHNUT DOMAINS -->
  <div class="col-sm-4">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Top {0} queried domains', $top_limit)?></h3>
      </div>

      <div class="box-body">
	    <canvas id="TopDomainsChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div>

  <!-- DOUGHNUT CLIENTS -->
  <div class="col-sm-4">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Top {0} active users and devices', $top_limit)?></h3>
      </div>

      <div class="box-body">
		<canvas id="TopClientsChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->

  <!-- BAR DATE TIME BLOCKED -->
  <div class="col-sm-12 hidden-xs">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Accepted and blocked domains')?></h3>
      </div>

      <div class="box-body">
		<canvas id="DateTimeBlockedChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->

  <!-- BAR DATE TIME DOMAINS -->
  <div class="col-sm-12 hidden-xs">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Top {0} queried domains', $top_limit)?></h3>
      </div>

      <div class="box-body">
		<canvas id="DateTimeDomainsChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->

  <!-- BAR DATE TIME CLIENTS -->
  <div class="col-sm-12 hidden-xs">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Top {0} active users and devices', $top_limit)?></h3>
      </div>

      <div class="box-body">
		<canvas id="DateTimeClientsChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->

<script>
//------------------------------
//- CHARTS OF BLOCKED DOUGHNUT -
//------------------------------
FilteringChartOptions = {
  legend: {
    position: 'bottom',
	display: false,
  }
}
var FilteringChartCtx = document.getElementById('FilteringChart').getContext('2d');
var FilteringChartData = {
  datasets: [{
    data: [
      <?= $filtering_data['accepted'] ?>,
      <?= $filtering_data['blocked'] ?>, 
    ],
    backgroundColor: [
      '#00a65a',
      '#f56954'
    ]
  }],

  // These labels appear in the legend and in the tooltips when hovering different arcs
  labels: [
    '<?= __('Total accepted domains') ?>',
    '<?= __('Total blocked domains') ?>'
  ]
};
var myDoughnutChart = new Chart(FilteringChartCtx, {
    type: 'doughnut',
    data: FilteringChartData,
    options: FilteringChartOptions
});
</script>

<script>
//------------------------
//- TOP DOMAINS DOUGHNUT -
//------------------------
TopDomainsChartOptions = {
  legend: {
    position: 'bottom',
	display: false,
  }
}
var TopDomainsChartCtx = document.getElementById('TopDomainsChart').getContext('2d');
var TopDomainsChartData = {
  datasets: [{
    data: [
      <?php foreach ($top_domains_data as $data): ?>
        <?= $data['hits'] ?>, 
      <?php endforeach ?>
    ],
    backgroundColor: [
      <?php foreach ($top_domains_data as $data): ?>
        '<?= $data['color'] ?>', 
      <?php endforeach ?>
    ]
  }],

  // These labels appear in the legend and in the tooltips when hovering different arcs
  labels: [
    <?php foreach ($top_domains_data as $data): ?>
    '<?= $data['domain'] ?>', 
    <?php endforeach ?>
  ]
};
var myDoughnutChart = new Chart(TopDomainsChartCtx, {
    type: 'doughnut',
    data: TopDomainsChartData,
    options: TopDomainsChartOptions
});
</script>

<script>
//------------------------
//- TOP CLIENTS DOUGHNUT -
//------------------------
TopClientsChartOptions = {
  legend: {
    position: 'bottom',
	display: false,
  }
}
var TopClientsChartCtx = document.getElementById('TopClientsChart').getContext('2d');
var TopClientsChartData = {
  datasets: [{
    data: [
      <?php foreach ($top_clients_data as $data): ?>
      <?= $data['hits'] ?>, 
      <?php endforeach ?>
    ],
    backgroundColor: [
      <?php foreach ($top_domains_data as $data): ?>
        '<?= $data['color'] ?>', 
      <?php endforeach ?>
    ]
  }],

  labels: [
    <?php foreach ($top_clients_data as $data): ?>
    '<?= $data['ip'] ?>', 
    <?php endforeach ?>
  ]
};
var myDoughnutChart = new Chart(TopClientsChartCtx, {
    type: 'doughnut',
    data: TopClientsChartData,
    options: TopClientsChartOptions
});
</script>

<script>
//-------------------------
//- DATE TIME BLOCKED BAR -
//-------------------------
DateTimeBlockedChartOptions = {
  scales: {
    xAxes: [{
      stacked: true,
    }],
    yAxes: [{
      stacked: true,
    }]
  },
  legend: {
    position: 'bottom',
	display: true,
  },
  elements: {
    point:{
      radius: 0
    }
  }
}
var DateTimeBlockedChartCtx = document.getElementById('DateTimeBlockedChart').getContext('2d');
var DateTimeBlockedChartData = {
  datasets: [
    {
      label: '<?= __('Accepted domains') ?>',
	  fill: true,
      //fillOpacity: 0.4,
      borderColor: "#00a65a",
      //backgroundColor: "#00a65a",
      backgroundColor: "rgba(0, 166, 90, 0.7)",
      borderColor: "#006638",
	  pointRadius: 4,
      pointBackgroundColor: "#006638",
      pointBorderColor: "#006638",
      //pointHoverBackgroundColor: "#00a65a",
      //pointHoverBorderColor: "#00a65a",
      data: [
		<?php foreach ($datetime_blocked_data as $data): ?>
          <?= $data['accepted'] ?>,
		<?php endforeach ?>
      ],
    },
    {
      label: '<?= __('Blocked domains')?>',
	  fill: true,
      //fillOpacity: 0.4,
      borderColor: "#f56954",
      //backgroundColor: "#f56954",
      backgroundColor: "rgba(245, 105, 84, 0.7)",
	  pointRadius: 4,
      borderColor: "#f24126",
      pointBackgroundColor: "#f24126",
      pointBorderColor: "#f24126",
      //pointHoverBackgroundColor: "#f56954",
      //pointHoverBorderColor: "#f56954",
      data: [
		<?php foreach ($datetime_blocked_data as $data): ?>
          <?= $data['blocked'] ?>,
		<?php endforeach ?>
      ],
    },
  ],

  labels: [
    <?php foreach ($datetime_blocked_data as $key => $data): ?>
      '<?= $key ?>',
    <?php endforeach ?>
  ]
};
var myDateTimeBlockedChart = new Chart(DateTimeBlockedChartCtx, {
    type: 'line',
    data: DateTimeBlockedChartData,
    options: DateTimeBlockedChartOptions
});
</script>

<script>
//------------------------
//- DATE TIME CLIENT BAR -
//------------------------
DateTimeClientsChartOptions = {
  scales: {
    xAxes: [{
      stacked: true,
    }],
    yAxes: [{
      stacked: true,
    }]
  },
  legend: {
    position: 'bottom',
	display: true,
  }
}
var DateTimeClientsChartCtx = document.getElementById('DateTimeClientsChart').getContext('2d');
var DateTimeClientsChartData = {
  datasets: [
  <?php foreach ($datetime_clients_data as $client_ip => $data): ?>
    {
      label: '<?= $data['legend'] ?>',
      data: [
		<?php foreach($data['times'] as $hits): ?>
          <?= $hits ?>,
		<?php endforeach ?>
      ],

      backgroundColor: [
		<?php foreach($data['times'] as $hits): ?>
          '<?= $data['color'] ?>',
		<?php endforeach ?>
      ]
    },
  <?php endforeach ?>
  ],

  labels: [
  <?php foreach($datetime_clients_labels as $label): ?>
    '<?= $label ?>',
  <?php endforeach ?>
  ]
};
var myDateTimeClientsChart = new Chart(DateTimeClientsChartCtx, {
    type: 'bar',
    data: DateTimeClientsChartData,
    options: DateTimeClientsChartOptions
});
</script>

<script>
//-------------------------
//- DATE TIME DOMAINS BAR -
//-------------------------
DateTimeDomainsChartOptions = {
  scales: {
    xAxes: [{
      stacked: true,
    }],
    yAxes: [{
      stacked: true,
    }]
  },
  legend: {
    position: 'bottom',
	display: true,
  },
}
var DateTimeDomainsChartCtx = document.getElementById('DateTimeDomainsChart').getContext('2d');
var DateTimeDomainsChartData = {
  datasets: [
  <?php foreach ($datetime_domains_data as $domain => $data): ?>
    {
      label: '<?= $domain ?>',
      data: [
		<?php foreach($data['times'] as $hits): ?>
          <?= $hits ?>,
		<?php endforeach ?>
      ],

      backgroundColor: [
		<?php foreach($data['times'] as $hits): ?>
          '<?= $data['color'] ?>',
		<?php endforeach ?>
      ]
    },
  <?php endforeach ?>
  ],

  labels: [
  <?php foreach($datetime_domains_labels as $label): ?>
    '<?= $label ?>',
  <?php endforeach ?>
  ]
};
var myDateTimeDomainsChart = new Chart(DateTimeDomainsChartCtx, {
    type: 'bar',
    data: DateTimeDomainsChartData,
    options: DateTimeDomainsChartOptions
});
</script>
