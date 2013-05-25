<div class="container">

	<div class="pull-left hidden-sm" style="width:300px">
		<form action="/" method="post" class="form-inline">
			<input type="hidden" name="action" value="create_bucket"/>
			<div class="input-group">
				<input type="text" name="app-name" value="" placeholder="New Bucket Name"/>
				<span class="input-group-btn">
					<button class="btn btn-primary">Create</button>
				</span>
			</div>
		</form>
	</div>

	<div class="pull-right">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td width="100" class="align-center"><?= number_format($totals['rps'], 2)?></td>
					<td width="100" class="align-center"><?= number_format($totals['records'])?></td>
					<td width="100" class="align-center"><?= normalize_bytes($totals['storage'], 2, true) ?></td>
					<td width="100" class="align-center"><?= normalize_bytes($totals['storage_index'], 2, true) ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	
	<table class="table table-condensed table-bordered">
		<tbody>
<?php foreach ($apps as $app): ?>
			<tr>
				<td>
					<a href="/bucket/<?php echo $app['appkey']; ?>">
						<?=$app['name']?>
					</a>
				</td>
				<td width="100" class="align-center hidden-sm"><?= round($app['rps'], 2) > 0 ? round($app['rps'], 2) : '-' ?></td>
				<td width="100" class="align-center"><a href="/viewer/#bucket=<?=$app['appkey']?>"><?= number_format($app['records']) ?></a></td>
				<td width="100" class="align-center"><?= normalize_bytes($app['storage'], 2, true) ?></td>
				<td width="100" class="align-center hidden-sm"><?= normalize_bytes($app['storage_index'], 2, true) ?></td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>
	
</div>
