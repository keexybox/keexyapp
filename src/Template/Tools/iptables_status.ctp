<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Iptables filter status')?></h3>
      </div>

      <div class="box-body">

        <?php 
			echo "<pre>".$filter_status."</pre>";
        ?>

      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->

    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Iptables NAT status')?></h3>
      </div>

      <div class="box-body">
        <?php 
			echo "<pre>".$nat_status."</pre>";
        ?>

      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
