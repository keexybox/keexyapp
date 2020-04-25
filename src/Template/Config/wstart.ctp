<legend><?= __('KeexyBox’s configuration wizard') ?></legend>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <h4><?= __('Congratulations! KeexyBox is installed.')?></h4>
      <p><?= __('We will now assist you in setting up your KeexyBox.') ?></p>
	  <?= __('How would you like to use it?') ?>
	  <div class="radio">
        <label><input onclick="set_install_type()" class="radio" type="radio" name="install_type" value="1"><?= __('For Internet filtering and anonymous access?') ?></input></label>
      </div>
	  <div class="radio">
        <label><input onclick="set_install_type()" class="radio" type="radio" name="install_type" value="2"><?= __('For Internet filtering only and DHCP enabled on KeexyBox?') ?></input></label>
      </div>
	  <div class="radio">
        <label><input onclick="set_install_type()" class="radio" type="radio" name="install_type" value="3"><?= __('For Internet filtering only and DHCP disabled on KeexyBox?') ?></input></label>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
	<a onclick="install_redirect()" class="btn btn-success pull-right float-vertical-align"><?= __('Start') ?>&nbsp;<span class="glyphicon glyphicon-chevron-right" aria-hidden="true" title="<?= __('Start') ?>"></span></a>
  </div>
</div>

<script> 
var install_type;
function set_install_type() { 
  var ele = document.getElementsByName('install_type'); 
             
  for(i = 0; i < ele.length; i++) { 
    if(ele[i].checked) 
    install_type = ele[i].value; 
  } 
} 

function install_redirect() {
  if (typeof install_type !== 'undefined') {
    window.location.href = "/help/wlicenses?install_type=" + install_type;
  } else {
	alert("<?= __('You must choose how you want to use KeexyBox.') ?>");
  }  
}
</script> 
