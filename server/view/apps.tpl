<div class="container">
	
	<div class="clearfix">
		<div class="pull-right input-prepend input-append">
			<form action="/apps/new" method="post">
				<span class="add-on">New App</span><?
				?><input type="text" name="app-name" value="" class="span3" placeholder=""/><?
				?><button class="btn btn-primary"><i class="icon-plus-sign icon-white"></i></button>
			</form>
		</div>
	</div>
	
	<table class="table table-condensed table-bordered">
		<thead>
			<th>Name</th>
			<th>Role</th>
			<th width="100">AppKey</th>
			<th width="300">Secret</th>
			<th width="100">Events</th>
		</thead>
		<tbody>
<?php foreach ($apps as $app): ?>
			<tr>
				<td><?=$app['name']?></td>
				<td><?=$app['roles'][Auth::$id]?></td>
				<td class="monospace"><?=$app['appkey']?></td>
				<td class="monospace"><?=$app['secret']?></td>
				<td><a href="/viewer/#appkey=<?=$app['appkey']?>"><?=number_format($app['records'])?></a></td>
			</tr>
<? endforeach; ?>
		</tbody>
	</table>
	
</div>