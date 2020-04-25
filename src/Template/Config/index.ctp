<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('System control panel')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('config') ?>
      <div class="box-body">
         <a href="/config/network" class="btn btn-app">
              <i class="fa fa-laptop"></i><?= __('Network') ?>
         </a>
         <a href="/config/datetime" class="btn btn-app">
              <i class="fa fa-clock-o"></i><?= __('Date and time') ?>
         </a>
         <a href="/config/dhcp" class="btn btn-app">
              <i class="fa fa-server"></i><?= __('DHCP') ?>
         </a>
         <a href="/config/certificate" class="btn btn-app">
              <i class="fa fa-certificate"></i><?= __('SSL certificate') ?>
         </a>
         <a href="/config/misc" class="btn btn-app">
              <i class="fa fa-cog"></i><?= __('Miscellaneous') ?>
         </a>
	  <!--  BODY -->

      </div>
      <!-- /.box-body -->

      <div class="box-footer">

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
