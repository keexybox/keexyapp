<div class="row">
    <div class="col-sm-12 alert alert-success">
         <form action=<?= h($params['restartlink']) ?>>
	      <strong><?= h($message) ?> updated successfully</strong>
	      &nbsp;&nbsp;
              <input class="btn btn-warning" type="submit" value="<?=__("You need to reboot KeexyBox. Reboot now?")?>">
         </form>
    </div>
</div>
