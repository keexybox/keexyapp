  <div class="login-box">
    <div class="box-header with-border">
      <h3 class="box-title login-page"><?= __('Connect to the Internet')?></h3>
    </div>

	<?= $this->Form->create('login', [ 'class' => 'form-signin'] ) ?>

      <div class="box-body">
        <div class="form-group has-feedback">
          <?= $this->Form->control('username', [
  			'class' => "form-control",
  			'placeholder' => __('username'),
  			'label' => false
  		]) 
  	    ?>
          <span class="glyphicon glyphicon-user form-control-feedback login-form-logo"></span>
        </div>
  
        <div class="form-group has-feedback">
  	    <?= $this->Form->control('password', [
  			'type' => 'password',
  			'class' => "form-control",
  			'placeholder' => __('password'),
  			'label' => false
  		    ])
  	    ?>
          <span class="glyphicon glyphicon-lock form-control-feedback login-form-logo"></span>
        </div>
  
        <div class="form-group has-feedback">
  	    <?= $this->Form->control('sessiontime', [
  			'type' => 'select',
  			'label' => __('Connection duration'),
  			'default' => $connection_default_time['value'] / 60,
  			'options' => $duration_list,
  			'class' => "form-control"
  		]) ?>
        </div>
	  </div>
      <input type="hidden" id="client_details" name="client_details" value="">
        <!-- /.col -->
      <div class="col-xs-6 col-sm-4">
	      <?= $this->Form->button(__('Connect'), [
		    	'class' => 'btn btn-primary btn-block'
		      ])
	      ?>
      </div>
      <?php if ($cportal_register_allowed == 1): ?>
      <div class="col-xs-6 col-sm-8">
	      <?= $this->Html->link(
                __('Register'),
                '/users/register',
                ['class' => 'btn'])
	      ?>
      </div>
      <?php endif ?>
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
