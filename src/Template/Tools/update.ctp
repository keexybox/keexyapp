<!-- Main content -->
<div class="row">

  <!-- Memory BOX -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('KeexyBox update')?></h3>
      </div>

      <div class="box-body">
        <div class="row">
          <div class="col-sm-12">
            <?php if (isset ($update_data)): ?>
              <div class="alert alert-info">
                <h4><i class="icon fa fa-info"></i><?= __('A new version is available.') ?></h4>
              </div>
              <h4><?= __('Information about the new version') ?><h4>
              <ul>
                <?php if(isset($update_data->version)): ?><li><?= __('New version').": ".$update_data->version ?></li><?php endif ?>
                <?php if(isset($update_data->changelog)): ?><li><?= $this->Html->link(__('ChangeLog'), $update_data->changelog, array('target'=>'_blank','escape'=>false)) ?></li><?php endif ?>
                <?php if(isset($update_data->documentation)): ?><li><?= $this->Html->link(__('Documentation'), $update_data->documentation, array('target'=>'_blank','escape'=>false)) ?></li><?php endif ?>
              </ul>
            <?php else: ?>
                <div class="alert alert-success">
                  <h4><i class="icon fa fa-info"></i><?= __('KeexyBox is up to date.') ?></h4>
                </div>
            <?php endif ?>
          </div>
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
            <?php if (isset ($update_data)): ?>
            <center>
            <a href="?update_keexybox=1" class="btn btn-success">
              <i class="fa fa-cloud-download"></i>&nbsp;<?= __('Install the update') ?>
            </a>
            </center>
            <?php endif ?>
      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->

