<div class="container">

	<div class="row">

		<div class="span6">
			<h5>Information</h5>
			<table class="table table-striped table-condensed">
				<tr>
					<td>Name</td>
					<td class="align-right"><?= $app['name'] ?></td>
				</tr>
				<tr>
					<td>Appkey</td>
					<td class="align-right"><?= $app['appkey'] ?></td>
				</tr>
				<tr>
					<td>Secret</td>
					<td class="align-right"><?= $app['secret'] ?></td>
				</tr>
				<tr>
					<td>User(s)</td>
					<td class="align-right">
<?php foreach ($app['roles'] as $uid => $role): $user = MongoX::selectCollection('user')->findOne(array('_id' => new MongoId($uid))); ?>
						<?= $user['email'] ?> 
<?php endforeach; ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="span6">
			<h5>Events</h5>
			<table class="table table-striped table-condensed">
				<tr>
					<td>Past Minute</td>
					<td class="align-right"><?= number_format($app['records_1minute']) ?></td>
				</tr>
				<tr>
					<td>Past Hour</td>
					<td class="align-right"><?= number_format($app['records_1hour']) ?></td>
				</tr>
				<tr>
					<td>Past Day</td>
					<td class="align-right"><?= number_format($app['records_1day']) ?></td>
				</tr>
				<tr>
					<td>All</td>
					<td class="align-right"><?= number_format($app['records_all']) ?></td>
				</tr>
			</table>
		</div>

	</div>

	<div class="row">

		<div class="span6">
			<h5>Settings</h5>
			<table class="table table-striped table-condensed">
				<tr>
					<td>Expire</td>
					<td class="align-right"><span style="color:#999">never</span></td>
				</tr>
				<tr>
					<td>Cache</td>
					<td class="align-right"><span style="color:#999">never</span></td>
				</tr>
				<tr>
					<td>Empty</td>
					<td class="align-right"><a href="/app/<?= $app['appkey'] ?>/empty" onclick="if ( ! confirm('Are you sure?')) return false">clear</a></td>
				<tr>
					<td>Delete</td>
					<td class="align-right"><a href="/app/<?= $app['appkey'] ?>/delete" onclick="if ( ! confirm('Are you sure?')) return false">destroy</a></td>
				</tr>
			</table>
		</div>

		<div class="span6">
			<h5>Schema</h5>
			<table class="table table-striped table-condensed">
				<tr>
					<td><span style="color:#999">Coming soon</span></td>
				</tr>
			</table>
		</div>

</div>