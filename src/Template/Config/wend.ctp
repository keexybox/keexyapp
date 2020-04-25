<legend><?= __('KeexyBox’s configuration wizard') ?></legend>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <h4><?= __('Your KeexyBox setup is complete!')?></h4>
      <p><?= __('You will be able to connect to KeexyBox using one of the URLs below:') ?></p>
	  <ul>
	    <?php foreach ($kxb_urls as $kxb_url): ?>
		  <?= "<b><li><a href='".$kxb_url."'>".h($kxb_url)."</a></li></b>" ?>
	    <?php endforeach ?>
	  </ul>
      <p><?= __('Please note the URLs and click {0} to restart the system.', '<b><i>"'.__("Finish").'"</i></b>') ?></p>

    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <?= $this->Form->create('config') ?>
    <a onclick="back_link()" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" title="<?= __('Back') ?>"></span>&nbsp;<?= __('Back') ?></a>
	<button class="btn btn-success pull-right float-vertical-align" type="submit"><?= __('Finish') ?>&nbsp;<span class="glyphicon glyphicon-chevron-right" aria-hidden="true" title="<?= __('Finish') ?>"></span></button>
    <?= $this->Form->end() ?>
  </div>
</div>
<script>
var urlParams = new URLSearchParams(location.search);
var install_type = urlParams.get('install_type');
function back_link() {
  window.location.href = "/devices/wscan?install_type=" + install_type;
}
</script>
