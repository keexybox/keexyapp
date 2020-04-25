<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>KeexyBox | <?= __('The box to keep the Internet under your control') ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="icon" type="image/png" href="/kxb-favicon.png" />

  <?= $this->element('css_load') ?>
  <?= $this->element('js_load') ?>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->


  <!-- Google Font -->
  <!-- <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-keexybox sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <!--<span class="logo-mini"><b><font color="#33cc33">K</font><font color="#ff00ff">x</font></b><font color="#ff8533">B</font></span>-->
      <span class="logo-mini"><img src="/img/kxb-favicon-white.png" class="logo-image" alt="KeexyBox"></span>
      <!-- logo for regular state and mobile devices -->
      <!-- <span class="logo-lg"><b>Keexy</b>BOX</span> -->
      <span class="logo-lg"><img src="/img/logo-keexybox.png" class="logo-image" alt="KeexyBox"></span>
              <!-- <img src="/adminlte/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

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
				    <?= __('Authenticated to manage KeexyBox') ?>
                    <small><?= __('Login: {0}', $lo_client['session_details']['Auth']['User']['username']) ?></small>
                    <small><?= __('Name: {0}', $lo_client['session_details']['Auth']['User']['displayname']) ?></small>
			      <?php else: ?>
				    <?= __('Not authenticated to manage KeexyBox') ?>
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
                    <a href="/users/login" class="btn btn-default btn-flat"><?= __('Connect') ?></a>
                  </div>
			    <?php endif ?>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <?php if($current_controller == 'Statistics'): ?> <li class="active"> <?php else: ?> <li> <?php endif ?>
		<a href="/statistics"><i class="fa fa-bar-chart"></i> <span><?= __('Statistics') ?></span></a></li>
        <?php if($current_controller == 'Connections'): ?> <li class="active"> <?php else: ?> <li> <?php endif ?>
		<a href="/connections"><i class="fa fa-users"></i> <span><?= __('Connections') ?></span></a></li>

        <?php if($current_controller == 'Users' or $current_controller == 'Devices' or $current_controller == 'Profiles' or $current_controller == 'Blacklist'): ?> <li class="treeview active"> <?php else: ?> <li class="treeview"> <?php endif ?>
          <a href="#"><i class="fa fa-globe"></i> <span><?= __('Connection settings') ?></span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/blacklist"><?= __('Blacklist') ?></a></li>
            <li><a href="/profiles"><?= __('Profiles') ?></a></li>
            <li><a href="/users"><?= __('Users') ?></a></li>
            <li><a href="/devices"><?= __('Devices') ?></a></li>
          </ul>
        </li>

        <?php if($current_controller == 'Config'): ?> <li class="treeview active"> <?php else: ?> <li class="treeview"> <?php endif ?>
          <a href="#"><i class="fa fa-cogs"></i> <span><?= __('System settings') ?></span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/config/network"><?= __('Network') ?></a></li>
            <li><a href="/config/datetime"><?= __('Date and time') ?></a></li>
            <li><a href="/config/dhcp"><?= __('DHCP') ?></a></li>
            <li><a href="/config/certificate"><?= __('SSL Certificate') ?></a></li>
            <li><a href="/config/misc"><?= __('Miscellaneous') ?></a></li>
          </ul>
        </li>

        <?php if($current_controller == 'Tools'): ?> <li class="treeview active"> <?php else: ?> <li class="treeview"> <?php endif ?>
          <a href="#"><i class="fa fa-wrench"></i> <span><?= __('Tools & diagnostics') ?></span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="/tools/services"><?= __('Services and power') ?></a></li>
            <li><a href="/tools/domain-issue"><?= __('Domain check') ?></a></li>
            <li><a href="/tools/system-state"><?= __('System state') ?></a></li>
          </ul>
        </li>

        <?php if($current_controller == 'Help'): ?> <li class="treeview active"> <?php else: ?> <li class="treeview"> <?php endif ?>
          <a href="#"><i class="fa fa-question-circle"></i> <span><?= __('Help') ?></span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="http://keexybox.org" target="_blank"><?= __('Website') ?></a></li>
            <li><a href="http://wiki.keexybox.org" target="_blank"><?= __('Documentation') ?></a></li>
            <li><a href="http://keexybox.org/donate" target="_blank"><?= __('Donate') ?></a></li>
            <li><a href="/help/licenses"><?= __('Licenses') ?></a></li>
          </ul>
        </li>

      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content container-fluid">

	<!-- PAGE CONTENT -->

				<?= $this->Flash->render() ?>
				<?= $this->fetch('content') ?>

	<!-- END PAGE CONTENT -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
	  KeexyBox - <?= __("The box to keep the Internet under your control") ?>
    </div>
    <!-- Default to the left -->
    <?= $this->element('copyright') ?>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:;">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:;">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="pull-right-container">
                    <span class="label label-danger pull-right">70%</span>
                  </span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
  immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>
</html>
