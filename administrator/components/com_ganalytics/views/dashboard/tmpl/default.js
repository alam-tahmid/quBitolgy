gajQuery(document).ready(function() {
	// add the tabs
	gajQuery("#tabs").tabs({
		cookie: {expires: 30},
		tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'></span></li>",
		add: function(event, ui) {
			gajQuery(ui.panel).append('<table width="100%" class="chart-table"><tr><td></td></tr>');
		},
		activate: function(event, ui) {
			if(getActiveTab().find('.ganalytics_chart').children().length > 0){
				return;
			}
			showCharts();
		}
	});
	
	// group manipulation
	gajQuery("#add-tab-button").click(function() {
		gajQuery.fancybox({
			width:500,
			height:115,
			content : gajQuery("#dialog-group"),
			autoDimensions : false,
			transitionIn : 'elastic',
			transitionOut : 'elastic',
			speedIn : 600,
			speedOut : 200,
			onStart: function() {
				gajQuery("#dialog-group").show();
				gajQuery("#tab_title").focus();
			},
			onClosed: function() {
				gajQuery("#dialog-group").hide();
				gajQuery('#tab_title').val('');
			}
		});
		gajQuery("#dialog-group-save").click(function(){
			gajQuery.ajax({
				type: 'POST',
				url: 'index.php?option=com_ganalytics&task=dashboard.addgroup',
				data: {name:  gajQuery("#tab_name").val() || "Group"},
				success: function (data) {
					var json = showAjaxMessage(data);
					if(json.status == 'success'){
						window.location.hash="";
						window.location.href = window.location.href.replace('#', '')+'#tabs-' + json.id;
						window.location.reload(true);
					}
				}
			});
		});
	});

	gajQuery("#tabs > ul > li").on("click", 'span.ui-icon-close', function() {
		gajQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_ganalytics&task=dashboard.deletegroup',
			data: {id:  gajQuery(this).parent().children('a').attr('href').replace('#tabs-')},
			success: function (data) {
				var json = showAjaxMessage(data);
				if(json.status == 'success'){
					var table = gajQuery("#tabs").find("#tabs-"+json.id);
					gajQuery("#" + table.attr('aria-labelledby')).parent().remove();
					var panelId = table.remove();
			        gajQuery("#tabs").tabs("refresh");
				}
			}
		});
	});

	// widget stuff
	makeSortable();
	
	gajQuery(".portlet")
		.addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
		.find(".portlet-header")
		.addClass("ui-widget-header ui-corner-all")
		.prepend("<span class='ui-icon ui-icon-gear'></span>")
		.prepend("<span class='ui-icon ui-icon-closethick'></span>")
		.prepend("<span class='ui-icon ui-icon-minusthick'></span>")
		.end().find(".portlet-content");

	gajQuery(".chart-table > tbody > tr > td").disableSelection();
	
	gajQuery('body').on('change', '.inputbox', function() {
		var form = gajQuery(this).parents('form');
		if(form.attr('name') != 'adminForm'){
			return;
		};
		gajQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_ganalytics&task=dashboard.savewidget',
			data: form.serialize(),
			success: function (data) {
				var json = showAjaxMessage(data);
				if(json.status == 'success'){
					form.gaChart('refresh');
					form.parent().parent().find('.portlet-title').text(form.find('input[name=name]').val());
				}
			}
		});
	});
	
	gajQuery(".portlet-header").on('click', '.ui-icon-gear', function() {
		var form = gajQuery(this).parent().parent().find(".adminform");
		if(!form.is(':visible') && form.find('.dimensionscombo option').length < 2){
			var combo = form.find('.dimensionscombo');
			var value = combo.find('option').val().split(',');
			
			combo.multiselect2side('destroy');
			combo.replaceWith(gajQuery('#dialog-widget .dimensionscombo').clone());
			
			gajQuery.each(value, function(index, item) {
				form.find('.dimensionscombo').find("[value='" + item + "']").attr('selected', true);
			});

			createMultiSelectCombo(form.find('.dimensionscombo'));
		}
		if(!form.is(':visible') && form.find('.metricscombo option').length < 2){
			var combo = form.find('.metricscombo');
			var value = combo.find('option').val().split(',');
			
			combo.multiselect2side('destroy');
			combo.replaceWith(gajQuery('#dialog-widget .metricscombo').clone().val(value));
			
			gajQuery.each(value, function(index, item) {
				form.find('.dimensionscombo').find("[value='" + item + "']").attr('selected', true);
			});
			
			createMultiSelectCombo(form.find('.metricscombo'));
		}
		updateSortCombo(form);
		form.slideToggle("slow");
	});
	
	gajQuery(".portlet-header").on('click', '.ui-icon-minusthick', function() {
		gajQuery(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
		gajQuery(this).parents(".portlet:first").find(".portlet-content").toggle();
	});
	gajQuery(".portlet-header").on('click', '.ui-icon-closethick', function() {
		gajQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_ganalytics&task=dashboard.deletewidget',
			data: {id: gajQuery(this).parent().parent().find('input[name=id]').val()},
			success: function (data) {
				var json = showAjaxMessage(data);
				if(json.status == 'success'){
					gajQuery('#widgetForm-'+json.id).parent().parent().remove();
				}
			}
		});
	});
	
	
	gajQuery(".column-action").on('click', '.ui-icon-calculator', function() {
		var cell = gajQuery(this).parent().parent();
		gajQuery("#dialog-widget").data('gacolumn', cell.parent().children().index(cell));
		
		gajQuery("#dialog-widget").find('.dimensionscombo').multiselect2side('destroy');
		gajQuery("#dialog-widget").find('.metricscombo').multiselect2side('destroy');
		createMultiSelectCombo(gajQuery("#dialog-widget").find('.dimensionscombo'));
		createMultiSelectCombo(gajQuery("#dialog-widget").find('.metricscombo'));
		
		gajQuery.fancybox({
			width:700,
			height:430,
			content : gajQuery("#dialog-widget"),
			autoDimensions : false,
			transitionIn : 'elastic',
			transitionOut : 'elastic',
			speedIn : 600,
			speedOut : 200,
			onStart: function() {
				gajQuery("#dialog-widget").show();
//				gajQuery("#tab_title").focus();
			},
			onClosed: function() {
				gajQuery("#dialog-widget").hide();
//				gajQuery('#tab_title').val('');
			}
		});
		gajQuery("#dialog-widget-save").click(function(){
			gajQuery.ajax({
				type: 'POST',
				url: 'index.php?option=com_ganalytics&task=dashboard.savewidget',
				data: gajQuery("#dialog-widget form").serialize()+'&column='+gajQuery("#dialog-widget").data('gacolumn')+'&group_id='+getActiveTab().attr('id').replace('tabs-', ''),
				success: function (data) {
					var json = showAjaxMessage(data);
					if(json.status == 'success'){
						window.location.reload(true);
					}
					gajQuery.fancybox.close();
				}
			});
		});
	});

	// column stuff
	gajQuery(".column-action").on('click', '.ui-icon-minusthick', function() {
		var cell = gajQuery(this).parent().parent();
		var column = cell.parent().children().index(cell);
		var groupID = cell.parent().parent().parent().attr('id').replace('tabs-', '');
		gajQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_ganalytics&task=dashboard.deletecolumn',
			data: {id: groupID, column: column},
			success: function (data) {
				var json = showAjaxMessage(data);
				if(json.status == 'success'){
					gajQuery('#tabs-'+json.id).children('tbody').children().children('td:eq(' + json.column + ')').remove();
					gajQuery('#tabs-'+json.id).children('thead').children().children('td:eq(' + json.column + ')').remove();
					
					makeSortable();
					
					showCharts();
				}
			}
		});
	});
	
	gajQuery(".column-action").on('click', '.ui-icon-plusthick', function() {
		var cell = gajQuery(this).parent().parent();
		var column = cell.parent().children().index(cell);
		var groupID = cell.parent().parent().parent().attr('id').replace('tabs-', '');
		
		gajQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_ganalytics&task=dashboard.addcolumn',
			data: {id: groupID, column: column},
			success: function (data) {
				var json = showAjaxMessage(data);
				if(json.status == 'success'){
					var cellToAppend = gajQuery('#tabs-'+json.id).children('thead').children().children('td:eq(' + json.column + ')');
					var newColumn = cellToAppend.clone();
					cellToAppend.after(newColumn);

					var cellToAppend = gajQuery('#tabs-'+json.id).children('tbody').children().children('td:eq(' + json.column + ')');
					var newColumn = cellToAppend.clone(false).html('');
					cellToAppend.after(newColumn);
					
					makeSortable();
					
					showCharts();
				}
			}
		});
	});

	// global setting stuff
	gajQuery('#profiles').change(function() {
		getActiveTab().find('form[name=adminForm]').gaChart('refresh', {
			gaid: gajQuery(this).val()
		});
	});
	gajQuery('#date_from').change(function() {
		getActiveTab().find('form[name=adminForm]').gaChart('refresh', {
			start: gajQuery(this).val()
		});
	});
	gajQuery('#date_to').change(function() {
		getActiveTab().find('form[name=adminForm]').gaChart('refresh', {
			end: gajQuery(this).val()
		});
	});
	gajQuery('.adminform').hide();
});
				
