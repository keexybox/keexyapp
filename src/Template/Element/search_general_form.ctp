      <?= $this->Form->create('search', array('type'=>'get')) ?>
      <div class="col-sm-offset-4 col-sm-2 col-xs-4">
        <div class="input-group stylish-input-group">
          <?= $this->Form->control('results', [
                'type' => 'select',
                'label' => false,
                'class' => "form-control",
                'title' => __('Results/page'),
                'options' => ['25' => '25', '50' => '50', '100' => '100', '500' => '500', '1000' => '1000'],
                'value' => '25',
              ]);
          ?>
        </div>
      </div>
      <div class="col-sm-4 col-xs-8">
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
      <?= $this->Form->end() ?>
