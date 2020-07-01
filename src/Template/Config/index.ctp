<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Control panel')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
         <legend><?= __('Connection settings')?></legend>
         <a href="/blacklist" class="btn btn-app">
              <i class="fa fa-minus-circle"></i><?= __('Blacklist') ?>
         </a>
         <a href="/profiles" class="btn btn-app">
              <i class="fa fa-sliders"></i><?= __('Profiles') ?>
         </a>
         <a href="/users" class="btn btn-app">
              <i class="fa fa-user"></i><?= __('Users') ?>
         </a>
         <a href="/devices" class="btn btn-app">
              <i class="fa fa-mobile"></i><?= __('Devices') ?>
         </a>
         <legend><?= __('System settings')?></legend>
         <a href="/config/network" class="btn btn-app">
              <i class="fa fa-laptop"></i><?= __('Network') ?>
         </a>
         <a href="/config/datetime" class="btn btn-app">
              <i class="fa fa-clock-o"></i><?= __('Date and time') ?>
         </a>
         <a href="/config/dhcp" class="btn btn-app">
              <i class="fa fa-server"></i><?= __('DHCP') ?>
         </a>
         <a href="/config/wifiap" class="btn btn-app">
              <i class="fa fa-wifi"></i><?= __('Access Point') ?>
         </a>
         <a href="/config/certificate" class="btn btn-app">
              <i class="fa fa-certificate"></i><?= __('SSL certificate') ?>
         </a>
         <a href="/config/misc" class="btn btn-app">
              <i class="fa fa-cog"></i><?= __('Miscellaneous') ?>
         </a>
         <legend><?= __('Tools & diagnostics')?></legend>
         <a href="/tools/services" class="btn btn-app">
              <i class="fa fa-power-off"></i><?= __('Services and power') ?>
         </a>
         <a href="/tools/domain-issue" class="btn btn-app">
              <i class="fa fa-check-circle-o"></i><?= __('Domain check') ?>
         </a>
         <a href="/tools/system-state" class="btn btn-app">
              <i class="fa fa-heartbeat"></i><?= __('System state') ?>
         </a>
         <a href="/tools/iptables-status" class="btn btn-app">
              <i class="fa fa-exchange"></i><?= __('Iptables status') ?>
         </a>
	  <!--  BODY -->
      </div>
      <!-- /.box-body -->

      <div class="box-footer">

      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
