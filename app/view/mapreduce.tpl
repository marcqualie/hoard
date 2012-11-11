<div class="container">
	
	<form action="/<?=PAGE?>" class="row" method="post">
		
		<div class="span3">

			<select name="appkey" class="span2">
				<option value="0" class="span3">-- Select Your App --</option>
<?php foreach (Auth::$apps as $app): ?>
				<option value="<?=$app['appkey']?>"<?php echo $page->prams['appkey'] === $app['appkey'] ? ' selected="true"' : ''?>><?=$app['name']?></option>
<?php endforeach; ?>
			</select>
			<textarea name="query" placeholder="Query" class="span3 monospace" rows="1"><?=$page->params['query']?></textarea>
			<textarea name="sort" placeholder="Sort" class="span3 monospace" rows="1" style="margin-top:17px"><?=$page->params['sort']?></textarea>
		</div>
		
		<div class="span4">
			<textarea name="map-func" placeholder="Map Function" class="span4 monospace" rows="6"><?=$page->params['map-func']?></textarea>
		</div>
		
		<div class="span4">
			<textarea name="reduce-func" placeholder="Reduce Function" class="span4 monospace" rows="6"><?=$page->params['reduce-func']?></textarea>
		</div>
		
		<div class="span1">
			<input type="button" class="span1 btn disabled" value="Save"/>
			<input type="button" class="span1 btn btn-inverse disabled" value="Load" style="margin-top:17px"/>
			<input type="submit" class="span1 btn btn-primary" value="Run" style="margin-top:17px"/>
		</div>
		
	</form>

<? if ($output): ?>
	<table id="output" class="table table-condensed table-striped table-bordered">
<? foreach ($output as $row): ?>
		<tr>
			<td width="50%"><?=htmlentities($row['_id'])?></td>
			<td><?=$row['value']?></td>
		</tr>
<? endforeach; ?>
	</table>
<? endif; ?>
	
</div>