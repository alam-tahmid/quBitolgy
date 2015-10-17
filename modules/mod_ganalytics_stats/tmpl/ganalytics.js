gajQuery(document).ready(function() {
	
	gajQuery('input[name=mod_date_from]').change(function() {
		gajQuery(this).closest('.mod-ganalytics-table').parent().find('.mod-ga-chart').gaChart('refresh', {
			start: gajQuery(this).val()
		});
	});
	gajQuery('input[name=mod_date_to]').change(function() {
		gajQuery(this).closest('.mod-ganalytics-table').parent().find('.mod-ga-chart').gaChart('refresh', {
			end: gajQuery(this).val()
		});
	});
	
	gajQuery('.date-range-button').click(function(){
		var range = 'day';
		var cl = gajQuery(this).attr('class');
		var parent = gajQuery(this).parent().parent();
		if(cl.indexOf('date-range-day') >= 0){
			range = 'day';
			parent.find('.date-range-day').attr('src', parent.find('.date-range-day').attr('src').replace('day-disabled-32.png', 'day-32.png'));
			parent.find('.date-range-week').attr('src', parent.find('.date-range-week').attr('src').replace('week-32.png', 'week-disabled-32.png'));
			parent.find('.date-range-month').attr('src', parent.find('.date-range-month').attr('src').replace('month-32.png', 'month-disabled-32.png'));
		}
		if(cl.indexOf('date-range-week') >= 0){
			range = 'week';
			parent.find('.date-range-day').attr('src', parent.find('.date-range-day').attr('src').replace('day-32.png', 'day-disabled-32.png'));
			parent.find('.date-range-week').attr('src', parent.find('.date-range-week').attr('src').replace('week-disabled-32.png', 'week-32.png'));
			parent.find('.date-range-month').attr('src', parent.find('.date-range-month').attr('src').replace('month-32.png', 'month-32.png'));
		}
		if(cl.indexOf('date-range-month') >= 0){
			range = 'month';
			parent.find('.date-range-day').attr('src', parent.find('.date-range-day').attr('src').replace('day-32.png', 'day-disabled-32.png'));
			parent.find('.date-range-week').attr('src', parent.find('.date-range-week').attr('src').replace('week-32.png', 'week-disabled-32.png'));
			parent.find('.date-range-month').attr('src', parent.find('.date-range-month').attr('src').replace('month-disabled-32.png', 'month-32.png'));
		}
		gajQuery(this).parent().parent().find('.mod-ga-chart').gaChart('refresh', {
			dateRange: range
		});
	});
});

function showModCharts(){
	gajQuery('.mod-ga-chart').gaChart('refresh', {
		start: gajQuery('input[name=mod_date_from]').val(),
		end: gajQuery('input[name=mod_date_to]').val()
	});
}