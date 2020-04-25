<!-- Main content -->
<div class="row">

  <!-- Memory BOX -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Memory')?></h3>
      </div>

      <div class="box-body">
	    <canvas id="MemChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->

  <!-- Swap BOX -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Swap')?></h3>
      </div>

      <div class="box-body">
	    <canvas id="SwapChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div>

  <!-- Load BOX -->
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Load average')?></h3>
      </div>

      <div class="box-body">
		<canvas id="LoadChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->

  <!-- Disk usage BOX -->
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Disk usage')?></h3>
      </div>

      <div class="box-body">
		<canvas id="DiskChart"></canvas>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->

<script>
//---------------------
//- PIE CHART OPTIONS -
//---------------------
MemSwapOptions = {
  legend: {
    position: 'right',
  }
}

//-------------
//- MEM CHART -
//-------------
var MemCtx = document.getElementById('MemChart').getContext('2d');
var MemData = {
  datasets: [{
    data: [
      <?= $meminfo['MemUsed'] ?>, 
      <?= $meminfo['Cached'] ?>,
      <?= $meminfo['Buffers'] ?>,
      <?= $meminfo['MemFree'] ?>,
    ],
    backgroundColor: [
      '#f56954',
      '#f39c12',
      '#00c0ef',
      '#00a65a'
    ]
  }],

  // These labels appear in the legend and in the tooltips when hovering different arcs
  labels: [
    'Used',
    'Cached',
    'Buffers',
    'Free'
  ]
};
var myDoughnutChart = new Chart(MemCtx, {
    type: 'doughnut',
    data: MemData,
    options: MemSwapOptions
});

//--------------
//- SWAP CHART -
//--------------
var SwapCtx = document.getElementById('SwapChart').getContext('2d');
var SwapData = {
  datasets: [{
    data: [
      <?= $meminfo['SwapUsed'] ?>, 
      <?= $meminfo['SwapFree'] ?>,
    ],
    backgroundColor: [
      '#f56954',
      '#00a65a'
    ]
  }],

  // These labels appear in the legend and in the tooltips when hovering different arcs
  labels: [
    'Used',
    'Free'
  ]
};
var myDoughnutChart = new Chart(SwapCtx, {
    type: 'doughnut',
    data: SwapData,
    options: MemSwapOptions
});

//--------------
//- LOAD CHART -
//--------------
LoadOptions = {
  scales: {
    yAxes: [{
      ticks: {
       suggestedMin: 0,
       suggestedMax: <?= $loadinfo['nbcpu']?> 
       }
     }]
  }
}
var LoadCtx = document.getElementById('LoadChart').getContext('2d');
var LoadData = {
  datasets: [{
    label: 'Load',
    data: [
      <?= $loadinfo['01min']?>, 
      <?= $loadinfo['05min']?>, 
      <?= $loadinfo['15min']?>, 
    ],
    backgroundColor: [
      '#00c0ef',
      '#00c0ef',
      '#00c0ef',
    ]
  }],

  // These labels appear in the legend and in the tooltips when hovering different arcs
  labels: [
    '1 min',
    '5 min',
    '15 min',
  ]
};
var myDoughnutChart = new Chart(LoadCtx, {
    type: 'bar',
    data: LoadData,
    options: LoadOptions
});

//--------------
//- DISK CHART -
//--------------
DiskOptions = {
  scales: {
    xAxes: [{
      ticks: {
        suggestedMin: 0,
        suggestedMax: 100,
      },
      stacked: true,
    }],
    yAxes: [{
      stacked: true,
    }]
  }
}
var DiskCtx = document.getElementById('DiskChart').getContext('2d');
var DiskData = {
  datasets: [
    {
      label: '% used',
      data: [
		<?php foreach ($diskinfo as $info): ?>
        	<?= $info['percent_used'] ?>,
		<?php endforeach ?>
      ],

      backgroundColor: [
		<?php foreach ($diskinfo as $info): ?>
        	'#f56954',
		<?php endforeach ?>
      ]
    },
    {
      label: '% free',
      data: [
		<?php foreach ($diskinfo as $info): ?>
        	<?= $info['percent_free'] ?>,
		<?php endforeach ?>
      ],

      backgroundColor: [
		<?php foreach ($diskinfo as $info): ?>
        	'#00a65a',
		<?php endforeach ?>
      ]
    }
  ],

  // These labels appear in the legend and in the tooltips when hovering different arcs
  labels: [
    <?php foreach ($diskinfo as $info): ?>
      '<?= $info['mount']." : ".$info['used']."MB"."/".$info['total']."MB" ?>',
    <?php endforeach ?>
  ]
};
var myDoughnutChart = new Chart(DiskCtx, {
    type: 'horizontalBar',
    data: DiskData,
    options: DiskOptions
});
</script>
