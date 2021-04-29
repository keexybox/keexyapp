<!-- Main content -->
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border"></div>
      <div class="box-body">

         <?php if($cportal_brand_name != ''): ?>
           <h2><center><?= h($cportal_brand_name)?></center></h2>
         <?php endif ?>
         <?php if($cportal_brand_logo_url != ''): ?>
         <center>
           <div id="cportal_img_div">
             <img src="<?= $cportal_brand_logo_url ?>" class="cportal_img">
           </div>
         </center>
         <?php endif ?>
         <?php if($cportal_brand_name != '' OR $cportal_brand_logo_url != ''): ?>
           <hr>
         <?php endif ?>

         <center>
         <a href="<?= h($cportal_homepage_button_url) ?>" class="btn btn-lg btn-success" target="_blank">
              <i class="fa fa-globe"></i>&nbsp;<?= h($cportal_homepage_button_name) ?>
         </a>
         </center>
      </div>
      <div class="box-footer"></div><!-- /.box-footer -->
    </div>
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Tools & diagnostics')?></h3>
      </div>
      <div class="box-body">
        <div class="col-md-12">
        <a href="<?= h($cportal_ip_info_url) ?>" class="btn btn-app" target="_blank">
             <i class="fa fa-laptop"></i><?= __('Check my IP') ?>
        </a>
        <a href="<?= $cportal_check_tor_url ?>" class="btn btn-app" target="_blank">
             <i class="fa fa-user-secret"></i><?= __('Check Tor') ?>
        </a>
        </div>

        <?= $this->Form->create('search', array('type'=>'get')) ?>
        <div class="form-group">
          <label for="domain"><?=  __("Check blocked domain") ?></label>
          <div class="input-group">
            <div class="input-group-addon">
               <i class="fa fa-globe"></i>
            </div>
            <?= $this->Form->control('domain', [
              'id' => 'domain',
              'type' => 'text',
              'label' => false,
              'class' => "form-control",
              'placeholder' => null,
              'value' => $search_domain,
              ]);
            ?>
          </div>
        </div>

        <?php if(isset($bl_domains)): ?>
        <div class="alert alert-warning">
          <i class="icon fa fa-info"></i>
            <?= __('The domains listed below could block the domain {0}.', $search_domain) ?>
        </div>
        <table class="table table-bordered table-striped" id="scroll_table">
          <thead>
            <tr>
              <th><?= $this->Paginator->sort('zone', __('Domain')) ?></th>
              <th><?= $this->Paginator->sort('category', __('Category')) ?></th>
            </tr>
          </thead>
          
          <tbody>
            <?php foreach ($bl_domains as $bl_domain): ?>
            <tr>
              <td><?= h($bl_domain->zone) ?></td>
              <td><?= h($bl_domain->category) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php elseif (isset($search_domain)): ?>
        <div class="row">
          <div class="col-sm-12">
            <div class="alert alert-success">
              <i class="icon fa fa-info"></i>
                <?= __('No domain in the blacklist should block {0}.', $search_domain) ?>
            </div>
          </div>
        </div>
        <?php endif ?>

        <div class="row">
          <div class="col-sm-12">
            <?= $this->Form->button(
              $this->Html->tag('span', '', [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("Check"),
                  ])."&nbsp;".__('Check'),
              [ 'class' => "btn btn-info pull-right float-vertical-align", 'escape' => false]) 
            ?>
          </div>
        </div>
        <?= $this->Form->end() ?>
      </div>
      <div class="box-footer">
      </div><!-- /.box-footer -->
    </div>
      <!-- /.box-header -->
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Connection information')?></h3>
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
        <h4 class="box-title"><?= __('General')?></h4>
        <table class="table table-striped">
          <tr>
            <td><?= __("Name") ?></td>
            <td><?= h("$connection->name") ?></td>
          </tr>
          <tr>
            <td><?= __("Type") ?></td>
            <td><?= __(h("$connection->type")) ?></td>
          </tr>
          <tr>
            <td><?= __("IP address") ?></td>
            <td><?= h($connection->ip) ?></td>
          </tr>
          <tr>
            <td><?= __("Connection start time") ?></td>
            <td><?= $connection->display_start_time->timezone($timezone) ?></td>
          </tr>
  
          <!-- IF CONNEXION STATUS IS SET -->
          <?php if(isset($connection->status)): ?>
          <tr>
            <td><?= __("Connection end time") ?></td>
            <?php if($connection->type == 'Device'): ?>
            <td><?= __('None') ?></td>
            <?php endif; ?>
  
            <?php if($connection->type == 'User'): ?>
            <td><?= $connection->display_end_time->timezone($timezone) ?></td>
            <?php endif; ?>
          </tr>
          <tr>
            <td><?= __("Connection status") ?></td>
            <?php if ($connection->status == 'running'): ?>
            <td>
              <font color="green">
                <?= $this->Html->tag('span', "", [
                    'class' => "glyphicon glyphicon-play",
                    'aria-hidden' => "true",
                    'title' => __("Connected"),
                    ]);
                ?>
                <?= __("Connected") ?>
              </font>
            </td>
            <?php elseif ($connection->status == 'pause'): ?>
            <td>
              <font color="red">
                <?= $this->Html->tag('span', "", [
                    'class' => "glyphicon glyphicon-pause",
                    'aria-hidden' => "true",
                    'title' => __("Paused"),
                ]);
                ?>
                <?= __("Paused") ?>
              </font>
            </td>
            <?php endif ?>
          </tr>
          <?php endif ?>
        </table>

        <hr>
        <h4 class="box-title"><?= __('Profile')?></h4>
        <table class="table table-striped">
          <tr>
            <td><?= __("Profile") ?></td>
            <td><?= h($connection->profile->profilename) ?></td>
          </tr>
          <tr>
            <td><?= __("Default connection type") ?></td>
            <td><?php 
              if($connection->profile->default_routing == 'direct') {
                echo __('Direct');
              } elseif ($connection->profile->default_routing == 'tor') {
                echo __('Tor');
              }
              ?>
            </td>
          </tr>
          <tr>
            <td><?= __("Default Firewall rule") ?></td>
            <td><?= h($connection->profile->default_ipfilter) ?></td>
          </tr>
          <tr>
            <td><?= __("Statistics enabled") ?></td>
            <td><?= h($connection->profile->log_enabled) ? 
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-ok",
                'aria-hidden' => "true",
                'title' => __("Enabled"),
              ]) : '';
              ?>
            </td>
          </tr>
          <tr>
            <td><?= __("Redirect DNS queries to TOR") ?></td>
            <td><?= h($connection->profile->use_ttdnsd) ? 
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-ok",
                'aria-hidden' => "true",
                'title' => __("Enabled"),
              ]) : '';
              ?>
            </td>
          </tr>
        </table>

        <hr>
        <h4 class="box-title"><?= __('Search engine options and Blacklist categories')?></h4>
        <table class="table table-striped">
          <tr>
            <td><?= __("Force Google SafeSearch") ?></td>
            <td><?= h($connection->profile->safesearch_google) ? 
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-ok",
                'aria-hidden' => "true",
                'title' => __("Enabled"),
              ]) : '';
              ?>
            </td>
          </tr>
          <tr>
            <td><?= __("Force Bing SafeSearch") ?></td>
            <td><?= h($connection->profile->safesearch_bing) ? 
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-ok",
                'aria-hidden' => "true",
                'title' => __("Enabled"),
              ]) : '';
              ?>
            </td>
          </tr>
          <tr>
            <td><?= __("Force Youtube SafeSearch") ?></td>
            <td><?= h($connection->profile->safesearch_youtube) ? 
              $this->Html->tag('span', "", [
                'class' => "glyphicon glyphicon-ok",
                'aria-hidden' => "true",
                'title' => __("Enabled"),
              ]) : '';
              ?>
            </td>
          </tr>
          <?php if(!empty($connection->profile_blacklists)): ?>
		  <tr>
		    <td><?= __('Blacklist categories').":" ?></td>
			<td>
              <?php 
			    $categories_string = null;
                foreach ($connection->profile_blacklists as $profileBlacklist){
                  $categories_string .= $profileBlacklist->category.", ";
				}
				echo rtrim($categories_string, ", ");
			  ?>
            </td>
          </tr>
          <?php endif ?>
        </table>
	  </div>
      <div class="box-footer">
	  </div>
    </div>
  </div>

  <?php if(!empty($connection->profile_times)): ?>
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Connection schedules')?></h3>
      </div>
      <div class="box-body">
        <table class="table table-striped" id="scroll_table_times">
		<thead>
		</thead>
		<tbody>
        <?php foreach ($connection->profile_times as $profilesTime): ?>
          <?php
            $daysofweek = str_split($profilesTime->daysofweek);
            $daysofweek = array_fill_keys($daysofweek, null);
            $days = null;
            foreach($daysofweek as $key => $day) {
              if($key == 'M') {
                $daysofweek['M'] = __('mon. ');
                $days .= $daysofweek['M'];
              }
              elseif ($key == 'T') {
                $daysofweek['T'] = __('tues. ');
                $days .= $daysofweek['T'];
              }
              elseif ($key == 'W') {
                $daysofweek['W'] = __('wed. ');
                $days .= $daysofweek['W'];
              }
              elseif ($key == 'H') {
                $daysofweek['H'] = __('thurs. ');
                $days .= $daysofweek['H'];
              }
              elseif ($key == 'F') {
                $daysofweek['F'] = __('fri. ');
                $days .= $daysofweek['F'];
              }
              elseif ($key == 'A') {
                $daysofweek['A'] = __('sat. ');
                $days .= $daysofweek['A'];
              }
              elseif ($key == 'S') {
                $daysofweek['S'] = __('sun. ');
                $days .= $daysofweek['S'];
              }
            }
            $timerange = explode('-', $profilesTime->timerange);
          ?>
          <tr>
            <td><?= h($days) ?></td>
            <td><?= h($timerange[0]." - ".$timerange[1]) ?></td>
          </tr>
  
        <?php endforeach; ?>
		</tbody>
        </table>
	  </div>
      <div class="box-footer">
	  </div>
    </div>
  </div>
  <?php endif ?>
