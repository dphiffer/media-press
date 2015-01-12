(function($) {
  
var imageEdit = window.imageEdit = {
	
	setup: function(postid) {
		this.setupForm(postid);
		this.updateImage();
		$('#image-editor-replace').click(function(e) {
		  $('#image-editor-file').trigger(e);
		});
		$('#image-editor-size').change(this.updateImage);
		$('#image-editor-iframe').load(this.updateImage);
	},
	
	setupForm: function(postid) {
		if ($('#image-editor-form').length == 0) {
			var form = $('<form action="/wp-admin/admin-ajax.php" ' +
		                     'method="post" ' +
		                     'id="image-editor-form" ' +
		                     'target="image-editor-iframe" ' +
		                     'enctype="multipart/form-data">' +
		                 '<input type="hidden" name="action" value="image-editor">' +
		                 '<input type="hidden" name="do" value="replace">' +
		                 '<input type="hidden" name="postid" value="' + postid + '">' +
		                 '<input type="hidden" name="size" value="thumbnail">' +
		                 '<input type="file" name="file" id="image-editor-file">' +
                   '</form>');
		  form.appendTo($('body'));
		  $('#image-editor-file').change(function() {
		    $('#image-editor-form').submit();
		  });
		} else {
			$('#image-editor-form input[name="postid"]').val(postid);
		}
	},
	
	updateImage: function() {
	  var id = $('#image-editor-size').val();
	  var size = $('#image-editor').data(id);
	  var now = (new Date()).getTime();
	  $('#image-editor-form input[name="size"]').val(id);
	  $('#image-editor-img').html('<img src="/wp-content/uploads/' + size.file + '?' + now + '">');
	},
	
	open: function( postid, nonce, view ) {
		
		this._view = view;
		
		var self = this;
	  
		var dfd, data, elem = $('#image-editor-' + postid), head = $('#media-head-' + postid),
		    btn = $('#imgedit-open-btn-' + postid), spin = btn.siblings('.spinner');

		btn.prop('disabled', true);
		spin.show();

		data = {
			'action': 'image-editor',
			'_ajax_nonce': nonce,
			'postid': postid,
			'do': 'open'
		};

		dfd = $.ajax({
			url:  ajaxurl,
			type: 'post',
			data: data
		}).done(function( html ) {
			elem.html(html);
			self.setup(postid);
			head.fadeOut('fast', function(){
				elem.fadeIn('fast');
				btn.removeAttr('disabled');
				spin.hide();
			});
		});

		return dfd;
	}
	
};

})(jQuery);
