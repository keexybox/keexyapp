<p><?= __('KeexyBox version').": ".$version ?></p>
<legend><?= __('Licenses') ?></legend>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <h4><?= __('KeexyBox license')?></h4>
      <p><?= __('This software is distributed under the {0} license, the source codes of which are in the following directories:', 'GPLv3') ?></p>
	  <ul>
	  <?php foreach($kxb_src_code_dirs as $kxb_src_code_dir): ?>
	  	<li><?= $kxb_src_code_dir?></li>
	  <?php endforeach ?>
	  </ul>
    </div>
  </div>
</div>
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">
<?= nl2br('Copyright (c) 2020, Benoit SAGLIETTO
License GPLv3

KeexyBox is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

KeexyBox is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with KeexyBox. If not, see ') ?><a href='http://www.gnu.org/licenses/' target="_blank">http://www.gnu.org/licenses/</a>.

      </div>
      <div class="box-footer">
      </div><!-- /.box-footer -->
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="callout callout-info">
      <h4><?= __('Extra software packages and licenses')?></h4>
      <p><?= __('KeexyBox software comes with extra software packages.') ?></p>
    </div>
  </div>
</div>
<div class="row">
  <!-- left column -->
  <div class="col-md-6">
    <!-- general form elements -->
    <div class="box box-info">
      <div class="box-header with-border">
      </div>
      <!-- /.box-header -->

      <!-- form start -->
      <div class="box-body">

      <h4>CakePHP 3</h4>
	  <ul>
		<li>License: <a href='https://github.com/cakephp/cakephp/blob/master/LICENSE' target="_blank">https://github.com/cakephp/cakephp/blob/master/LICENSE</a></li>
		<li>Website: <a href='https://cakephp.org/' target="_blank">https://cakephp.org/</a></li>
	  </ul>
      <h4>ISC Bind9</h4>
	  <ul>
		<li>License: <a href='https://www.isc.org/licenses/' target="_blank">https://www.isc.org/licenses/</a></li>
		<li>Website: <a href='https://www.isc.org/bind/' target="_blank">https://www.isc.org/bind/</a></li>
	  </ul>
      <h4>ISC DHCP</h4>
	  <ul>
		<li>License: <a href='https://www.isc.org/licenses/' target="_blank">https://www.isc.org/licenses/</a></li>
		<li>Website: <a href='https://www.isc.org/dhcp/' target="_blank">https://www.isc.org/dhcp/</a></li>
	  </ul>
      <h4>Tor</h4>
	  <ul>
		<li>License: <a href='https://gitweb.torproject.org/tor.git/plain/LICENSE' target="_blank">https://gitweb.torproject.org/tor.git/plain/LICENSE</a></li>
		<li>Website: <a href='https://www.torproject.org/' target="_blank">https://www.torproject.org/</a></li>
	  </ul>
      <h4>AdminLTE</h4>
	  <ul>
		<li>License: <a href='https://adminlte.io/docs/2.4/license' target="_blank">https://adminlte.io/docs/2.4/license</a></li>
		<li>Website: <a href='https://adminlte.io/' target="_blank">https://adminlte.io/</a></li>
	  </ul>
      <h4>Bootstrap</h4>
	  <ul>
		<li>License: <a href='https://getbootstrap.com/docs/4.0/about/license/' target="_blank">https://getbootstrap.com/docs/4.0/about/license/</a></li>
		<li>Website: <a href='https://getbootstrap.com/' target="_blank">https://getbootstrap.com/</a></li>
	  </ul>

      </div>
      <div class="box-footer">
      </div><!-- /.box-footer -->
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
            <a onclick="back_link()" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" title="<?= __('I decline') ?>"></span>&nbsp;<?= __('I decline') ?></a>
            <a onclick="accept_licence()" class="btn btn-success pull-right float-vertical-align"><?= __('I accept') ?>&nbsp;<span class="glyphicon glyphicon-chevron-right" aria-hidden="true" title="<?= __('I accept') ?>"></span></a>
  </div>
</div>
<script>
var urlParams = new URLSearchParams(location.search);
var install_type = urlParams.get('install_type');
function back_link() {
  window.location.href = "/config/wstart?install_type=" + install_type;
}
function accept_licence() {
  window.location.href = "/config/wdatetime?install_type=" + install_type;
}
</script>
