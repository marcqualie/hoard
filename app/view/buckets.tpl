<div class="container">
	
	<table class="table table-condensed table-bordered">
		<thead>
			<th>Name</th>
			<th width="100" class="align-center">ID</th>
			<th width="200" colspan="2" class="align-center">Events</th>
			<th width="200" colspan="2" class="align-center">Storage</th>
		</thead>
		<tbody>
<?php foreach ($apps as $app): ?>
			<tr>
				<td>
					<a href="/bucket/<?php echo $app['appkey']; ?>">
						<?=$app['name']?>
					</a>
				</td>
				<td class="align-center monospace"><?=$app['appkey']?></td>
				<td width="100" class="align-center"><a href="/viewer/#appkey=<?=$app['appkey']?>"><?=number_format($app['records'])?></a></td>
				<td width="100" class="align-center"><?=$app['rps'] > 0 ? round($app['rps'], 2) : '-'?></td>
				<td width="100" class="align-center"><?=number_format($app['storage'] / 1024 / 1024, 2)?>M</td>
				<td width="100" class="align-center"><?=number_format($app['storage_avg'] / 1024, 2)?>K</td>
			</tr>
<?php endforeach; ?>
		</tbody>
	</table>

	<table class="table table-condensed table-bordered">
		<tbody>
			<tr>
				<td colspan="2"><b>Totals</b></td>
				<td width="100" class="align-center"><?=number_format($totals['records'])?></td>
				<td width="100" class="align-center"><?=number_format($totals['rps'], 2)?></td>
				<td width="100" class="align-center"><?=number_format($totals['storage'] / 1024 / 1024)?>M</td>
				<td width="100" class="align-center">-</td>
			</tr>
		</tbody>
	</table>

	<div class="clearfix">
		<div class="pull-right input-prepend input-append">
			<form action="/buckets/new" method="post">
				<span class="add-on">New Bucket</span><?
				?><input type="text" name="app-name" value="" class="span3" placeholder=""/><?
				?><button class="btn btn-primary"><i class="icon-plus-sign icon-white"></i></button>
			</form>
		</div>
	</div>
	
</div>