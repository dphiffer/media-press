<?php

if ( ! class_exists( 'MP_Image_Editor' ) ) {
	
	class MP_Image_Editor extends MP_Base {
		
		function __construct() {
			$this->add_action( 'admin_enqueue_scripts' );
		  $this->add_action( 'wp_ajax_image-editor', 'dispatch', 0 );
		}
		
		function dispatch() {
			//dbug('dispatch');
		  if ( $_POST['do'] == 'open' ) {
		  	$this->open();
		  } else if ($_POST['do'] == 'replace' ) {
		  	$this->replace();
		  }
		  exit;
		}
		
		function open() {
			$meta = wp_get_attachment_metadata( $_POST['postid'] );
			$options = '';
			$data_attr = '';
			$dir = dirname( $meta['file'] );
			foreach ( $meta['sizes'] as $id => $size ) {
				$label = $this->get_size_label( $id );
				$data_value = esc_attr( json_encode( array(
					'file' => "$dir/{$size['file']}",
					'width' => $size['width'],
					'height' => $size['height']
				) ) );
				$data_attr .= " data-$id=\"$data_value\"";
				$options .= "<option value=\"$id\">$label – {$size['width']} × {$size['height']}</option>\n";
			}
			?>
			<div id="image-editor"<?php echo $data_attr; ?>>
				<select id="image-editor-size" name="size"><?php echo $options; ?></select>
				<input type="button" value="Replace" class="button" id="image-editor-replace">
				<div id="image-editor-img"></div>
				<iframe src="about:blank" name="image-editor-iframe" id="image-editor-iframe">
			</div>
			<?php
		}
		
		function replace() {
			$meta = wp_get_attachment_metadata( $_POST['postid'] );
			$dir = wp_upload_dir();
			$dir = $dir['basedir'];
			$subdir = dirname( $meta['file'] );
			$file = $meta['sizes'][$_POST['size']]['file'];
			if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
				$tmp_name = $_FILES['file']['tmp_name'];
				//dbug($tmp_name, "$dir/$subdir/$file");
				move_uploaded_file($tmp_name, "$dir/$subdir/$file");
			}
		}
		
		function get_size_label( $id ) {
			if ( substr( $id, -3, 3 ) == '_2x' ) {
				return $this->get_size_label( substr( $id, 0, -3 ) ) . ' 2x';
			}
			if ( $id == 'thumbnail' ||
			     $id == 'medium' ||
			     $id == 'large' ) {
				return ucfirst( $id );
			}
			$sizes = get_option( 'image_sizes' );
			foreach ( $sizes as $size ) {
				if ( $size['id'] == $id ) {
					return $size['name'];
				}
			}
		  return $id;
		}
		
		function admin_enqueue_scripts() {
			if ( wp_script_is( 'image-edit', 'enqueued' ) ) {
				wp_dequeue_script( 'image-edit' );
				wp_enqueue_style( 'image_editor', plugin_dir_url( __FILE__ ) . 'image-editor.css', array(), '20141226');
				wp_enqueue_script( 'image_editor', plugin_dir_url( __FILE__ ) . 'image-editor.js', array( 'jquery' ), '20141226', true );
			}
		}
		
	}
	
	function mp_image_editor_init() {
	  $image_editor = new MP_Image_Editor();
	}
	add_action( 'admin_init', 'mp_image_editor_init' );
	
}

