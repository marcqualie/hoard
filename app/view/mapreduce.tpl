<div class="container">
	
	<form action="/<?=PAGE?>" class="row" method="post">
		
		<div class="span3">

			<select name="appkey" class="span3">
				<option value="0">-- Select Your App --</option>
<?php foreach (Auth::$apps as $app): ?>
				<option value="<?=$app['appkey']?>"<?php echo $page->params['appkey'] === $app['appkey'] ? ' selected="true"' : ''?>><?php echo $app['name']?></option>
<?php endforeach; ?>
			</select>
			<textarea name="query" placeholder="Query" class="span3 monospace" rows="1"><?php echo $page->params['query']?></textarea>
			<textarea name="sort" placeholder="Sort" class="span3 monospace" rows="1" style="margin-top:17px"><?=$page->params['sort']?></textarea>
		</div>
		
		<div class="span4">
			<textarea name="map-func" placeholder="Map Function" class="span4 monospace" rows="6"><?php echo $page->params['map-func']?></textarea>
		</div>
		
		<div class="span4">
			<textarea name="reduce-func" placeholder="Reduce Function" class="span4 monospace" rows="6"><?php echo $page->params['reduce-func']?></textarea>
		</div>
		
		<div class="span1">
			<input type="button" class="span1 btn disabled" value="Save"/>
			<input type="button" class="span1 btn btn-inverse disabled" value="Load" style="margin-top:17px"/>
			<input type="submit" class="span1 btn btn-primary" value="Run" style="margin-top:17px"/>
		</div>
		
	</form>

<?php if (isset($output)): ?>
	<table id="output" class="table table-condensed table-striped table-bordered">
<?php foreach ($output as $row): ?>
		<tr>
			<td width="50%"><?php echo htmlentities($row['_id']); ?></td>
			<td><?php echo $row['value']; ?></td>
		</tr>
<?php endforeach; ?>
	</table>
<?php endif; ?>
	
</div>