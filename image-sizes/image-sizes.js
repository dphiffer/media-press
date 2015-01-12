(function($) {
  
	function updateSizes() {
		var sizes = [];
	  $('.image_sizes').each(function(index, tr) {
			var id = $(tr).attr('id').substr("image_sizes_".length);
	    sizes.push({
				id:     id,
				name:   $(tr).find('.image_sizes_name').html(),
				width:  parseInt($(tr).find('#' + id + '_size_w').val(), 10),
				height: parseInt($(tr).find('#' + id + '_size_h').val(), 10),
				crop:   $(tr).find('#' + id + '_crop')[0].checked,
				show:   $(tr).find('#' + id + '_show')[0].checked
	    });
	  });
	  $('#image_sizes').val(JSON.stringify(sizes));
	}
	
	function addSize(config) {
		var id = config.id;
		var name = config.name;
		var size = $('#image_sizes_template').data('size');
		var tr = $('<tr>' +
		             '<th scope="row"><span class="image_sizes_name">' + name + '</span> ' + size + '</th>' +
		             '<td>' + $('#image_sizes_template').html() + '</td>' +
		           '</tr>');
		tr.insertBefore($('#image_sizes_template').closest('tr'));
		$(tr).attr('id', 'image_sizes_' + id);
		$(tr).attr('class', 'image_sizes');
		$(tr).find('.image_sizes_name').html(name);
		$(tr).find('input').each(function(index, input) {
			if ($(input).hasClass('image_sizes_remove')) {
				return;
			}
			$(input).attr('id', $(input).attr('id').replace('image', id));
			$(input).attr('name', $(input).attr('name').replace('image', id));
			if (config.width && $(input).attr('id').substr(-2, 2) == '_w') {
				$(input).val(config.width);
			} else if (config.height && $(input).attr('id').substr(-2, 2) == '_h') {
				$(input).val(config.height);
			} else if ($(input).attr('id').substr(-5, 5) == '_crop') {
				input.checked = !!config.crop;
			} else if ($(input).attr('id').substr(-5, 5) == '_show') {
				input.checked = !!config.show;
			}
		});
		$(tr).find('.image_sizes_remove').click(function(e) {
			e.preventDefault();
			$(this).closest('tr').remove();
			updateSizes();
		});
		$(tr).find('input').change(updateSizes);
	}
	
	$(document).ready(function() {
		var sizes = JSON.parse($('#image_sizes').val());
		$.each(sizes, function(index, size) {
			addSize(size);
		});
		$('#image_sizes_add').click(function(e) {
	    e.preventDefault();
	    var name = $('#image_sizes_name').val();
	    var id = name.toLowerCase().replace(/\W+/, '_');
	    if (id == '') {
	    	return;
	    }
	    if (id == 'thumbnail' ||
	        id == 'medium' ||
	      	id == 'large' ||
	      	id == 'full' ||
	      	$('#image_sizes_' + id).length > 0) {
	    	alert('Image size ‘' + id + '’ already exists.');
	    	return;
			}
	    addSize({
				id: id,
				name: name
	    });
	    updateSizes();
	  });
	});
	
})(jQuery);
