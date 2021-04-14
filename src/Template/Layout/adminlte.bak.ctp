<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>KeexyBox | <?= __('The box to keep the Internet under your control') ?></title>
  <?= $this->element('css_load') ?>
  <?= $this->element('js_load') ?>

</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to to the body tag
to get the desired effect
|---------------------------------------------------------|
|LAYOUT OPTIONS | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-keexybox sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="dropdown notifications-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <i class="fa fa-flag"></i>
          <!--<span class="label label-warning">10</span>-->
        </a>
        <ul class="dropdown-menu">
          <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
              <?php foreach($languages_list as $lang_link => $language) : ?>
              <li>
                <a href="<?= $lang_link ?>">
					  <?= $language ?>
                </a>
              </li>
            <?php endforeach ?>
            </ul>
          </li>
        </ul>
      </li>
      <!-- /.messages-menu -->
		  <li class="dropdown user user-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		      <i class="fa fa-cog"></i><?= __('Admin') ?>
			</a>
        <ul class="dropdown-menu">
          <li class="user-header">
            <p class="user-info">
		          <?php if(isset($lo_client['session_status'])): ?>
				    <?= __('KeexyBox management') ?>
                <small><?= __('Login: {0}', $lo_client['session_details']['Auth']['User']['username']) ?></small>
                <small><?= __('Name: {0}', $lo_client['session_details']['Auth']['User']['displayname']) ?></small>
			      <?php else: ?>
				    <?= __('Click on Manage to edit your account or manage KeexyBox') ?>
			      <?php endif ?>
				</p>
			  </li>
          <li class="user-footer">
            <div class="pull-right">
		          <?php if(isset($lo_client['session_status'])): ?>
                <a href="/users/logout" class="btn btn-default btn-flat"><?= __('Quit') ?></a>
			      <?php else: ?>
                <a href="/users/adminlogin" class="btn btn-default btn-flat"><?= __('Sign in') ?></a>
			      <?php endif ?>
            </div>
          </li>
			</ul>
		  </li>

      <!-- User Account Menu -->
      <li class="dropdown user user-menu">
        <!-- Menu Toggle Button -->
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
          <!-- The user image in the navbar-->
          <!-- <img src="/adminlte/dist/img/user2-160x160.jpg" class="user-image" alt="User Image"> -->
			  <?php if(isset($lo_client['connection_status']) and $lo_client['connection_status'] == 'running'): ?>
          <i class="fa fa-circle fa-circle-online"></i><?= __('Online') ?>
			  <?php elseif(isset($lo_client['connection_status']) and $lo_client['connection_status'] == 'pause'): ?>
          <i class="fa fa-circle fa-circle-pause"></i><?= __('Paused') ?>
			  <?php else: ?>
          <i class="fa fa-circle fa-circle-offline"></i><?= __('Offline') ?>
			  <?php endif ?>
          <!-- hidden-xs hides the username on small devices so only the image appears. -->
        </a>
        <ul class="dropdown-menu">
          <!-- The user image in the menu -->
          <li class="user-header">
            <!-- <img src="/adminlte/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">-->
            <p class="user-info">
			      <?php if(isset($lo_client['connection_status'])): ?>
              <?= __('Connected to the Internet', $lo_client['connection_details']['type']) ?>
              <small><?= __('Name: {0}', $lo_client['connection_details']['name']) ?> </small>
              <small><?= __('Type').': ' . __($lo_client['connection_details']['type']) ?> </small>
              <small><?= __('Default connection type').': ' . __($lo_client['connection_details']['profile']['default_routing']) ?> </small>
              <small><?= __('Default Firewall rule').': ' . __($lo_client['connection_details']['profile']['default_ipfilter']) ?> </small>
              <small><?= __('IP Address: {0}', $lo_client['connection_details']['ip']) ?> </small>
              <small><?= __('Profile: {0}', $lo_client['connection_details']['profile']['profilename']) ?> </small>
				  <?php else: ?>
              <?= __('Disconnected from the Internet') ?>
			      <?php endif ?>
            </p>
          </li>
          <li class="user-footer">
		        <?php if(isset($lo_client['connection_status'])): ?>
              <div class="pull-left">
                <a href="/connections/view" class="btn btn-default btn-flat"><?= __('Info') ?></a>
				  </div>
              <div class="pull-right">
                <a href="/users/disconnect" class="btn btn-default btn-flat"><?= __('Disconnect') ?></a>
				  </div>
			    <?php else: ?>
              <div class="pull-right">
                <a href="/users/portal" class="btn btn-default btn-flat"><?= __('Connect') ?></a>
              </div>
			    <?php endif ?>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- Logo -->
    <a href="#" class="brand-link">
      <img src="/img/kxb-favicon-white.png" class="brand-image" alt="KeexyBox">
      <span class="brand-text"><img src="/img/logo-keexybox.png" class="brand-image" alt="KeexyBox"></span>
    </a>
    <!--
    <a href="index3.html" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>-->

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <!--
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div>
      -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <?php if($current_controller == 'Statistics'): ?> <li class="nav-item active"> <?php else: ?> <li class="nav-item"> <?php endif ?>
          <a href="/statistics" class="nav-link"><i class="nav-icon fas fa-chart-bar"></i> <p><?= __('Statistics') ?></p></a></li>

          <?php if($current_controller == 'Connections'): ?> <li class="nav-item active"> <?php else: ?> <li class="nav-item"> <?php endif ?>
          <a href="/connections"class="nav-link"><i class="nav-icon fas fa-users"></i> <p><?= __('Connections') ?></p></a></li>
  
          <?php if($current_controller == 'Users' or $current_controller == 'Devices' or $current_controller == 'Profiles' or $current_controller == 'Blacklist'): ?> <li class="nav-item has-treeview active"> <?php else: ?> <li class="nav-item has-treeview"> <?php endif ?>
            <a href="#" class="nav-link"><i class="nav-icon fas fa-globe"></i> 
              <p><?= __('Connection settings') ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="/blacklist" class="nav-link"><i class="nav-icon far fa-minus-circle"></i><p><?= __('Blacklist') ?></p></a></li>
              <li class="nav-item"><a href="/profiles" class="nav-link"><i class="nav-icon far fa-sliders-h"></i><p><?= __('Profiles') ?></p></a></li>
              <li class="nav-item"><a href="/users" class="nav-link"><i class="nav-icon far fa-user"></i><p><?= __('Users') ?></p></a></li>
              <li class="nav-item"><a href="/devices" class="nav-link"><i class="nav-icon far fa-tablet"></i><p><?= __('Devices') ?></p></a></li>
            </ul>
          </li>
  
          <?php if($current_controller == 'Config'): ?> <li class="treeview active"> <?php else: ?> <li class="treeview"> <?php endif ?>
            <a href="#"><i class="fa fa-cogs"></i> <span><?= __('System settings') ?></span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="/config/network"><i class="fa fa-laptop"></i><?= __('Network') ?></a></li>
              <li><a href="/config/datetime"><i class="fa fa-clock"></i><?= __('Date and time') ?></a></li>
              <li><a href="/config/dhcp"><i class="fa fa-server"></i><?= __('DHCP') ?></a></li>
              <li><a href="/config/wifiap"><i class="fa fa-wifi"></i><?= __('Wireless Access Point') ?></a></li>
              <li><a href="/config/captiveportal"><i class="fa fa-road"></i><?= __('Captive portal') ?></a></li>
              <li><a href="/config/certificate"><i class="fa fa-certificate"></i><?= __('SSL Certificate') ?></a></li>
              <li><a href="/config/misc"><i class="fa fa-cog"></i><?= __('Miscellaneous') ?></a></li>
            </ul>
          </li>
  
          <?php if($current_controller == 'Tools'): ?> <li class="treeview active"> <?php else: ?> <li class="treeview"> <?php endif ?>
            <a href="#"><i class="fa fa-wrench"></i> <span><?= __('Tools & diagnostics') ?></span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="/tools/services"><i class="fa fa-power-off"></i><?= __('Services and power') ?></a></li>
              <li><a href="/tools/domain-issue"><i class="fa fa-check-square"></i><?= __('Domain check') ?></a></li>
              <li><a href="/tools/system-state"><i class="fa fa-heartbeat"></i><?= __('System state') ?></a></li>
              <li><a href="/tools/update?step=1"><i class="fa fa-cloud-download-alt"></i><?= __('Update') ?></a></li>
            </ul>
          </li>
  
          <?php if($current_controller == 'Help'): ?> <li class="treeview active"> <?php else: ?> <li class="treeview"> <?php endif ?>
            <a href="#"><i class="fa fa-question-circle"></i> <span><?= __('Help') ?></span>
              <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="http://keexybox.org" target="_blank"><i class="fa fa-external-link-alt"></i><?= __('Website') ?></a></li>
              <li><a href="http://wiki.keexybox.org" target="_blank"><i class="fa fa-book"></i><?= __('Documentation') ?></a></li>
              <li><a href="http://keexybox.org/donate" target="_blank"><i class="fa fa-star"></i><?= __('Donate') ?></a></li>
              <li><a href="/help/licenses"><i class="fa fa-file-alt"></i><?= __('Licenses') ?></a></li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <div class="content">
      <div class="container-fluid">
	  <!-- PAGE CONTENT -->
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
	  <!-- END PAGE CONTENT -->
      </div>
    </div>
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
	  KeexyBox - <?= __("The box to keep the Internet under your control") ?>
    </div>
    <!-- Default to the left -->
    <?= $this->element('copyright') ?>
    <!--
    <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.0.0
    </div>
    -->
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="dist/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<script src="dist/js/demo.js"></script>
<script src="dist/js/pages/dashboard3.js"></script>
</body>
</html>
