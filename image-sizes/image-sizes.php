<?php

if ( ! class_exists( 'MP_Image_Sizes' ) ) {

	class MP_Image_Sizes extends MP_Base {
	
		function __construct() {
			$this->image_sizes = get_option( 'image_sizes', array() );
			$this->image_sizes_2x = get_option( 'image_sizes_2x', 0 );
			$this->add_image_sizes();
			$this->add_action( 'admin_init' );
			$this->add_action( 'admin_enqueue_scripts' );
			$this->add_filter( 'whitelist_options' );
			$this->add_filter( 'pre_update_option_image_sizes' );
			$this->add_filter( 'image_size_names_choose' );
		}
		
		function add_image_sizes() {
			if ( ! empty( $this->image_sizes_2x ) ) {
				foreach ( get_intermediate_image_sizes() as $size ) {
					$double_w = $this->get_width( $size ) * 2;
					$double_h = $this->get_height( $size ) * 2;
					$crop = $this->get_crop( $size );
					add_image_size( "{$size}_2x", $double_w, $double_h, $crop );
				}
			}
		  foreach ( $this->image_sizes as $size ) {
				add_image_size( $size['id'], $size['width'], $size['height'], $size['crop'] );
				if ( ! empty( $this->image_sizes_2x ) ) {
					add_image_size( $size['id'] . '_2x', $size['width'] * 2, $size['height'] * 2, $size['crop'] );
				}
			}
		}
		
		function admin_init() {
			add_settings_field(
				'image_sizes_add',
				__( 'Add image size' ),
				array( $this, 'add_settings_field' ),
				'media'
			);
			add_settings_field(
				'image_sizes_2x',
				__( '2x image sizes' ),
				array( $this, 'double_density_settings_field' ),
				'media'
			);
		}
		
		function add_settings_field() {
			$image_sizes = json_encode( $this->image_sizes );
			$image_sizes = esc_attr( $image_sizes );
			?>
			<input type="hidden" id="image_sizes" name="image_sizes" value="<?php echo $image_sizes; ?>">
			<input type="text" id="image_sizes_name" placeholder="<?php _e( 'Image size name', 'media-press' ); ?>">
			<input type="button" id="image_sizes_add" class="button" value="<?php _e( 'Add', 'media-press' ); ?>">
			<div id="image_sizes_template" data-size="<?php _e( 'size', 'media-press' ); ?>">
				<div class="image_sizes_settings">
					<label for="image_size_w"><?php _e( 'Max Width' ); ?></label>
					<input type="number" class="small-text" id="image_size_w" min="0" step="1" name="image_size_w">
					<label for="image_size_h"><?php _e( 'Max Height' ); ?></label>
					<input type="number" class="small-text" id="image_size_h" min="0" step="1" name="image_size_h"><br>
					<input type="checkbox" value="1" id="image_crop" name="image_crop">
					<label for="image_crop"><?php _e( 'Crop to exact dimensions', 'media-press' ); ?></label><br>
					<input type="checkbox" value="1" id="image_show" name="image_show">
					<label for="image_show"><?php _e( 'Show in image insertion', 'media-press' ); ?></label>
				</div>
				<input type="button" value="<?php _e( 'Remove', 'media-press' ); ?>" class="button image_sizes_remove">
			</div>
			<?php
		}
		
		function double_density_settings_field() {
			$checked = '';
			if ( ! empty( $this->image_sizes_2x ) ) {
				$checked = ' checked="checked"';
			}
		  ?>
		  <input type="checkbox" value="1" id="image_sizes_2x" name="image_sizes_2x"<?php echo $checked; ?>>
		  <label for="image_sizes_2x"><?php _e( 'Create double pixel density versions of each image size', 'media-press' ); ?></label>
			<?php
		}
		
		function admin_enqueue_scripts() {
			global $pagenow;
			if ( $pagenow == 'options-media.php' ) {
				wp_enqueue_style( 'image_sizes', plugin_dir_url( __FILE__ ) . 'image-sizes.css', array(), '20141226');
				wp_enqueue_script( 'image_sizes', plugin_dir_url( __FILE__ ) . 'image-sizes.js', array( 'jquery' ), '20141226', true );
			}
		}
		
		function whitelist_options( $whitelist_options ) {
			$whitelist_options['media'][] = 'image_sizes';
			$whitelist_options['media'][] = 'image_sizes_2x';
		  return $whitelist_options;
		}
		
		function pre_update_option_image_sizes( $value ) {
		  return json_decode( $value, true );
		}
		
		function image_size_names_choose( $sizes ) {
			$show_sizes = array();
		  foreach ( $this->image_sizes as $size ) {
		  	if ( ! empty( $size['show'] ) ) {
		  		$show_sizes[$size['id']] = $size['name'];
		  	}
		  }
		  
		  // Full size should come last
		  $full = $sizes['full'];
		  unset( $sizes['full'] );
		  $sizes = array_merge( $sizes, $show_sizes );
		  $sizes['full'] = $full;
		  return $sizes;
		}
		
		function get_width( $size ) {
		  return $this->get_setting( $size, 'width', '_size_w' );
		}
		
		function get_height( $size ) {
		  return $this->get_setting( $size, 'height', '_size_h' );
		}
		
		function get_crop( $size ) {
		  return $this->get_setting( $size, 'crop', '_crop' );
		}
		
		function get_setting( $size, $key, $option_postfix ) {
		  global $_wp_additional_image_sizes;
		  if ( isset( $_wp_additional_image_sizes[$size][$key] ) ) {
		  	return intval( $_wp_additional_image_sizes[$size][$key] );
		  } else {
		  	return intval( get_option( "{$size}{$option_postfix}" ) );
		  }
		}
		
	}
	
	$mp_image_sizes = new MP_Image_Sizes();
	
}

?>
