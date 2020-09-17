<!DOCTYPE html>
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
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-keexybox layout-top-nav">
<div class="wrapper">
  <!-- Main Header -->
  <header class="main-header">

    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Logo -->
      <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b><font color="#33cc33">K</font><font color="#ff00ff">x</font></b><font color="#ff8533">B</font></span>
        <!-- logo for regular state and mobile devices -->
        <!-- <span class="logo-lg"><b>Keexy</b>BOX</span> -->
        <span class="logo-lg"><img src="/img/logo-keexybox.png" class="logo-image" alt="KeexyBox"></span>
                <!-- <img src="/adminlte/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
      </a>
  
      <!-- Header Navbar -->
      <!-- Sidebar toggle button-->
	  <!--
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
	  -->
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
                    <small><?= __('Name: {0}', $lo_client['session_details']['Auth']['User']['displayname']) ?></small>
                    <small><?= __('Login: {0}', $lo_client['session_details']['Auth']['User']['username']) ?></small>
			      <?php else: ?>
				    <?= __('Click on Manage to edit your account or manage KeexyBox') ?>
			      <?php endif ?>
				</p>
			  </li>
              <li class="user-footer">
		        <?php if(isset($lo_client['session_status'])): ?>
                  <div class="pull-left">
                    <a href="/users/logout" class="btn btn-default btn-flat"><?= __('Quit') ?></a>
                  </div>
                  <div class="pull-right">
                    <a href="/users/adminlogin" class="btn btn-default btn-flat"><?= __('Manage') ?></a>
                  </div>
			      <?php else: ?>
                  <div class="pull-right">
                    <a href="/users/adminlogin" class="btn btn-default btn-flat"><?= __('Manage') ?></a>
                  </div>
			    <?php endif ?>
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
                  <?= __('Connected to the Internet') ?>
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

  <!-- Full Width Column -->
  <div class="content-wrapper">
    <section class="content container-fluid">

				<?= $this->Flash->render() ?>
				<?= $this->fetch('content') ?>
    </section>
    <!-- /.container -->
  </div>
  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
	  KeexyBox - <?= __("The box to keep the Internet under your control") ?>
    </div>
    <!-- Default to the left -->
    <?= $this->element('copyright') ?>
  </footer>
  </footer>
</div>
<!-- ./wrapper -->

</body>
</html>
