function updateSortCombo(form){
	if(form == null){
		return;
	}
	var combo = form.find('.sortcombo');
	
	if(form.find('.dimensionscombo option').length < 2 && form.find('.metricscombo option').length < 2) {
		return;
	}
	
	var oldValue = combo.val();
	
	// we are initializing it
	if(combo.find('option').length < 2 && combo.find('option').val() != null) {
		oldValue = combo.find('option').val().split(',');
	}
	
	combo.multiselect2side('destroy');
	
	combo.html('');
	combo.append(gajQuery('<option></option>').val('').html(''));
	
	form.find('.dimensionscombo :selected').each(function() {
		var option = gajQuery(this);
		combo.append(gajQuery('<option></option>').val(option.val()).html(option.html()));
		combo.append(gajQuery('<option></option>').val('-'+option.val()).html('-'+option.html()));
	});
	form.find('.metricscombo :selected').each(function() {
		var option = gajQuery(this);
		combo.append(gajQuery('<option></option>').val(option.val()).html(option.html()));
		combo.append(gajQuery('<option></option>').val('-'+option.val()).html('-'+option.html()));
	});
	
	if(oldValue != null && oldValue.length > 0) {
		var first = combo.children("option").not(":selected").first();
		gajQuery.each(oldValue, function(index, item) {
			var option = combo.find("[value='" + item + "']");
			option.attr('selected', true);
			option.insertBefore(first);
		});
	}
	
	createMultiSelectCombo(form.find('.sortcombo'));
}

function createMultiSelectCombo(element){
	element.multiselect2side({
		optGroupSearch: false, // &nbps;
		search: false, //"<img src='components/com_ganalytics/libraries/jquery/multiselect/img/search.gif' />",
		labelTop: '+ +',
		labelBottom: '- -',
		labelUp: '+',
		labelDown: '-',
		labelSort: '',
		labelsx: null,
		labeldx: null
	});
}

gajQuery(document).ready(function() {
	gajQuery('body').on('change', '.dimensionscombo', function() {
		updateSortCombo(gajQuery(this).parents('form'));
	});
	gajQuery('body').on('change', '.metricscombo', function() {
		updateSortCombo(gajQuery(this).parents('form'));
	});

	gajQuery('.dimensionscombo').each(function() {
		updateSortCombo(gajQuery(this).parents('form'));
	});
});