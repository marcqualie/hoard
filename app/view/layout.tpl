<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title><?= $title ?: 'Hoard' ?></title>
	<link href="/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="/css/global.css" rel="stylesheet"/>
	<script src="/js/jquery.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>
	<script src="/js/highcharts.min.js"></script>
	<script src="/js/global.js"></script>
	<script src="/js/dashboard.js"></script>
</head>
<body>
	
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
		<a class="navbar-brand" href="/">Hoard</a>
<?php if (Auth::$id): ?>
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<div class="nav-collapse collapse navbar-responsive-collapse">
			<ul class="nav navbar-nav">
				<li<?=PAGE === 'home' ? ' class="active"' : ''?>><a href="/">Buckets (<?=count(Auth::$buckets)?>)</a></li>
				<li<?=PAGE === 'viewer' ? ' class="active"' : ''?>><a href="/viewer/">Viewer</a></li>
				<li<?=PAGE === 'mapreduce' ? ' class="active"' : ''?>><a href="/mapreduce/">Map Reduce</a></li>
			</ul>
			<ul class="nav navbar-nav pull-right">
<?php if (Auth::$admin): ?>
				<li<?=PAGE === 'admin' ? ' class="active"' : ''?>><a href="/admin/">Admin</a></li>
<?php endif; ?>
				<li<?=PAGE === 'account' ? ' class="active"' : ''?>><a href="/account/"><?= Auth::$user['email'] ?></a></li>
				<li><a href="/logout/">Logout</a></li>
			</ul>
			</ul>
<?php endif; ?>
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
