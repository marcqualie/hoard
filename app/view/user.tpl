<div class="container">

	<div class="row">
		
		<div class="span4">
			<h4>Details</h4>
			<br/>
			<table class="table table-bordered table-condensed table-striped">
<?php foreach ($page->user as $k => $v): if ($k !== 'password'): ?>
				<tr>
					<td><?=$k?></td>
					<td><?=(String) $v?></td>
				</tr>
<?php endif; endforeach; ?>
			</table>
		</div>
			
		<div class="span4">
			<h4>Bucket Access</h4>
			<br/>
<?php if (count($user_apps) > 0): ?>
			<table class="table table-bordered table-condensed table-striped">
<?php foreach ($user_apps as $app): ?>
				<tr>
					<td><a href="/bucket/<?=$app['appkey']?>"><?=$app['name']?></a></td>
					<td style="text-align:center"><?=$app['roles'][$page->id]?></td>
					<td width="10"><a href="?action=revoke-app-access&amp;bucket=<?=$app['appkey']?>"><i class="icon icon-trash"></i></a></td>
				</tr>
<?php endforeach; ?>
			</table>
<?php else: ?>
			<div class="alert alert-info">This user can't access any buckets</div>
<?php endif; ?>
			
			<form action="<?=$_SERVER['REQUEST_URI']?>" class="clearfix" method="post">
				<input type="hidden" name="action" value="grant-app-access"/>
				<select name="bucket" class="span2">
					<option value="0">-- Grant Access --</option>
<?php foreach ($apps as $app): ?>
					<option value="<?=$app['appkey']?>"><?=$app['name']?></option>
<?php endforeach; ?>
				</select>
				<select name="role" class="span1">
					<option>read</option>
					<option>write</option>
					<option>admin</option>
					<option>owner</option>
				</select>
				<input type="submit" class="span1 btn btn-primary" value="Grant"/>
			</form>
			
		</div>
			
		<div class="span4">
			<h4>Password</h4>
			<br/>
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="clearfix">
				<input type="hidden" name="action" value="change-password"/>
				<input type="text" name="password" value="" placeholder="Password" class="pull-left"/>
				<input type="submit" class="btn btn-primary pull-left" value="Update"/>
			</form>
		</div>
	
	</div>

</div>
