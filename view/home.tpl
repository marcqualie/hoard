<?php if (Auth::$id): ?>

<div class="container">

	<div id="dashboard_chart" style="width:100%;height:300px;text-align:center;line-height:300px">
		loading..
	</div>

	<br/>
	<div class="clearfix">
		<div class="btn-group pull-right" data-toggle="buttons-radio">
			<button type="button" class="btn" onclick="chart_getData('second')">Second</button>
			<button type="button" class="active btn" onclick="chart_getData('minute')">Minute</button>
			<button type="button" class="btn" onclick="chart_getData('hour')">Hour</button>
			<button type="button" class="btn" onclick="chart_getData('day')">Day</button>
		</fib>
	</div>

</div>

<?php else: ?>
<div class="container">
	
	Hello, welcome to <a href="https://github.com/marcqualie/hoard">Hoard</a>
	by <a href="http://www.marcqualie.com/">Marc Qualie</a>
</div>
<?php endif; ?>