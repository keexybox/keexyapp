<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Import a profile')?></h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <?= $this->Form->create($profile) ?>
      <div class="box-body">
        <legend><?= __('General') ?></legend>

        <?php if(isset($import_settings['import_profile'])): ?>

          <div class="form-group">
            <label for="inputProfilename"><?= __('Name')?></label>
            <?php 
            if(isset($import_data['profilename'])) {
              $profilename = $import_data['profilename'];
            } else {
              $profilename = null;
            }
            ?>
            <?= $this->Form->control('profilename', [
                'label' => false,
                'class' => "form-control",
                'value' => $profilename,
                'id' => "inputProfilename",
                'placeholder' => __("Name"),
                ]);
            ?>
          </div>
  
  
          <div class="checkbox">
            <label>
              <?php if(isset($import_data['log_enabled']) and $import_data['log_enabled'] == true): ?>
                <?= $this->Form->control('log_enabled', [
                    'type' => 'checkbox',
                    'label' => __('Statistics enabled'),
                    'checked' => 'checked',
                    ]);
                ?>
              <?php else: ?>
                <?= $this->Form->control('log_enabled', [
                    'type' => 'checkbox',
                    'label' => __('Statistics enabled'),
                    ]);
                ?>
              <?php endif ?>
            </label>
          </div>
  

        <?php else: ?>
          <div class="form-group">
            <label for="inputProfilename"><?= __('Import to profile')?></label>
            <?php if(isset($import_settings['profile_id'])): ?>
              <?= $this->Form->control('profile_id', [
                  'type' => 'select',
                  'label' => false,
                  'options' => $profiles,
                  'value' => $import_settings['profile_id'],
                  'empty' => __('(select a profile)'),
                  'id' => "inputProfile",
                  'class' => "form-control input-lg",
                  'required' => 'required',
                   ]);
              ?>
            <?php else: ?>
              <?= $this->Form->control('profile_id', [
                  'type' => 'select',
                  'label' => false,
                  'options' => $profiles,
                  'empty' => __('(select a profile)'),
                  'id' => "inputProfile",
                  'class' => "form-control input-lg",
                  'required' => 'required',
                   ]);
              ?>
            <?php endif ?>
          </div>
        <?php endif ?>

        <legend><?= __('Search engine options and Blacklist categories')?></legend>

        <label for="bl-select"><?= __('Search engine options')?></label>
        <div class="checkbox">
          <label>
            <?php 
            if (isset($import_data['safesearch_google']) and $import_data['safesearch_google'] == true) {
              $checked = 'checked';
            } else {
              $checked = false;
            }
            ?>
            <?= $this->Form->control('safesearch_google', [
                'type' => 'checkbox',
                'label' => __('Force Google SafeSearch'),
                'checked' => $checked,
                ]);
            ?>
          </label>
        </div>

        <div class="checkbox">
          <label>
            <?php 
            if (isset($import_data['safesearch_bing']) and $import_data['safesearch_bing'] == true) {
              $checked = 'checked';
            } else {
              $checked = false;
            }    
            ?>
            <?= $this->Form->control('safesearch_bing', [
                'type' => 'checkbox',
                'label' => __('Force Bing SafeSearch'),
                'checked' => $checked,
            ]);
            ?>
          </label>
        </div>

        <div class="checkbox">
          <label>
            <?php 
            if (isset($import_data['safesearch_youtube']) and $import_data['safesearch_youtube'] == true) {
              $checked = 'checked';
            } else {
              $checked = false;
            }    
            ?>
            <?= $this->Form->control('safesearch_youtube', [
               'type' => 'checkbox',
               'label' => __('Force Youtube SafeSearch'),
               'checked' => $checked,
               ]);
            ?>
          </label>
        </div>


        <div class="form-group">
          <label for="bl-select"><?= __('Domain categories to filter')?></label>
          <?php if (isset($categories) and is_array($categories)): ?>
            <select name="categories[]" id="bl-select" class="form-control select2" multiple="multiple" data-placeholder="<?= __('Select categories')?>" style="width: 100%;">
            <?php foreach ($categories as $category): ?>
              <?php if($category['enabled'] == 1): ?>
                <option value="<?= $category['category']?>" selected><?= $category['category']?></option>
              <?php else: ?>
                <option value="<?= $category['category']?>"><?= $category['category']?></option>
              <?php endif ?>
            <?php endforeach ?>
            </select>
          <?php else: ?>
            <div class="alert alert-info">
              <?= __('The Blacklist is empty.') ?>
            </div>
          <?php endif ?>
        </div>

        <?php if(isset($import_settings['import_websites']) or isset($import_settings['import_firewall'])): ?>
          <legend><?= __('Firewall and routing') ?></legend>
          <div class="form-group">
            <label for="inputAccesstype"><?= __('Default connection type')?></label>
            <?php 
            if(isset($import_data['default_routing'])) {
              $default_routing = $import_data['default_routing'];
            } else {
              $default_routing = null;
            }
            ?>
            <?= $this->Form->control('default_routing', [
                'type' => 'select',
                'label' => false,
                'class' => "form-control",
                'options' => ['tor' => __('Tor'), 'direct' => __('Direct')],
                'empty' => __('(Select a connection type)'),
                'value' => $default_routing,
                'id' => "inputAccesstype",
                ]);
            ?>
          </div>
  
          <div class="form-group">
            <label for="inputFirewall"><?= __('Default firewall rule')?></label>
            <?php 
            if(isset($import_data['default_ipfilter'])) {
              $default_ipfilter = $import_data['default_ipfilter'];
            } else {
              $default_ipfilter = null;
            }
            ?>
            <?= $this->Form->control('default_ipfilter', [
                'type' => 'select',
                'label' => false,
                'class' => "form-control",
                'options' => ['ACCEPT' => __('Accept'), 'DROP' => __('Drop')],
                'value' => $default_ipfilter,
                'empty' => __('Default firewall rule'),
                'id' => "inputFirewall",
                ]);
            ?>
          </div>

          <div class="checkbox">
            <label>
              <?php if(isset($import_data['use_ttdnsd']) and $import_data['use_ttdnsd'] == true): ?>
                <?= $this->Form->control('use_ttdnsd', [
                    'type' => 'checkbox',
                    'label' => __('Redirect DNS queries to TOR'),
                    'checked' => 'checked',
                    ]);
                ?>
              <?php else: ?>
                <?= $this->Form->control('use_ttdnsd', [
                    'type' => 'checkbox',
                    'label' => __('Redirect DNS queries to TOR'),
                    ]);
                ?>
              <?php endif ?>
            </label>
          </div>

          <?php if(isset($import_settings['import_websites'])): ?>
            <?php 
            if(isset($import_data['profile_routing']) and $import_data['profile_routing'] != '') {
              $profile_routing_msg = ": ".count($import_data['profile_routing'])." ".__('domain(s)');
            } else {
              $profile_routing_msg = __('Nothing to import');
            }
          ?>
          <div class="checkbox">
            <label>
              <?= $this->Form->control('import_routing', [
                  'type' => 'checkbox',
                  'label' => __('Import domain routing')." $profile_routing_msg",
                  'checked' => 'checked',
              ]);?>
            </label>
          </div>
          <?php endif ?>

          <?php if(isset($import_settings['import_firewall'])): ?>
            <?php 
            if(isset($import_data['profile_ipfilters']) and $import_data['profile_ipfilters'] != '') {
              $profile_ipfilters_msg = ": ".count($import_data['profile_ipfilters'])." ".__('rule(s)');
            } else {
              $profile_ipfilters_msg = __('Nothing to import');
            }
          ?>
          <div class="checkbox">
            <label>
              <?= $this->Form->control('import_ipfilters', [
                  'type' => 'checkbox',
                  'label' => __('Import firewall rules')." $profile_ipfilters_msg",
                  'checked' => 'checked',
                  ]);
              ?>
            </label>
          </div>
          <?php endif ?>
        <?php endif ?>

        <?php if(isset($import_settings['import_times'])): ?>
          <legend><?= __('Connection schedules') ?></legend>
          <div class="checkbox">
            <label>
              <?= $this->Form->control('import_times', [
                 'type' => 'checkbox',
                 'label' => __('Import connection schedules'),
                 'checked' => 'checked',
                 ]);
                  ?>
            </label>
          </div>
          <?php if(isset($import_data['profile_times']) && is_array($import_data['profile_times'])): ?>
                <?php if(isset($import_data['profile_times']) and $import_data['profile_times'] != ''): ?>
                  <?= __('The following schedules will be imported:') ?>
                <?php else: ?>
                  <?= __('Nothing to import') ?>
                <?php endif ?>
                <table class="table table-bordered table-striped" id="scroll_table">
                  <thead>
                    <tr>
                      <th><?= __('Mon.') ?></th>
                      <th><?= __('Tue.') ?></th>
                      <th><?= __('Wed.') ?></th>
                      <th><?= __('Thu.') ?></th>
                      <th><?= __('Fri.') ?></th>
                      <th><?= __('Sat.') ?></th>
                      <th><?= __('Sun.') ?></th>
                      <th><?= __('Start time') ?></th>
                      <th><?= __('End time') ?></th>
                    </tr>
                  </thead>
           
	              <tbody>
                  <?php foreach ($import_data['profile_times'] as $profilesTime): ?>
                    <tr>
                      <?php
                      $daysofweek = str_split($profilesTime['daysofweek']);
                      $daysofweek = array_fill_keys($daysofweek, 1);
                      ?>
      
                      <?php foreach(['M', 'T', 'W', 'H', 'F', 'A', 'S'] as $day) : ?>
                      <td>
                        <?php 
                        if(isset($daysofweek[$day])) { echo '<span class="glyphicon glyphicon-ok">';} 
                        ?>
                      </td>
                      <?php endforeach ?>
                      <?php $timerange = explode('-', $profilesTime['timerange']); ?>
                      <td>
                        <?= $timerange[0] ?>
                      </td>
                      <td>
                        <?= $timerange[1] ?>
                      </td>
                    </tr>
                    <?php endforeach ?>
  	              <tbody>
                </table>
          <?php endif ?>
        <?php endif ?>

      </div><!-- /.box-body -->

      <div class="box-footer">
        <?= $this->Html->link(
          $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-remove-sign",
              'aria-hidden' => "true",
              'title' => __("Cancel"),
              ])."&nbsp;".__('Cancel'),
          ['controller' => 'users', 'action' => 'index'], 
          ['class' => "btn btn-default", 'escape' => false]) 
        ?>
        <?= $this->Form->button(
          $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-save",
              'aria-hidden' => "true",
              'title' => __("Import"),
              ])."&nbsp;".__('Import'),
          [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>
      </div><!-- /.box-footer -->
      <?= $this->Form->end() ?>
    </div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->
<script>
  $(function () {
              //Initialize Select2 Elements
              $('.select2').select2()
  })
</script>
<!-- Script for large table horizontal scrolling -->
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
