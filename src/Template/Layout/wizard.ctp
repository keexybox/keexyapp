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
<body class="hold-transition skin-keexybox sidebar-collapse sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="/img/kxb-favicon-white.png" class="logo-image" alt="KeexyBox"></span>
      <!-- logo for regular state and mobile devices -->
      <!-- <span class="logo-lg"><b>Keexy</b>BOX</span> -->
      <span class="logo-lg"><img src="/img/logo-keexybox.png" class="logo-image" alt="KeexyBox"></span>
              <!-- <img src="/adminlte/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
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
              <!-- <span class="hidden-xs"><?= $lo_connection['name'] ?></span>-->
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <!-- <img src="/adminlte/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">-->
                <p class="user-info">
			      <?php if(isset($lo_client['connection_status'])): ?>
                  <?= __('Connected to the Internet') ?>
                  <small><?= __('Name: {0}', $lo_client['connection_details']['name']) ?> </small>
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
