<!-- Main content -->
<div class="row">
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('KeexyBox update')?></h3>
      </div>






      <?php if ( $step == 1 ): ?>
      <!-- Content displayed for update step 1 -->
      <div class="box-body">
        <div class="row">
          <div class="col-sm-12">
            <?php if (isset ($update_data)): ?>
              <div class="alert alert-success">
                <h4><i class="icon fa fa-info"></i><?= __('A new version is available.') ?></h4>
              </div>
              <h4><?= __('Information about the new version') ?><h4>
              <ul>
                <?php if(isset($update_data->version)): ?><li><?= __('New version').": ".$update_data->version ?></li><?php endif ?>
                <?php if(isset($update_data->changelog)): ?><li><?= $this->Html->link(__('ChangeLog'), $update_data->changelog, array('target'=>'_blank','escape'=>false)) ?></li><?php endif ?>
                <?php if(isset($update_data->documentation)): ?><li><?= $this->Html->link(__('Documentation'), $update_data->documentation, array('target'=>'_blank','escape'=>false)) ?></li><?php endif ?>
              </ul>
            <?php else: ?>
                <div class="alert alert-info">
                  <h4><i class="icon fa fa-info"></i><?= __('No update is available.') ?></h4>
                </div>
            <?php endif ?>
          </div>
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
            <?php if (isset ($update_data)): ?>
            <center>
            <a href="?step=2&download=<?= $update_data->download ?>" class="btn btn-success">
              <i class="fa fa-cloud-download"></i>&nbsp;<?= __('Install the update') ?>
            </a>
            </center>
            <?php endif ?>
      </div><!-- /.box-footer -->

      <?php endif ?>

      <?php if ( $step == 2 ): ?>

      <!-- Content displayed for update step 2 -->
      <div class="box-body">
        <div class="row">
          <div class="col-sm-12">
              <div class="alert alert-success">
                <h4><i class="icon fa fa-info"></i><?= __('Please wait while updating...') ?></h4>
              </div>
          </div>
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->

      <script>
        const urlParams = new URLSearchParams(window.location.search);
        const download_param = urlParams.get('download');
        window.onload = function() {
          window.location.replace("?step=3&download=" + download_param);
        }
      </script>
      <?php endif ?>

      <?php if ( $step == 3 ): ?>

      <!-- Content displayed for update step 2 -->
      <div class="box-body">
        <div class="row">
          <div class="col-sm-12">
              <div class="alert alert-success">
                <h4><i class="icon fa fa-info"></i><?= __('Update done.') ?></h4>
              </div>
          </div>
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
      <?php endif ?>


    </div><!-- /.box -->
  </div><!-- /.col -->
</div>

