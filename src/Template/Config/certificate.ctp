<?= $this->Flash->render('gen_certificate') ?>
<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Generate SSL certificate')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('config', [ 
        'onsubmit' => "return confirm(\"".__('Certificate will be replaced. Confirm?')."\");"
        ]) 
      ?>
      <div class="box-body">
	    
        <div class="form-group">
          <label for="inputssl_csr_cn"><?= __('Common Name') ?></label>
          <?= $this->Form->control('ssl_csr_cn',[
              'label' => false,
              'default' => $ssl_csr_cn->value,
              'id' => "inputssl_csr_c",
              'placeholder' => __("(fqdn or hostname)"),
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_ssl_csr_cn')?>
		</div>
  
        <div class="form-group">
          <label for="inputssl_csr_c"><?= __('Country code') ?></label>
          <?= $this->Form->control('ssl_csr_c',[
              'label' => false,
              'default' => $ssl_csr_c->value,
              'id' => "inputssl_csr_c",
              'minlength' => '2',
              'maxlength' => '2',
              'placeholder' => __("(2 letter code)"),
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_ssl_csr_c')?>
		</div>
  
        <div class="form-group">
          <label for="inputssl_csr_st"><?= __('State or Province Name') ?></label>
          <?= $this->Form->control('ssl_csr_st',[
              'label' => false,
              'default' => $ssl_csr_st->value,
              'id' => "inputssl_csr_st",
              'placeholder' => __("(full name)"),
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_ssl_csr_st')?>
		</div>
  
        <div class="form-group">
          <label for="inputssl_csr_l"><?= __('Locality Name') ?></label>
          <?= $this->Form->control('ssl_csr_l',[
              'label' => false,
              'default' => $ssl_csr_l->value,
              'id' => "inputssl_csr_l",
              'placeholder' => __("(e.g. city)"),
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_ssl_csr_l')?>
		</div>
  
        <div class="form-group">
          <label for="inputssl_csr_o"><?= __('Organization Name') ?></label>
          <?= $this->Form->control('ssl_csr_o',[
              'label' => false,
              'default' => $ssl_csr_o->value,
              'id' => "inputssl_csr_o",
              'placeholder' => __("(e.g. company)"),
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_ssl_csr_o')?>
		</div>
  
        <div class="form-group">
          <label for="inputssl_csr_ou"><?= __('Organizational Unit Name') ?></label>
          <?= $this->Form->control('ssl_csr_ou',[
              'label' => false,
              'default' => $ssl_csr_ou->value,
              'id' => "inputssl_csr_ou",
              'placeholder' => __("(e.g. department)"),
              'class' => "form-control",
            ])
          ?>
		  <?= $this->Flash->render('error_ssl_csr_ou')?>
		</div>

      </div>
      <!-- /.box-body -->

      <div class="box-footer">

        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-remove-sign",
              'aria-hidden' => "true",
              'title' => __("Cancel"),
              ])."&nbsp;".__('Cancel'),
            ['action' => 'index'], 
            [ 'class' => "btn btn-default", 'escape' => false]) 
        ?>
  
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-save",
              'aria-hidden' => "true",
              'title' => __("Save"),
              ])."&nbsp;".__('Save'),
            [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>

      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
