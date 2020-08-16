<div class="login-box">
  <div class="box-header with-border">
    <h3 class="box-title login-page"><?= __('Connect to the Internet')?></h3>
  </div>

	<?= $this->Form->create('login', [ 'class' => 'form-signin'] ) ?>

  <div class="box-body">

    <div class="col-xs-12 col-sm-12" name="terms_text" style="overflow-y: scroll; height:400px;">
      <?= $cportal_terms ?>
    </div>

    <label>
      <!-- this button is enabled if terms_text fully scrolled -->
      <?= $this->Form->control('accept_checkbox', [
         'type' => 'checkbox',
         'id' => 'accept_checkbox',
         'label' => __("I accept terms and conditions"),
         'disabled' => 'disabled',
         'onchange' => 'document.getElementById("connect_button").disabled = !this.checked;'
         ])
      ?>
    </label>

    <div class="col-xs-12 col-sm-12">
	  <?= $this->Form->button(__('Connect'), [
         'class' => 'btn btn-primary btn-block',
         'id' => 'connect_button',
         'disabled' => 'disabled'
         ])
	  ?>
    </div>
    <div class="col-xs-12 col-sm-12">
	  <?= $this->Html->link(
            __('Or connect with an account'),
            '/users/login',
          )
	  ?>
    </div>

    <input type="hidden" id="client_details" name="client_details" value="">
    <!-- /.col -->

	<?= $this->Form->end() ?>

</div>

<!-- JS to get UserAgent -->
<script>
  // instance of UserAgent Parser
  var parser = new UAParser();
  // Get UserAgent
  var ua = parser.getResult();
  
  // get screen info
  var ratio = window.devicePixelRatio || 1;
  var width = window.screen.width * ratio;
  var height = window.screen.height * ratio;

  // Add screen info to UA info
  ua.screen = {'ratio': ratio, 'width': width, 'height': height};
  ua.lang = {'browser': navigator.language};
  
  // Convert Object to JSON string
  var client_details = JSON.stringify(ua);
  
  // Set the value in the form
  document.getElementById("client_details").value = client_details;
</script>

<!-- JS To enable Accept terms and conditions checkbox -->
<script>
  // Get terms text Element
  var textElement = document.getElementsByName("terms_text")[0];
  // Add Event Listener and run function checkScrollHeight
  textElement.addEventListener("scroll", checkScrollHeight, false);

  // function checkScrollHeight, enable checkbox when text fully scrolled
  function checkScrollHeight(){
    if ((textElement.scrollTop + textElement.offsetHeight) >= textElement.scrollHeight){
        document.getElementById("accept_checkbox").disabled = false;
    }
  }
</script>
