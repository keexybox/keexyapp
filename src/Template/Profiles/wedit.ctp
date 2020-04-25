<legend><?= __('KeexyBox’s configuration wizard').': '.__('Edit profile: {0}', h($profile->profilename)) ?></legend>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <p><?= __("You can now adjust the profile settings.") ?></p>
	  <ul>
	    <li><?= '<b>'.__('Statistics enabled').'</b>: '.__('This option enables logging and permits connection statistics.') ?></li>
	    <li><?= '<b>'.__('Search engine options').'</b>: '.__('These options enable filtering in search engines.') ?></li>
	    <li><?= '<b>'.__('Domain categories to filter').'</b>: '.__('Enable Blacklist categories to use for blocking domains.') ?></li>
	    <li><?= '<b>'.__('Default connection type').'</b>: '.__('Use anonymous connection (Tor) or not (Direct).') ?></li>
	    <li><?= '<b>'.__('Default Firewall rule').'</b>: '.__('Drop or allow connections to the Internet by default.') ?></li>
	    <li><?= '<b>'.__('Redirect DNS queries to TOR').'</b>: '.__('This option allows you to redirect DNS queries to the Tor network for more anonymity.') ?></li>
	    <li><?= '<b>'.__('Connection schedules').'</b>: '.__('Add Internet connection schedules.') ?></li>
	  </ul>
    </div>
  </div>
</div>
<?= $this->Flash->render('reload_profile') ?>
<?= $this->Flash->render('reconnect') ?>
<?= $this->Form->create($profile) ?>
<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-4">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('General')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
        <div class="form-group">
          <label for="inputProfilename"><?= __('Name')?></label>
          <?= $this->Form->control('profilename', [
                        'label' => false,
                        'class' => "form-control",
                        'id' => "inputProfilename",
                        'placeholder' => __("Name"),
                    ]);
          ?>
        </div>
    

        <div class="checkbox">
          <label>
            <?= $this->Form->control('log_enabled', [
					'id' => 'log_enabled',
                    'type' => 'checkbox',
                    'label' => __('Statistics enabled'),
                ]);
            ?>
          </label>
        </div>

      </div>
      <!-- /.box-body -->

      <div class="box-footer">

      </div><!-- /.box-footer -->
    </div><!-- /.box -->
  </div><!-- /.col -->

  <!-- right column -->
  <div class="col-md-4">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Filtering')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
        <label><?= __('Search engine options')?></label>
        <div class="checkbox">
          <label>
          <?= $this->Form->control('safesearch_google', [
                    'type' => 'checkbox',
                    'label' => __('Force Google SafeSearch'),
                ]);
          ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
          <?= $this->Form->control('safesearch_bing', [
                    'type' => 'checkbox',
                    'label' => __('Force Bing SafeSearch'),
                ]);
          ?>
          </label>
        </div>
        <div class="checkbox">
          <label>
          <?= $this->Form->control('safesearch_youtube', [
                    'type' => 'checkbox',
                    'label' => __('Force Youtube SafeSearch'),
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
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
    </div><!-- /.box -->
  </div><!-- /.col -->

  <div class="col-md-4">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Firewall and routing')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
        <div class="form-group">
          <label for="inputConnType"><?= __('Default connection type')?></label>
          <?= $this->Form->control('default_routing', [
                        'type' => 'select',
                        'label' => false,
                        'class' => "form-control",
                        'empty' => __('(Select a connection type)'),
                        'id' => "inputConnType",
                        'options' => [
                            'direct' => __('Direct'), 
                            'tor' => __('Tor'), 
                        ],
                    ]);
          ?>
        </div>
    
        <div class="form-group">
          <label for="inputFirewallRule"><?= __('Default Firewall rule')?></label>
          <?= $this->Form->control('default_ipfilter', [
                        'type' => 'select',
                        'label' => false,
                        'class' => "form-control",
                        'options' => ['ACCEPT' => __('Accept'), 'DROP' => __('Drop')],
                        'empty' => __('(Select a rule)'),
                        'id' => "inputFirewallRule",
                    ]);
          ?>
        </div>
        <div class="checkbox">
          <label>
            <?= $this->Form->control('use_ttdnsd', [
						'id' => 'dnstor',
                        'type' => 'checkbox',
                        'label' => __('Redirect DNS queries to TOR'),
                    ]);
            ?>
          </label>
        </div>
		<!--
        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-filter",
              'aria-hidden' => "true",
              'title' => __("Edit"),
              ])."&nbsp;".__('Manage domains routing'),
            //['controller' => 'ProfilesRouting', 'action' => 'index', $profile->id], 
            '#',
            [ 'class' => "btn btn-app btn-info", 'escape' => false, 'onclick' => "open_window_f('/profiles-routing/index/$profile->id')"]) 
        ?>
        <?= $this->Html->link(
            $this->Html->tag('span', '', [
              'class' => "glyphicon glyphicon-fire",
              'aria-hidden' => "true",
              'title' => __("Edit"),
              ])."&nbsp;".__('Manage Firewall'),
            //['controller' => 'ProfilesIpfilters', 'action' => 'index', $profile->id], 
            '#',
            [ 'class' => "btn btn-app btn-warning", 'escape' => false, 'onclick' => "open_window_f('/profiles-ipfilters/index/$profile->id')"]) 
        ?>
		-->
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
      </div><!-- /.box-footer -->
    </div><!-- /.box -->
  </div><!-- /.col -->

