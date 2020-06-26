<?= $this->Flash->render('reconnect') ?>
<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"><?= __('Users')?></h3>
	<hr>
    <div class="row bottom-buffer">
      <!-- actions -->
      <div class="col-sm-2 col-xs-12">
        <?= $this->Html->link(
             $this->Html->tag('span', "", [
               'class' => "glyphicon glyphicon-plus",
               'aria-hidden' => "true",
               'title' => __("Add a new user"),
             ]),
             ['controller' => 'users', 'action' => 'add'],
             ['escape' => false])
        ?>
        <?= $this->Html->link(
             $this->Html->tag('span', "", [
               'class' => "glyphicon glyphicon-export",
                'aria-hidden' => "true",
                'title' => __("Export users to CSV"),
              ]),
              ['action' => 'export'],
              ['escape' => false])
        ?>
        <?= $this->Html->link(
             $this->Html->tag('span', "", [
               'class' => "glyphicon glyphicon-import",
               'aria-hidden' => "true",
               'title' => __("Import users from CSV"),
             ]),
             ['action' => 'import'],
             ['escape' => false])
        ?>
      </div>
      <!-- actions -->
      <!-- search -->
      <?= $this->element('search_general_form') ?>
    </div><!-- /.row -->
  </div>
  <!-- /.box-header -->

    <?= $this->Form->create('action', [ 
        'class' => 'form-horizontal',
        'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
      ])
    ?>
  <div class="box-body">
  
    <table class="table table-bordered table-striped" id="scroll_table">
      <thead>
        <tr>
          <th><input type="checkbox" id="select_all"/></th>
          <th><?= $this->Paginator->sort('displayname', __('Display name')) ?></th>
          <th><?= $this->Paginator->sort('username', __('Login')) ?></th>
          <th><?= $this->Paginator->sort('profile_id', __('Profile')) ?></th>
          <th><?= $this->Paginator->sort('email', __('Email')) ?></th>
          <th><?= $this->Paginator->sort('enabled', __('Enabled')) ?></th>
          <th><?= $this->Paginator->sort('admin', __('Admin')) ?></th>
          <th><?= $this->Paginator->sort('expiration', __('Expiration')) ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      
	  <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
          <td>
            <?php if($user->id != 1): ?>
              <input class="checkbox" type="checkbox" name="check[]" value="<?= $user->id ?>">
            <?php endif ?>
          </td>
          <td><?= h($user->displayname) ?></td>
          <td>
            <?= $this->Html->link($user->username, ['action' => 'edit', $user->id]) ?>
          </td>
          <td>
            <?= $this->Html->link($user->profile->profilename, ['controller' => 'profiles', 'action' => 'edit', $user->profile->id]) ?>
          </td>
          <td>
            <?= $this->Html->link($user->email, 'mailto:'.$user->email) ?>
          </td>
          <td>
            <?= h($user->enabled) ? 
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("Yes"),
                ]) : 
                $this->Html->tag('span', "", [
                  'class' => "none",
                  'aria-hidden' => "true",
                  'title' => __("No"),
                ])
            ?>
          </td>
        
          <td>
            <?= h($user->admin) ? 
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("Yes"),
              ]) : 
              $this->Html->tag('span', "", [
                  'class' => "none",
                  'aria-hidden' => "true",
                  'title' => __("No"),
              ])
            ?>
          </td>
          <td>
            <?php if ( $user->expiration != null ): ?>
                <?= $user->expiration->timezone($timezone)->format('Y-m-d H:i:s') ?>
            <?php endif ?>
          </td>
          <td>
            <?= $this->Html->link(
                $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-edit",
                'aria-hidden' => "true",
                'title' => __("Edit"),
                  ]),
              ['controller' => 'users', 'action' => 'edit', $user->id],
              ['escape' => false]
            )
            ?>
            <?php
              if($user->id != 1 ) {
              echo $this->Form->postLink(
                $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-trash",
                'aria-hidden' => "true",
                'title' => __("Delete"),
                  ]),
              ['action' => 'delete', $user->id],
              ['escape' => false, 'block' => true, 'confirm' => __('Are you sure you want to delete {0}?', h($user->username))]
              );
            }
            ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div><!-- /.box-body -->

  <div class="box-footer">
  
    <div class="row">
      <div class="col-sm-2 col-xs-12">
          <?= $this->Form->control('action', [
              'type' => 'select',
              'label' => false,
              'options' => [
                'disable' => __('Disable') ,
                'enable' => __('Enable'), 
                'setprofile' => __('Change the profile'), 
                'delete' => __('Delete')
                ],
              'empty' => __('(select action)'),
              'id' => "action",
              'class' => "form-control",
              'required' => "required",
            ]);
          ?>
      </div>
      <div id="setprofile" class="dynform" style="display:none">
        <div class="col-sm-2 col-xs-12" name="select-profile">
          <?= $this->Form->control('profile_id', [
              'type' => 'select',
              'label' => false,
              'options' => $profiles,
              'empty' => __('(select a profile)'),
              'id' => "inputProfile",
              'class' => "form-control",
            ]);
          ?>
        </div>
      </div>
      <div class="col-sm-1 col-xs-12">
        <?= $this->Form->button(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-ok",
              'aria-hidden' => "true",
              'title' => __("Run"),
              ])."&nbsp;".__('Run'),
            [ 'class' => "btn btn-default pull-right", 'escape' => false]) 
        ?>
      </div>
    </div>
  
    <?= $this->element('paginator') ?>
  
  </div><!-- /.box-footer -->
  <?= $this->Form->end() ?>
  <?= $this->fetch('postLink') ?>
</div><!-- /.box -->

<script src="/js/selectall.js"></script>
<script>
  $(document).ready(function() {
    $('select#action').change(function() {
      $('.dynform').hide();
      $('#' + $(this).val()).show();
    });
	$('#' + $("select#action").val()).show();
  });
</script>

<script>
$(document).ready(function() {
  $('#scroll_table').DataTable( {
    "scrollX": true,
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false,
    "searching": false,
  } );
});
</script>
