var chart_getData
,	chart_dataInterval = 1000
,	chart_dataPeriod = 'minute'
,	chart_dataRequest = null
;

$(document).ready(function () {
	
	var dashboard_chart = $('#dashboard_chart');
	if (dashboard_chart.length === 0) return;

	// Options
	var options = {
		chart: {
			renderTo: 'dashboard_chart',
			defaultSeriesType: 'spline'
		},
		credits: {
			enabled: false
		},
		title: {
			text: 'Tracked Events'
		},
		xAxis: {
			categories: []
		},
		yAxis: {
			title: {
				text: 'Units'
			}
		},
		series: []
	};

	// Create base Chart
	var chart = new Highcharts.Chart(options);

	// Chart generator
	chart_getData = function (p) {

		if (p) chart_dataPeriod = p;
		if (chart_dataRequest) chart_dataRequest.abort();

		chart_dataRequest = $.get('/stats?period=' + chart_dataPeriod, function (data) {
			
			// Split the lines
			var lines = data.split('\n');
			
			// Iterate over the lines and add categories or series
			$.each(lines, function (lineNo, line) {
				var items = line.split(',');
				var ln = lineNo - 1;
				
				// header line containes categories
				if (lineNo < 1) {
					$.each(items, function(itemNo, item) {
						if (itemNo > 0) options.xAxis.categories.push(item);
					});
				}
				
				// Add Data
				else {
					
					var name = items[0];
					items.splice(0, 1);
					var series = {
						id: 'series-' + name,
						name: name,
						data: []
					};
					$.each(items, function (k, v) {
						series.data.push(parseFloat(v));
					});
					var s = chart.get(series.id);
					if ( ! s) {
						chart.addSeries(series, false, false);
					} else {
						s.setData(series.data, false);
					}
			
				}
				
			});
			
			// Create the chart
			chart.redraw(false);
			setTimeout(chart_getData, chart_dataInterval);

		});
	
	}
	chart_getData();

});