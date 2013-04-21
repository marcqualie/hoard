<div id="viewer">

	<form action="javascript:void(0)" id="viewer-form" class="viewer-head clearfix">
		
		<div class="container">
		
			<div class="pull-left clearfix">
				
				<div class="pull-left input-prepend margin-right">
					<select name="bucket" class="span2">
						<option value="0">-- Select Bucket --</option>
<?php foreach (Auth::$buckets as $bucket): ?>
						<option value="<?=$bucket['appkey']?>"><?= $bucket['name'] ?></option>
<?php endforeach; ?>
					</select>
				</div>
				
				<div class="input-prepend pull-left margin-right">
					<span class="add-on">Query</span><?
					?><input type="text" name="query" value="" placeholder='{}' class="span2"/>
				</div>
				
				<div class="input-prepend pull-left margin-right">
					<span class="add-on">Fields</span><?
					?><input type="text" name="fields" value="" placeholder='All Fields' class="span2"/>
				</div>
				
				<div class="input-prepend pull-left margin-right">
					<span class="add-on">Sort</span><?
					?><input type="text" name="sort" value="" placeholder='{"date": -1}' class="span2"/>
				</div>
				
				<div class="input-prepend pull-left">
					<span class="add-on">Limit</span><?
					?><input type="text" name="limit" value="50" placeholder="10" class="span1"/>
				</div>
			
			</div>
			
			<div class="pull-right">
				<input type="submit" value="Run" class="btn btn-primary"/>
			</div>
			
		</div>

	</form>

	<div id="viewer-content" class="container">

	</div>

</div>
