<!-- Main content -->
<legend><?= __('KeexyBox’s configuration wizard').': '.__('Add domains to the Blacklist') ?></legend>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <p><?= __("The Blacklist is KeexyBox’s internal database containing the domains to block. By default the Blacklist is empty, KeexyBox is not provided with any list of domains. Here you can add domains to block or import lists that you can retrieve from the Internet.") ?></p>
	  <p><?= __("Click {0} if you do not want to do it now.", '<b><i>"'.__("Skip / Next").'"</i></b>') ?></p>
    </div>
  </div>
</div>
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->

    <div class="box box-info">
      <div class="box-header with-border">
	    <h3 class="box-title"><?= __('Import domains to the Blacklist from a file')?></h3>
      </div>
      <div class="box-body">
        <div class="form-group col-sm-12">
          <label for="importDomains" class="control-label"><?= __('To import domains from tar.gz file, please click on {0}.', __('Import'))?></label>
		  <p><?= __('Domains will be automatically categorized.') ?></p>
        </div>
	  </div>
      <div class="box-footer">
        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-import",
              'aria-hidden' => "true",
              'title' => __("Import"),
              ])."&nbsp;".__('Import'),
            //['controller' => 'ProfilesRouting', 'action' => 'index', $profile->id], 
            '#',
            [ 'class' => "btn btn-info pull-right", 'escape' => false, 'onclick' => "open_window_f('/blacklist/import')"]) 
        ?>
	  </div>
	</div>

    <div class="box box-info">
      <div class="box-header with-border">
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('BL') ?>
      <div class="box-body">


        <div class="form-group">
          <label for="inputDomain" class="col-sm-12 control-label"><?= __('Here you can add domains to the Blacklist line per line or separated by a space')?></label>
          <?= $this->Form->control('domains', [
                'type' => 'textarea',
                'label' => false,
                'class' => "form-control",
                'id' => "inputProfilename",
                'value' => $prefillurls,
                'placeholder' => "ads.domain.com bad.website.com",
                //'required' => 'required',
              ]);
          ?>
        </div>

        <div class="checkbox">
          <label>
            <?= $this->Form->control('replace', [
                'type' => 'checkbox',
                'label' => " ".__('Overwrite the category of existing domains in the Blacklist'),
				'checked' => false,
              ])
            ?>
          </label>
        </div>

        <div class="form-group">
          <label for="inputProfilename" class="col-sm-12 control-label"><?= __('You can here import domains to the Blacklist from lists available on the Internet (one URL per line)')?></label>
          <?= $this->Form->control('weblist', [
                'type' => 'textarea',
                'label' => false,
                'class' => "form-control",
                'id' => "inputProfilename",
                //'value' => $prefillurls,
                'placeholder' => "https://www.blacklist-provider.com/list.txt",
                //'required' => 'required',
              ]);
          ?>
        </div>

        <div class="form-group">
          <label for="inputCategory"><?= __('Category')?></label>
          <?= $this->Form->control('category', [
                'type' => 'select',
                'label' => false,
                'class' => "form-control select2",
                'options' => $categories,
                'empty' => __('(Select existing category)'),
                'id' => "inputCategory",
                'value' => $BL['category']
              ]);
          ?>
        <div class="form-group">
        </div>
          <label for="inputCategoryNew"><?= __('Or create new category')?></label>
          <?= $this->Form->control('newcategory', [
                'label' => false,
                'class' => "form-control",
                'empty' => '()',
                'id' => "inputCategoryNew",
                'placeholder' => __('Category name'),
                'value' => $BL['newcategory']
              ]);
          ?>
        </div>

	  <!--  BODY -->

      </div>
      <!-- /.box-body -->

      <div class="box-footer">

        <a onclick="back_link()" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" title="<?= __('Back') ?>"></span>&nbsp;<?= __('Back') ?></a>
		<a onclick="skip_link()" class="btn btn-success pull-right float-vertical-align"><?= __('Skip / Next') ?>&nbsp;<span class="glyphicon glyphicon-chevron-right" aria-hidden="true" title="<?= __('Skip / Next') ?>"></span></a>
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-save",
              'aria-hidden' => "true",
              'title' => __("Save"),
              ])."&nbsp;".__('Save'),
            [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]
          ) 
        ?>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->

  </div><!-- /.col -->
</div><!-- /.row -->
<script>
  $(function () {
		      //Initialize Select2 Elements
		      $('.select2').select2()
  })
</script>
<!-- Script to open domains routing or firewall page in a new window  -->
<script>
function open_window_f() {
        window.open(arguments[0], "_blank", "location=no,status=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,top=500,left=500,width=600,height=600");
}

var urlParams = new URLSearchParams(location.search);
var install_type = urlParams.get('install_type');
function back_link() {
  window.location.href = "/config/wdhcp?install_type=" + install_type;
}

function skip_link() {
  window.location.href = "/profiles/wadd?install_type=" + install_type;
}

</script>