function saveCharts(){
	var activeTab = getActiveTab();
	var countCol = activeTab.children('tbody').children('tr:first-child').children('td').length;

	var chartData = activeTab.children('tbody').children().children().map(function(i) {
		var column = {};
		gajQuery(this).children('div').each(function(i) {
			column[i] = gajQuery(this).find('input[name=id]').val();
		});
		return column;
	}).get();

	gajQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_ganalytics&task=dashboard.save',
		data: {structure:  JSON.stringify(chartData)},
		success: function (data) {
			showAjaxMessage(data);
		}
	});
}

function showCharts(){
	var activeTab = getActiveTab();
	var countCol = activeTab.children('tbody').children('tr:first-child').children('td').length;
	activeTab.children('tbody').children('tr:first-child').children('td').each(function(i) {
		var cell = gajQuery(this);
		var width = (activeTab.width()-30)/countCol;
		cell.width(width);
		cell.find('.ganalytics_chart').width(width-30);
	});
	activeTab.find('form[name=adminForm]').gaChart('refresh', {start: gajQuery('#date_from').val(), end: gajQuery('#date_to').val()});
}

function showAjaxMessage(data) {
	var tmp = gajQuery.parseJSON(data); 
	gajQuery.pnotify({
	    pnotify_title: '',
	    pnotify_text: tmp.message,
	    pnotify_opacity: .95
	});
	return tmp;
}

function makeSortable(){
	gajQuery(".chart-table > tbody > tr > td").sortable({
		connectWith : ".chart-table > tbody > tr > td",
		handle: 'div.portlet-header .portlet-title',
		items: 'div.portlet',
		stop: function(event, ui) {
			ui.item.find('.adminForm').gaChart('show');
			saveCharts();
		},
		start: function(event, ui) {
			ui.item.find('.adminForm').gaChart('hide');
		}
	});
}

function getActiveTab() {
	return gajQuery('#tabs table.ui-tabs-panel').filter(function() { return gajQuery(this).css("display") != "none" });
}