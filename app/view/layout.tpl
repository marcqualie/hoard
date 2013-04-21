<!DOCTYPE html>
<html>
<head>
	<title><?=isset ($title) ? $title : 'Hoard'?></title>
	<link href="<?=URIBASE?>/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="<?=URIBASE?>/css/src/global.css" rel="stylesheet"/>
	<script src="<?=URIBASE?>/js/jquery.min.js"></script>
	<script src="<?=URIBASE?>/js/bootstrap.min.js"></script>
	<script src="<?=URIBASE?>/js/highcharts.min.js"></script>
	<script src="<?=URIBASE?>/js/src/global.js?t=<?php echo filemtime(WEBROOT . '/js/src/global.js') ?>"></script>
	<script src="<?=URIBASE?>/js/src/dashboard.js"></script>
	<script>
	  app.uribase = '<?php echo URIBASE; ?>';
	</script>
</head>
<body>
	
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a href="<?=URIBASE?>/" class="brand">Hoard</a>
			<ul class="nav pull-left">
<?php if (Auth::$id): ?>
				<li<?=PAGE === 'apps' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/buckets/">Buckets (<?=count(Auth::$apps)?>)</a></li>
<?php endif; ?>
				<li<?=PAGE === 'viewer' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/viewer/">Viewer</a></li>
				<li<?=PAGE === 'mapreduce' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/mapreduce/">Map Reduce</a></li>
			</ul>
			<div class="nav-collaps">
				<ul class="nav pull-right">
<?php if (Auth::$id): ?>
<?php if (Auth::$admin): ?>
					<li<?=PAGE === 'admin' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/admin/">Admin</a></li>
<?php endif; ?>
					<li<?=PAGE === 'account' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/account/"><?= Auth::$user['email'] ?></a></li>
					<li><a href="<?=URIBASE?>/logout/">Logout</a></li>
<?php else: ?>
					<li class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <b class="caret"></b></a>
						<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
							<form action="<?=URIBASE?>/login/" method="post" class="clearfix">
								<label><input type="text" name="email" value="" placeholder="Email Address" class="span3"/></label>
								<label><input type="password" name="password" value="" placeholder="Password"/></label>
								<div class="pull-right">
									<input type="submit" value="Login" class="btn btn-primary"/>
								</div>
							</form>
						</div>
					</li>
<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php if (isset($page->alert_data['message'])): ?>
<div class="container">
	<div class="alert alert-<?=$page->alert_data['type']?>"><?=$page->alert_data['message']?></div>
</div>
<?php endif; ?>

<?php echo $html; ?>

</body>
</html>
