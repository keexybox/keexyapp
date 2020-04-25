<div class="row">
    <div class="col-sm-12 alert alert-success">
         <form action=<?= h($params['link']) ?>>
	      <strong><?= h($message) ?> updated successfully</strong>
	      &nbsp;&nbsp;
              <input class="btn btn-success" type="submit" value="<?=__("Clic here to reload")." ".h($message) ?>">
         </form>
    </div>
</div>
