<!DOCTYPE html>
<html>
<head>
	<title><?=$title?></title>
	<link href="<?=URIBASE?>/css/lib/bootstrap/min.css" rel="stylesheet"/>
	<link href="<?=URIBASE?>/css/lib/bootstrap/responsive.min.css" rel="stylesheet"/>
	<link href="<?=URIBASE?>/css/src/global.css" rel="stylesheet"/>
	<script src="<?=URIBASE?>/js/lib/jquery/min.js"></script>
	<script src="<?=URIBASE?>/js/lib/bootstrap/min.js"></script>
	<script src="<?=URIBASE?>/js/src/global.js"></script>
	<script>
	  app.uribase = '<?=URIBASE?>';
	</script>
</head>
<body>
	
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a href="<?=URIBASE?>/" class="brand">Hoard</a>
			<ul class="nav pull-left">
<? if (Auth::$id): ?>
				<li<?=PAGE === 'apps' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/apps">My Apps (<?=count(Auth::$apps)?>)</a></li>
<? endif; ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Tools
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li><a href="<?=URIBASE?>/viewer">Viewer</a></li>
						<li><a href="<?=URIBASE?>/mapreduce">Map Reduce</a></li>
					</ul>
				</li>
				<li<?=PAGE === 'docs' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/docs">Documentation</a></li>
				<li<?=PAGE === 'contact' ? ' class="active"' : ''?>><a href="http://www.marcqualie.com/contact/" target="_blank">Support</a></li>
			</ul>
			<div class="nav-collaps">
				<ul class="nav pull-right">
<? if (Auth::$id): ?>
<? if (Auth::$admin): ?>
					<li<?=PAGE === 'admin' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/admin">Admin</a></li>
<? endif; ?>
					<li<?=PAGE === 'account' ? ' class="active"' : ''?>><a href="<?=URIBASE?>/account"><?=Auth::$user['email']?></a></li>
					<li><a href="<?=URIBASE?>/logout">Logout</a></li>
<? else: ?>
					<li<?=PAGE === 'register' ? ' class="active"' : ''?>><a href="/register">Register</a></li>
					<li class="dropdown">
						<a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <b class="caret"></b></a>
						<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
							<form action="<?=URIBASE?>/login" method="post" class="clearfix">
								<label><input type="text" name="email" value="" placeholder="Email Address"/></label>
								<label><input type="password" name="password" value="" placeholder="Password"/></label>
								<div class="pull-left">
									<label class="checkbox"><input type="checkbox" name="remember_me" value=""/> Remember Me</label>
								</div>
								<div class="pull-right">
									<input type="submit" value="Login" class="btn btn-primary"/>
								</div>
							</form>
						</div>
					</li>
<? endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<? if ($page->alert_data['message']): ?>
<div class="container">
	<div class="alert alert-<?=$page->alert_data['type']?>"><?=$page->alert_data['message']?></div>
</div>
<? endif; ?>

<?=$html?>

</body>
</html>