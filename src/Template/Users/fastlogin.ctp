  <div class="login-box">
    <div class="box-header with-border">
      <h3 class="box-title login-page"><?= __('Connect to the Internet')?></h3>
    </div>

	<?= $this->Form->create('login', [ 'class' => 'form-signin'] ) ?>

      <div class="box-body">
  
      <div class="col-xs-12 col-sm-12">
	      <?= $this->Form->button(__('Accept & Connect'), [
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
