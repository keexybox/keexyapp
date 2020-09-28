<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Advanced settings and configuration management')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <?= $this->Form->create('action', [ 
                  'onsubmit' => "return confirm(\"".__('Do you confirm the action?')."\");"
              ])
      ?>
      <div class="box-body">
        <legend><?= __('Generate configuration files')?></legend>
        <div class="row">
          <div class="col-lg-3">
              <?= $this->Form->control('config', [
                  'type' => 'select',
                  'label' => false,
                  'options' => [
                      'apache' => 'apache',
                      'bind' => 'bind',
                      'dhcp' => 'dhcp',
                      'hostapd' => 'hostapd',
                      'ntp' => 'ntp',
                      'scripts' => 'scripts',
                      'tor' => 'tor',
                    ],
                  'empty' => __('(select a configuration)'),
                  'id' => "action",
                  'class' => "form-control",
                  'required' => "required",
                ]);
              ?>
          </div>
          <div class="col-lg-2">
            <?= $this->Form->button(
                $this->Html->tag('span', '', [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("Generate"),
                  ])."&nbsp;".__('Generate'),
                [ 'class' => "btn btn-default pull-right", 'escape' => false]) 
            ?>
          </div>
          <div class="col-lg-7"></div>
        </div>


      </div><!-- /.box-body -->
      <?= $this->Form->end() ?>

      <!-- search and some actions -->
      <?= $this->Form->create('search', array('type'=>'get')) ?>
      <div class="box-body">
        <legend><?= __('Advanced settings')?></legend>

        <div class="col-sm-1 col-sm-offset-6">
          <div class="input-group stylish-input-group">
            <?= $this->Form->control('results', [
                'type' => 'select',
                'label' => false,
                'class' => "form-control",
                'title' => __('Results/page'),
                'options' => ['25' => '25', '50' => '50', '100' => '100', '500' => '500'],
                'value' => '25',
              ]);
            ?>
          </div>
        </div>
        <div class="col-sm-5">
          <div class="input-group">
            <input type="text" name="query" class="form-control" value="<?= $search_query ?>" placeholder="<?= __('Search') ?>">
            <?= $this->Form->control('action', [
               'type' => 'hidden',
               'value' => 'search'])
            ?>
            <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-info"><i class="fa fa-search"></i>
              </button>
            </span>
          </div>
        </div>
      </div><!-- /.box-body -->
      <?= $this->Form->end() ?>

      <div class="box-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th><?= $this->Paginator->sort('param') ?></th>
              <th><?= $this->Paginator->sort('value') ?></th>
              <th><?= $this->Paginator->sort('type') ?></th>
              <th><?= $this->Paginator->sort('description') ?></th>
              <th class="actions"><?= __('Actions') ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($config as $config): ?>
            <tr>
              <td><?= h($config->param) ?></td>
              <td><?= h($config->value) ?></td>
              <td><?= h($config->type) ?></td>
              <td><?= h($config->description) ?></td>
              <td class="actions">
                <?= $this->Html->link(
                    $this->Html->tag('span', "", [
                      'class' => "glyphicon glyphicon-edit",
                      'aria-hidden' => "true",
                      'title' => __("Edit"),
                    ]),
                  ['action' => 'edit', $config->param],
                  ['escape' => false]
                  )
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
	  </div>

      <div class="box-footer">

        <div class="paginator pull-right">
          <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
          </ul>
          <p><?= $this->Paginator->counter() ?></p>
        </div>

      </div><!-- /.box-footer -->
	</div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
