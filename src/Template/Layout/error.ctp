<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>KeexyBox | <?= __('The box to keep the Internet under your control') ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="icon" type="image/png" href="kxb-favicon.png" />

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
  
    </nav>
  </header>

  <!-- Full Width Column -->
  <div class="content-wrapper login-page">
    <section class="content container-fluid">

      <h4>Oops! that's an error.<br><h4>
      Go back to <a href="/" class="http-link-color-error">home</a>
		
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
