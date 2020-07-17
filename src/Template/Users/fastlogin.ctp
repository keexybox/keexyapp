  <div class="login-box">
    <div class="box-header with-border">
      <h3 class="box-title login-page"><?= __('Connect to the Internet')?></h3>
    </div>

	<?= $this->Form->create('login', [ 'class' => 'form-signin'] ) ?>

    <div class="box-body">
  
      <input type="hidden" id="client_details" name="client_details" value="">

      <div class="col-xs-12 col-sm-12">
	      <?= $this->Form->button(__('Accept & Connect'), [
		    	'class' => 'btn btn-primary btn-block'
		      ])
	      ?>
      </div>
      <!-- /.col -->

	<?= $this->Form->end() ?>

  </div>

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

// Convert Object to JSON string
var client_details = JSON.stringify(ua);

// Set the value in the form
document.getElementById("client_details").value = client_details;
</script>
