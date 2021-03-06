  <div class="login-box">
    <div class="box-header with-border">
      <h3 class="box-title login-page"><?= __('Manage KeexyBox')?></h3>
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
	  </div>
        <!-- /.col -->
      <div class="col-xs-6 col-sm-4">
	      <?= $this->Form->button(__('Sign in'), [
		    	'class' => 'btn btn-primary btn-block'
		      ])
	      ?>
      </div>
      <!-- /.col -->

	<?= $this->Form->end() ?>

  </div>
