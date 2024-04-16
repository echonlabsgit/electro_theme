<?php
/**
 * electro engine room
 *
 * @package electro
 */

//remove the default jQuery script

// add_filter( 'wp_default_scripts', 'change_default_jquery' );

function change_default_jquery( &$scripts){
    if(!is_admin()){
        $scripts->remove('jquery');
    }
}

//inject a new one from a remote source
// add_action('wp_enqueue_scripts', 'ocean_theme_scripts');

function ocean_theme_scripts() {
    if(!is_admin()){
        wp_register_script('jquery-raawana', '//code.jquery.com/jquery-1.12.4.min.js', null, null, true);
        wp_enqueue_script('jquery-raawana');
    }
}


 /**
 * Custom Change Currency Symbol In WooCommerce
 */
 
 
add_filter('woocommerce_currency_symbol', 'el_change_currency_symbol_woocommerce', 10, 2);
 
function el_change_currency_symbol_woocommerce( $currency_symbol, $currency ) {
 
     switch( $currency ) {
          case 'LKR': $currency_symbol = 'LKR'; break;
     }
     return $currency_symbol;
      
}


/**
 * Initialize all the things.
 */
require get_template_directory() . '/inc/init.php';

/**
 * Note: Do not add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * http://codex.wordpress.org/Child_Themes
 */


// add_action( 'admin_menu', 'el_remove_items', 99, 0 );

function el_remove_items() {
   $remove = array( 'wc-admin','wc-settings','woocommerce_email_control','wc-addons');
   foreach ( $remove as $slug ) {
     if ( ! current_user_can( 'update_core' ) ) {
       remove_submenu_page( 'woocommerce', $slug );
     }
   }

}

function filter_script_loader_tag( $tag, $handle ) {

	foreach ( array( 'async', 'defer' ) as $attr ) {
		if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
			continue;
		}
		// Prevent adding attribute when already added in #12009.
		if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
			$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
		}
		// Only allow async or defer, not both.
		break;
	}
	return $tag;
}
if(!is_admin()) {
	add_filter( 'script_loader_tag', 'add_defer_to_script', 10, 4 );
 
	function add_defer_to_script( $tag, $handle, $src ) {
		if($handle === 'jquery-core'){
			return '<script type="text/javascript" src="' . esc_url( $src ) . '" id="'.$handle.'"></script>';
		}
		$tag = '<script defer="defer" type="text/javascript" src="' . esc_url( $src ) . '" id="'.$handle.'"></script>';
		return $tag;
	}
}


 // Custom functions added by Vishwajith Weerasinghe

function add_image_insert_override($sizes){
    unset($sizes['thumbnail']);
    unset($sizes['medium']);
    unset($sizes['medium_large']);
    unset($sizes['large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);        
    unset($sizes['blog-isotope']);
    unset($sizes['product_small_thumbnail']);
    unset($sizes['shop_catalog']);
    unset($sizes['shop_single']);
    unset($sizes['shop_single_small_thumbnail']);
    unset($sizes['shop_thumbnail']);
    unset($sizes['woocommerce_thumbnail']);
    unset($sizes['woocommerce_single']);
    unset($sizes['woocommerce_gallery_thumbnail']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'add_image_insert_override' );
add_filter('max_srcset_image_width', create_function('', 'return 1;'));

function add_rel_preload($html, $handle, $href, $media) {
    
    if (is_admin())
        return $html;

     $html = <<<EOT
<link rel='preload' async as='style' onload="this.onload=null;this.rel='stylesheet'" id='$handle' href='$href' type='text/css' media='all' />
EOT;
    return $html;
}
add_filter( 'style_loader_tag', 'add_rel_preload', 10, 4 );
