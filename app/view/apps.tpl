<div class="container">
	
	<table class="table table-condensed table-bordered">
		<thead>
			<th>Name</th>
			<th width="100" class="align-center">AppKey</th>
			<th width="300" colspan="2" class="align-center">Events</th>
		</thead>
		<tbody>
<?php foreach ($apps as $app): ?>
			<tr>
				<td>
					<a href="/app/<?php echo $app['appkey']; ?>">
						<?=$app['name']?>
					</a>
				</td>
				<td class="align-center monospace"><?=$app['appkey']?></td>
				<td width="150" class="align-center"><a href="/viewer/#appkey=<?=$app['appkey']?>"><?=number_format($app['records'])?></a></td>
				<td width="150" class="align-center"><?=round($app['rps'], 2)?> / s</td>
			</tr>
<? endforeach; ?>
		</tbody>
	</table>

	<div class="clearfix">
		<div class="pull-right input-prepend input-append">
			<form action="/apps/new" method="post">
				<span class="add-on">New App</span><?
				?><input type="text" name="app-name" value="" class="span3" placeholder=""/><?
				?><button class="btn btn-primary"><i class="icon-plus-sign icon-white"></i></button>
			</form>
		</div>
	</div>
	
</div>