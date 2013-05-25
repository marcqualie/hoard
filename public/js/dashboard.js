var chart_getData
,	chart_dataInterval = 1000
,	chart_dataPeriod = 'minute'
,	chart_dataRequest = null
,	chart_bucket = ''
;

Highcharts.setOptions({
	xAxis: {
		lineWidth: 0,
		tickWidth: 0,
		tickLength: 0,
		gridLineWidth: 1,
		gridLineColor: 'rgba(0, 0, 0, 0)'
	},
	yAxis: {
		gridLineWidth: 1,
		gridLineColor: 'rgba(0, 0, 0, 0)'
	},
	chart: {
		backgroundColor: 'rgba(0, 0, 0, 0)',
		plotBackgroundColor: null,
		plotShadow: false,
		plotBorderWidth: 0
	}
});

$(document).ready(function () {
	
	var dashboard_chart = $('#dashboard_chart');
	if (dashboard_chart.length === 0) return;

	// Options
	var options = {
		chart: {
			renderTo: 'dashboard_chart',
			defaultSeriesType: 'spline',
			animation: false,
			spacingTop: 40,
			spacingRight: 40,
			spacingBottom: 40
		},
		credits: {
			enabled: false
		},
		tooltip: {
			crosshairs: true,
			shared: true
		},
		title: {
			text: false
		},
		xAxis: {
			categories: [],
			labels: {
				enabled: false
			}
		},
		yAxis: {
			title: {
				text: false
			}
		},
		legend: {
			enabled: false
		},
		plotOptions: {
			spline: {
				shadow: false,
				lineWidth: 3
			},
			series: {
				threshold: 0
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

		chart_dataRequest = $.get('/stats?period=' + chart_dataPeriod + '&bucket=' + chart_bucket, function (data) {
			
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
						data: [],
						color: '#428bca',
						enabled: true,
						marker: {
							enabled: false
						}
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

		});
	
	}
	chart_getData();
	setInterval(chart_getData, chart_dataInterval);

});