<!--</div>-->

<!-- <div class="row"> -->
  <?php if(!empty($connection->profile_ipfilters)): ?>
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Firewall rules')?></h3>
      </div>
      <div class="box-body">
        <table class="table table-striped" id="scroll_table_firewall">
		  <thead>
            <th><?= __('Position') ?></th>
            <th><?= __('Destination IP') ?></th>
            <th><?= __('Protocol') ?></th>
            <th><?= __('Ports') ?></th>
            <th><?= __('Target') ?></th>
            <th><?= __('Enabled') ?></th>
          <thead>
  
		  <tbody>
            <?php foreach ($connection->profile_ipfilters as $ProfileIpfilter): ?>
            <tr>
              <td>
                <?= h($ProfileIpfilter->rule_number) ?>
              </td>
      
              <?php if($ProfileIpfilter->dest_ip_type == 'net') : ?>
              <td><?= h($ProfileIpfilter->dest_ip."/".$ProfileIpfilter->dest_ip_mask) ?></td>
      
              <?php elseif($ProfileIpfilter->dest_ip_type == 'range') : ?>
              <td><?= h($ProfileIpfilter->dest_iprange_first." - ".$ProfileIpfilter->dest_iprange_last) ?></td>
      
              <?php elseif($ProfileIpfilter->dest_ip_type == 'hostname') : ?>
              <td><?= h($ProfileIpfilter->dest_hostname) ?></td>
      
              <?php endif ?>
      
              <td><?= h($ProfileIpfilter->protocol) ?></td>
      
              <td>
                <?php if($ProfileIpfilter->dest_ports != ''): ?>
                  <?= h($ProfileIpfilter->dest_ports) ?>
                <?php else: ?>
                  <?= 'ANY' ?>
                <?php endif ?>
              </td>
      
              <td><?= h($ProfileIpfilter->target) ?></td>
        
              <td><?= h($ProfileIpfilter->enabled) ? 
                    $this->Html->tag('span', "", [
                      'class' => "glyphicon glyphicon-ok",
                      'aria-hidden' => "true",
                      'title' => __("Enabled"),
                    ]) : '';
                ?>
              </td>
            </tr>
            <?php endforeach; ?>
		  </tbody>
        </table>
	  </div>
      <div class="box-footer">
	  </div>
    </div>
  </div>
  <?php endif ?>
  <?php if(!empty($connection->profile_routing)): ?>
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><?= __('Domains routing')?></h3>
      </div>
      <div class="box-body">
        <table class="table table-striped" id="scroll_table_routing">
		  <thead>
		    <tr>
              <th><?= __('Address') ?></th>
              <th><?= __('Connection type') ?></th>
              <th><?= __('Enabled') ?></th>
		    </tr>
		  </thead>
		  <tbody>
            <?php foreach ($connection->profile_routing as $ProfileUrls): ?>
            <tr>
              <td>
                <?= $this->Html->link(
                $ProfileUrls->address, 
                  'http://'.$ProfileUrls->address,
                  ['target' => '_blank']
                ) 
                ?>
              </td>
              <td>
                <?php
                if($ProfileUrls->routing == 'direct') { $routing = __('Direct'); }
                elseif($ProfileUrls->routing == 'tor') { $routing = __('Tor'); }
                else {$ProfileUrls = $ProfileUrls->routing;}
                echo $routing;
                ?>
              </td>
              <td>
                <?= h($ProfileUrls->enabled) ? 
                $this->Html->tag('span', "", [
                  'class' => "glyphicon glyphicon-ok",
                  'aria-hidden' => "true",
                  'title' => __("yes"),
                ])
                : '' 
                ?>
              </td>
            </tr>
            <?php //$website_count++ ?>
            <?php endforeach; ?>
		  </tbody>
        </table>
	  </div>
      <div class="box-footer">
	  </div>
    </div>
  </div>
  <?php endif ?>
</div>

<!-- Script for large table horizontal scrolling -->
<script>
$(document).ready(function() {
  $('#scroll_table_times').DataTable( {
    "scrollX": true,
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false,
    "searching": false,
  } );
});
$(document).ready(function() {
  $('#scroll_table_firewall').DataTable( {
    "scrollX": true,
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false,
    "searching": false,
  } );
});
$(document).ready(function() {
  $('#scroll_table_routing').DataTable( {
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
