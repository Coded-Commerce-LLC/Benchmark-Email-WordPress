function bmebinding() {
	jQuery('.widget').delegate('input[id^="widget-benchmarkemaillite_widget-"]', 'click', function() {
		var bmestatus = 0;
		jQuery(this).parent().parent().parent().find('.bmelabels').each(function() {
			if (jQuery(this).val() == '') {
				jQuery(this).css('border-color', 'red');
				jQuery(this).focus();
				bmestatus++;
			} else { jQuery(this).css('border-color', ''); }
		});
		if (bmestatus > 1) {
			alert('Please enter labels for all fields.');
			return false;
		}
		return true;
	});
	jQuery('.widget').delegate('.bmefields', 'focus', function() {
		var current = jQuery(this);
		var selected = new Array();
		current.parent().parent().parent().find('.bmefields').each(function() {
			var val = jQuery(this).val();
			if (val != '') { selected.push(val); }
		});
		current.children().each(function() {
			var option = jQuery(this);
			var used = jQuery.inArray(option.val(), selected);
			used = (used == -1) ? false : true;
			var isthis = (option.val() == current.val()) ? true : false;
			if (used && !isthis) { option.attr('disabled', 'disabled'); }
			else if (option.val() != '') { option.removeAttr('disabled'); }
		});
		return true;
	});
	jQuery('.widget').delegate('.bmefields', 'change', function() {
		jQuery(this).parent().parent().find('.bmelabels').val(jQuery(this).find('option:selected').text());
		return false;
	});
	jQuery('.widget').delegate('.bmedelete', 'click', function() {
		jQuery(this).parent().parent().remove();
		return false;
	});
	jQuery('.widget').undelegate('.bmemoveup', 'click');
	jQuery('.widget').delegate('.bmemoveup', 'click', function() {
		var item = jQuery(this).parent().parent();
		if (item.prev().length == 0 || item.prev().hasClass('bmebase')) { return false; }
		item.fadeOut(250, function() {
			item.prev().before(item);
			item.fadeIn(500);
		});
		return false;
	});
	jQuery('.widget').undelegate('.bmemovedown', 'click');
	jQuery('.widget').delegate('.bmemovedown', 'click', function() {
		var item = jQuery(this).parent().parent();
		if (item.next().length == 0) { return false; }
		item.fadeOut(250, function() {
			item.next().after(item);
			item.fadeIn(500);
		});
		return false;
	});
	jQuery('.widget').undelegate('.bmeadd', 'click');
	jQuery('.widget').delegate('.bmeadd', 'click', function() {
		var item = jQuery(this).parent().prev().find('.bmebase');
		var cloned = item.clone(true);
		var key = Math.floor(Math.random() * 100000);
		cloned.find('input,select').each(function() {
			jQuery(this).attr('name', jQuery(this).attr('name').replace('INSERT-KEY', key));
		});
		cloned.removeAttr('style');
		cloned.removeAttr('class');
		cloned.appendTo(item.parent());
		return false;
	});
}