</div><!-- /.row -->

<div class="row">
  <!-- right column -->
  <div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Connection schedules')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
        <table class="table table-striped" id="scroll_table">
		  <thead>
  		  <!-- schedules headers -->
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
              <th><?= __('Actions') ?></th>
            </tr>
		  </thead>
  
		  <tbody>
            <?php foreach ($profile->profiles_times as $profilesTime): ?>
              <?php
                $daysofweek = str_split($profilesTime->daysofweek);
                $daysofweek = array_fill_keys($daysofweek, 1);
                $timerange = explode('-', $profilesTime->timerange);
              ?>
    
    		    <!-- display existing schedules -->
              <tr>
                <?php foreach(['M', 'T', 'W', 'H', 'F', 'A', 'S'] as $day) : ?>
                <td>
                  <?php 
                    if(isset($daysofweek[$day])) {
                      echo "<input type=\"checkbox\" name=\"times[".$profilesTime['id']."][".$day."]\" value=\"".$day."\" checked=\"checked\">";
                    } else {
                      echo "<input type=\"checkbox\" name=\"times[".$profilesTime['id']."][".$day."]\" value=\"".$day."\">";
                    }
                  ?>
                </td>
                <?php endforeach ?>
      
                <td>
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                      </div>
                      <input type="text" name="times[<?= $profilesTime['id'] ?>][starttime]" class="form-control timepicker" value="<?= $timerange[0] ?>">
                    </div>
                  </div>
				</td>

                <td>
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                      </div>
                      <input type="text" name="times[<?= $profilesTime['id'] ?>][endtime]" class="form-control timepicker" value="<?= $timerange[1] ?>">
                    </div>
                  </div>
                </td>
      
                <td class="actions">
                  <?= $this->Form->postLink(
                      $this->Html->tag('span', "", [
                        'class' => "glyphicon glyphicon-trash",
                        'aria-hidden' => "true",
                        'title' => __("Delete"),
                      ]),
                      ['controller' => 'profiles_times', 'action' => 'delete', $profilesTime->id],
                      ['escape' => false, 'block' => true, 'confirm' => __('Are you sure you want to delete?')]
                    )
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
  		  <!-- end display existing schedules -->
  
  		  <!-- display 10 new schedules form input -->
            <?php while ($count_times <= $max_times) : ?>
              <tr id="time<?= $count_times ?>" style="display:none">
                <?php 
                  foreach(['M', 'T', 'W', 'H', 'F', 'A', 'S'] as $day) {
                    echo "<td>";
                    echo "<input type=\"checkbox\" id=\"val_".$count_times."_".$day."\" name=\"newtimes[".$count_times."][".$day."]\" value=\"".$day."\">";
                    echo "</td>";
                  }
                ?>
      
                <td>
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                      </div>
                      <input id="new_start_hour_<?= $count_times ?>" type="text" name="newtimes[<?= $count_times ?>][starttime]" class="form-control timepicker" value="00:00">
                    </div>
                  </div>
                </td>
      
                <td>
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                      </div>
                      <input id="new_end_hour_<?= $count_times ?>" type="text" name="newtimes[<?= $count_times ?>][endtime]" class="form-control timepicker" value="00:00">
                    </div>
                  </div>
                </td>
                <td></td>
                <?php $count_times++; ?>
              </tr>
            <?php endwhile ?>
		  </tbody>
        </table>
  
        <div class="row" id="new_links_div_0">
		  <div class="col-md-6">
            <button class="btn btn-default btn-link" onclick="add_new_field(1); return false;">
              <span class="glyphicon glyphicon-plus"></span>
              <?= __('Add schedule') ?>
            </button>
		  </div>
        </div>
  
        <?php
        $count_times = 1;
        while ($count_times <= $max_times) : ?>
        <div class="row" id="new_links_div_<?= $count_times ?>" style="display:none">
          <?php if($count_times != $max_times) : ?>
		  <div class="col-md-6">
            <button class="btn btn-default btn-link" onclick="add_new_field(<?=$count_times + 1 ?>); return false;">
              <span class="glyphicon glyphicon-plus"></span>
              <?= __('Add schedule') ?>
            </button>
            <button class="btn btn-default btn-link" onclick="remove_new_field(<?= $count_times ?>); return false;">
              <span class="glyphicon glyphicon-remove"></span>
              <?= __('Remove last schedule') ?>
            </button>
	      </div>
          <?php else : ?>
		  <div class="col-md-6">
            <button class="btn btn-default btn-link" onclick="remove_new_field(<?= $count_times ?>); return false;">
              <span class="glyphicon glyphicon-remove"></span>
              <?= __('Remove last schedule') ?>
            </button>
		  </div>
          <?php endif ?>
          <?php $count_times++; ?>
        </div>
        <?php endwhile ?>


      </div>
      <!-- /.box-body -->

      <div class="box-footer">

      </div><!-- /.box-footer -->
    </div><!-- /.box -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="row">
    <div class="col-sm-12">
        <a onclick="back_link()" class="btn btn-success pull-right"><?= __('Skip / Next') ?>&nbsp;<span class="glyphicon glyphicon-chevron-right" aria-hidden="true" title="<?= __('Skip') ?>"></span></a>
        <?= $this->Form->button(
                $this->Html->tag('span', '', [
                    'class' => "glyphicon glyphicon-save",
                    'aria-hidden' => "true",
                    'title' => __("Next"),
                    ])."&nbsp;".__('Save'),
                [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
        ?>
    </div>
</div>

<?= $this->Form->end() ?>
<script>
  $(function () {
              //Initialize Select2 Elements
              $('.select2').select2()
  })
</script>
<?= $this->fetch('postLink') // fetch delete operation outside the main form?>

<!-- Script to add or remove times -->
<script>
function add_new_field(field) {
    var scroll = $(window).scrollTop();
    $(document).ready(function() {
        $('#time' + field).show();
        $('#new_links_div_' + field).show();
        $('#new_links_div_' + (field - 1)).hide();
        return false;    
    });
    $("html").scrollTop(scroll);
}

function remove_new_field(field) {
    var scroll = $(window).scrollTop();
    $(document).ready(function() {
        $('#time' + field).hide();
        $('#val_' + field + '_M').attr('checked', false);
        $('#val_' + field + '_T').attr('checked', false);
        $('#val_' + field + '_W').attr('checked', false);
        $('#val_' + field + '_H').attr('checked', false);
        $('#val_' + field + '_F').attr('checked', false);
        $('#val_' + field + '_A').attr('checked', false);
        $('#val_' + field + '_S').attr('checked', false);
        $('#new_start_hour_' + field).val(0);
        $('#new_start_min_' + field).val(0);
        $('#new_end_hour_' + field).val(0);
        $('#new_end_min_' + field).val(0);
        $('#new_links_div_' + field).hide();
        $('#new_links_div_' + (field - 1)).show();
        return false;
    });
    $("html").scrollTop(scroll);
}
</script>

<!-- Script to select blacklist categories with multiselect -->
<script>
    $('#bl-select').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        buttonClass: 'btn-default btn-lg',
        buttonWidth: 'auto',
        numberDisplayed: 100,
        filterPlaceholder: '<?= __('Search')?>',
        nonSelectedText: '<?= __('None selected')?>',
        nSelectedText: '<?= __('selected')?>',
        selectAllText: '<?= __(' Select all')?>',
        maxHeight: 400
    });
</script>

<!-- Script to open domains routing or firewall page in a new window  -->
<script>
function open_window_f() {
        window.open(arguments[0], "_blank", "location=no,status=no,menubar=no,toolbar=no,scrollbars=yes,resizable=yes,top=500,left=500,width=600,height=600");
}
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
<script>
  $(function () {
    //Timepicker
    $('.timepicker').timepicker({
      showInputs: false,
      minuteStep: 5,
      showMeridian: false,
      showSeconds: false,
      secondStep: 1,
    })
  });
</script>
<script>
var urlParams = new URLSearchParams(location.search);
var install_type = urlParams.get('install_type');
function back_link() {
  window.location.href = "/profiles/wadd?install_type=" + install_type;
}
</script>
