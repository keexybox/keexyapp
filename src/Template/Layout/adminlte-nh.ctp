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

</head>

<body>

	<?= $this->Flash->render() ?>
	<?= $this->fetch('content') ?>

</body>
</html>
