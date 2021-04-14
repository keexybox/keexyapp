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
<body class="hold-transition sidebar-mini skin-keexybox">
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

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-flag"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <div class="dropdown-divider"></div>
          <?php foreach($languages_list as $lang_link => $language) : ?>
          <a href="<?= $lang_link ?>" class="dropdown-item">
	     	<?= $language ?>
          </a>
          <?php endforeach ?>

        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-cog"></i><?= __('Admin') ?>
          <!--<span class="badge badge-warning navbar-badge">15</span>-->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header"><?= __('KeexyBox management') ?></span>
		  <?php if(isset($lo_client['session_status'])): ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-user mr-2"></i>
            <?= h($lo_client['session_details']['Auth']['User']['username']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('Login').')' ?></span>
          </a>
          <a href="#" class="dropdown-item">
            <i class="fas fa-id-card mr-2"></i>
            <?= h($lo_client['session_details']['Auth']['User']['displayname']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('Display name').')' ?></span>
          </a>
		  <?php else: ?>
		    <?= __('Click on Manage to edit your account or manage KeexyBox') ?>
		  <?php endif ?>

          <div class="dropdown-divider"></div>
		  <?php if(isset($lo_client['session_status'])): ?>
          <a href="/users/logout" class="dropdown-item dropdown-footer"><?= __('Quit') ?></a>
          <?php else: ?>
          <a href="/users/adminlogin" class="dropdown-item dropdown-footer"><?= __('Sign in') ?></a>
          <?php endif ?>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <?php if(isset($lo_client['connection_status']) and $lo_client['connection_status'] == 'running'): ?>
            <i class="fa fa-circle fa-circle-online"></i><?= __('Online') ?>
          <?php elseif(isset($lo_client['connection_status']) and $lo_client['connection_status'] == 'pause'): ?>
            <i class="fa fa-circle fa-circle-pause"></i><?= __('Paused') ?>
          <?php else: ?>
            <i class="fa fa-circle fa-circle-offline"></i><?= __('Offline') ?>
          <?php endif ?>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
	    <?php if(isset($lo_client['connection_status'])): ?>
          <span class="dropdown-item dropdown-header"><?= __('Connected to the Internet') ?></span>
          <div class="dropdown-divider"></div>
          <span class="dropdown-item">
            <i class="fas fa-user mr-2"></i>
            <?= h($lo_client['connection_details']['name']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('Login').')' ?></span>
          </span>
          <span class="dropdown-item">
            <i class="fas fa-tag mr-2"></i>
            <?= h($lo_client['connection_details']['type']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('Connected as').')' ?></span>
          </span>
          <span class="dropdown-item">
            <i class="fas fa-network-wired mr-2"></i>
            <?= h($lo_client['connection_details']['profile']['default_routing']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('Default connection type').')' ?></span>
          </span>
          <span class="dropdown-item">
            <i class="fas fa-fire mr-2"></i>
            <?= h($lo_client['connection_details']['profile']['default_ipfilter']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('Default Firewall rule').')' ?></span>
          </span>
          <span class="dropdown-item">
            <i class="fas fa-laptop mr-2"></i>
            <?= h($lo_client['connection_details']['ip']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('IP address').')' ?></span>
          </span>
          <span class="dropdown-item">
            <i class="fas fa-sliders-h mr-2"></i>
            <?= h($lo_client['connection_details']['profile']['profilename']) ?>
            <span class="float-right text-muted text-sm"><?= '('.__('Profile').')' ?></span>
          </span>
          <div class="dropdown-divider"></div>
          <div class="dropdown-footer">
              <div class="float-left"><a href="/connections/view" class="btn btn-default"><?= __('Info') ?></a></div>
              <div class="pull-right"><a href="/users/disconnect" class="btn btn-default"><?= __('Disconnect') ?></a></div>
          </div>
        <?php else: ?>
          <span class="dropdown-item dropdown-header"><?= __('Disconnected from the Internet') ?></span>
          <div class="dropdown-divider"></div>
          <div class="dropdown-footer">
            <a href="/users/portal" class="btn btn-default"><?= __('Connect') ?></a>
          </div>
        <?php endif ?>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="/img/kxb-favicon-white.png" alt="KeexyBox" class="brand-image"
           style="opacity: .8">
      <span class="brand-text font-weight-light"><img src="/img/logo-keexybox.png"></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <?php if($current_controller == 'Statistics'): ?> <li class="nav-item active"> <?php else: ?> <li class="nav-item"> <?php endif ?>
          <a href="/statistics" class="nav-link"><i class="nav-icon fas fa-chart-bar"></i> <p><?= __('Statistics') ?></p></a></li>

          <?php if($current_controller == 'Connections'): ?> <li class="nav-item active"> <?php else: ?> <li class="nav-item"> <?php endif ?>
          <a href="/connections" class="nav-link"><i class="nav-icon fas fa-users"></i> <p><?= __('Connections') ?></p></a></li>
  
          <?php if($current_controller == 'Users' or $current_controller == 'Devices' or $current_controller == 'Profiles' or $current_controller == 'Blacklist'): ?> <li class="nav-item has-treeview menu-open"> <?php else: ?> <li class="nav-item has-treeview"> <?php endif ?>
            <a href="#" class="nav-link"><i class="nav-icon fas fa-globe"></i>
              <p><?= __('Connection settings') ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="/blacklist" class="nav-link"><i class="nav-icon fa fa-minus-circle"></i><p><?= __('Blacklist') ?></p></a></li>
              <li class="nav-item"><a href="/profiles" class="nav-link"><i class="nav-icon fa fa-sliders-h"></i><p><?= __('Profiles') ?></p></a></li>
              <li class="nav-item"><a href="/users" class="nav-link"><i class="nav-icon fa fa-user"></i><p><?= __('Users') ?></p></a></li>
              <li class="nav-item"><a href="/devices" class="nav-link"><i class="nav-icon fa fa-tablet"></i><p><?= __('Devices') ?></p></a></li>
            </ul>
          </li>


          <?php if($current_controller == 'Config'): ?> <li class="nav-item has-treeview menu-open"> <?php else: ?> <li class="nav-item has-treeview"> <?php endif ?>
            <a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i>
              <p><?= __('System settings') ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="/config/network" class="nav-link"><i class="nav-icon fa fa-laptop"></i><p><?= __('Network') ?></p></a></li>
              <li class="nav-item"><a href="/config/datetime" class="nav-link"><i class="nav-icon fa fa-clock"></i><p><?= __('Date and time') ?></p></a></li>
              <li class="nav-item"><a href="/config/dhcp" class="nav-link"><i class="nav-icon fa fa-server"></i><p><?= __('DHCP') ?></p></a></li>
              <li class="nav-item"><a href="/config/wifiap" class="nav-link"><i class="nav-icon fa fa-wifi"></i><p><?= __('Wireless Access Point') ?></p></a></li>
              <li class="nav-item"><a href="/config/captiveportal" class="nav-link"><i class="nav-icon fa fa-road"></i><p><?= __('Captive portal') ?></p></a></li>
              <li class="nav-item"><a href="/config/certificate" class="nav-link"><i class="nav-icon fa fa-certificate"></i><p><?= __('SSL Certificate') ?></p></a></li>
              <li class="nav-item"><a href="/config/misc" class="nav-link"><i class="nav-icon fa fa-cog"></i><p><?= __('Miscellaneous') ?></p></a></li>
            </ul>
          </li>


          <?php if($current_controller == 'Tools'): ?> <li class="nav-item has-treeview menu-open"> <?php else: ?> <li class="nav-item has-treeview"> <?php endif ?>
            <a href="#" class="nav-link"><i class="nav-icon fas fa-wrench"></i>
              <p><?= __('Tools & diagnostics') ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="/tools/services" class="nav-link"><i class="nav-icon fas fa-power-off"></i><p><?= __('Services and power') ?></p></a></li>
              <li class="nav-item"><a href="/tools/domain-issue" class="nav-link"><i class="nav-icon fas fa-check-square"></i><p><?= __('Domain check') ?></p></a></li>
              <li class="nav-item"><a href="/tools/system-state" class="nav-link"><i class="nav-icon fas fa-heartbeat"></i><p><?= __('System state') ?></p></a></li>
              <li class="nav-item"><a href="/tools/update?step=1" class="nav-link"><i class="nav-icon fas fa-cloud-download-alt"></i><p><?= __('Update') ?></p></a></li>
            </ul>
          </li>

          <?php if($current_controller == 'Help'): ?> <li class="nav-item has-treeview menu-open"> <?php else: ?> <li class="nav-item has-treeview"> <?php endif ?>
            <a href="#" class="nav-link"><i class="nav-icon fa fa-question-circle"></i>
              <p><?= __('Help') ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="http://keexybox.org" target="_blank" class="nav-link"><i class="nav-icon fa fa-external-link-alt"></i><p><?= __('Website') ?></p></a></li>
              <li class="nav-item"><a href="http://wiki.keexybox.org" target="_blank" class="nav-link"><i class="nav-icon fa fa-book"></i><p><?= __('Documentation') ?></p></a></li>
              <li class="nav-item"><a href="http://keexybox.org/donate" target="_blank" class="nav-link"><i class="nav-icon fa fa-star"></i><p><?= __('Donate') ?></p></a></li>
              <li class="nav-item"><a href="/help/licenses" class="nav-link"><i class="nav-icon fa fa-file-alt"></i><p><?= __('Licenses') ?></p></a></li>
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
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <?= $this->element('copyright') ?>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      KeexyBox - <?= __("The box to keep the Internet under your control") ?>
    </div>
  </footer>

</div>
<!-- ./wrapper -->

</body>
</html>
