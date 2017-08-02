<?php
/*
Plugin Name: WP Terms Popup
Plugin URI: http://termsplugin.com
Description: Make your visitors agree to your terms and conditions before entering your website. Now you can create as many different terms as you want and choose to display any of them on any specific post or page.
Version: 1.1.0
Author: Tentenbiz & Dalv8
Author URI: http://tentenbiz.com
*/

function terms_registerStyles () {	
   wp_register_style( 'popupstyle', plugins_url( 'wp-terms-popup/popup-style.css' ) );
   wp_enqueue_style( 'popupstyle' );
}
add_action( 'wp_enqueue_scripts', 'terms_registerStyles', 1 );


function terms_openPopup () {
	
	$currentpostid = get_the_ID();
	$enabled = get_post_meta( $currentpostid, 'terms_enablepop', true );
	$isshortcode = 0;
	
	if (is_user_logged_in()) {
		if (get_option('termsopt_adminenabled') <> 1) {
			//nothing happens
		}
		elseif (get_option('termsopt_adminenabled') == 1) {
			include_once('terms-gateway.php');
		}
	}
	else {
		include_once('terms-gateway.php');
	}

}
add_action('get_header', 'terms_openPopup'); //where the popup fires


function terms_shortcode_call ( $atts ) { //shortcodes are for if user wants to show popups on custom post type items
	extract( shortcode_atts( array(
        'id' => 0
    ), $atts ) ); //default id
	
	$enabled = 1;
	$isshortcode = 1;
	$termsidscode = $atts['id'];
	
	include('terms-gateway.php');
}
add_shortcode('wpterms', 'terms_shortcode_call');


function terms_popup_post_type() {
	
	$labels = array(
		'name'               => _x( 'Terms Popups', 'post type general name', 'wp-terms-popup' ),
		'singular_name'      => _x( 'Terms Popup', 'post type singular name', 'wp-terms-popup' ),
		'menu_name'          => _x( 'Terms Popups', 'admin menu', 'wp-terms-popup' ),
		'name_admin_bar'     => _x( 'Terms Popup', 'add new on admin bar', 'wp-terms-popup' ),
		'add_new'            => _x( 'Add New', 'termpopup', 'wp-terms-popup' ),
		'add_new_item'       => __( 'Add New Terms', 'wp-terms-popup' ),
		'new_item'           => __( 'New Terms', 'wp-terms-popup' ),
		'edit_item'          => __( 'Edit Terms', 'wp-terms-popup' ),
		'view_item'          => __( 'View Terms', 'wp-terms-popup' ),
		'all_items'          => __( 'All Terms', 'wp-terms-popup' ),
		'search_items'       => __( 'Search Terms', 'wp-terms-popup' ),
		'not_found'          => __( 'No terms found.', 'wp-terms-popup' ),
		'not_found_in_trash' => __( 'No terms found in Trash.', 'wp-terms-popup' )
	);
	
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'termspopup' ),
		'capability_type'    => 'page',
		'hierarchical'       => true,
		'menu_position'      => 20,
		'menu_icon' 		 => 'dashicons-visibility',
		'supports'           => array( 'title', 'editor', 'author', 'revisions' )
	);
    register_post_type( 'termpopup', $args );
}
add_action( 'init', 'terms_popup_post_type' );


add_filter( 'manage_edit-termpopup_columns', 'terms_edit_termpopup_columns' );
add_action( 'manage_termpopup_posts_custom_column', 'terms_manage_termpopup_columns', 10, 2 );

function terms_edit_termpopup_columns( $columns ) {
	
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Terms' ),
		'shortcodeid' => __( 'Shortcode' ),
		'author' => __( 'Author' ),
		'date' => __( 'Date' )
	);

	return $columns;
}


function terms_manage_termpopup_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {
		case 'shortcodeid' :
			$shortcodeid = $post_id;
			printf( __( '[wpterms id="%s"]' ), $shortcodeid );
			break;

		default :
			break;
	}
}


add_action( 'load-post.php', 'terms_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'terms_post_meta_boxes_setup' );


function terms_post_meta_boxes_setup() {
	add_action( 'add_meta_boxes', 'terms_add_post_meta_boxes' );
	add_action( 'save_post', 'terms_save_setting_meta', 10, 2 );
	add_action( 'save_post', 'terms_save_popup_meta', 10, 2 );
}


function terms_save_setting_meta( $post_id, $post ) {

  if ( !isset( $_POST['terms_enablepopup_nonce'] ) || !wp_verify_nonce( $_POST['terms_enablepopup_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  $post_type = get_post_type_object( $post->post_type );

  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  $new_enablepop_value = ( isset( $_POST['terms_enablepop'] ) ? sanitize_html_class( $_POST['terms_enablepop'] ) : '' );
  $new_selectedterms_value = ( isset( $_POST['terms_selectedterms'] ) ? sanitize_html_class( $_POST['terms_selectedterms'] ) : '' );

  $enablepop_key = 'terms_enablepop';
  $enablepop_value = get_post_meta( $post_id, $enablepop_key, true );
  
  $selectedterms_key = 'terms_selectedterms';
  $selectedterms_value = get_post_meta( $post_id, $selectedterms_key, true );

  
  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_enablepop_value && '' == $enablepop_value )
    add_post_meta( $post_id, $enablepop_key, $new_enablepop_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_enablepop_value && $new_enablepop_value != $enablepop_value )
    update_post_meta( $post_id, $enablepop_key, $new_enablepop_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_enablepop_value && $enablepop_value )
    delete_post_meta( $post_id, $enablepop_key, $enablepop_value );
	

  if ( $new_selectedterms_value && '' == $selectedterms_value )
    add_post_meta( $post_id, $selectedterms_key, $new_selectedterms_value, true );

  elseif ( $new_selectedterms_value && $new_selectedterms_value != $selectedterms_value )
    update_post_meta( $post_id, $selectedterms_key, $new_selectedterms_value );

  elseif ( '' == $new_selectedterms_value && $selectedterms_value )
    delete_post_meta( $post_id, $selectedterms_key, $selectedterms_value );
}


function terms_save_popup_meta( $post_id, $post ) {

  if ( !isset( $_POST['terms_popupmeta_nonce'] ) || !wp_verify_nonce( $_POST['terms_popupmeta_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  $post_type = get_post_type_object( $post->post_type );

  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  $metakeys = array(terms_agreetxt, terms_disagreetxt, terms_redirecturl);

  foreach ($metakeys as $metakey) {
	$new_meta_value = ( isset( $_POST[$metakey] ) ? $_POST[$metakey] : '' );

	$meta_key = $metakey;
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
  }
}


function terms_add_post_meta_boxes() {
	
	$screen = array("post", "page");

	add_meta_box(
		'termpopup-setting',      // ID
		esc_html__( 'Terms Popup', 'wp-terms-popup' ),    // title
		'terms_enablepopup_meta_box',   // callback function
		$screen,         // screen
		'side',         // context
		'default'         // priority
	);
	
	add_meta_box(
		'thepopup-meta',      // ID
		esc_html__( 'Popup Setting', 'wp-terms-popup' ),    // title
		'terms_popup_meta',   // callback function
		'termpopup',         // screen
		'normal',         // context
		'high'         // priority
	);
}


function terms_enablepopup_meta_box( $object, $box ) {

  wp_nonce_field( basename( __FILE__ ), 'terms_enablepopup_nonce' );
  
  _e( "Enable terms popup? :", 'wp-terms-popup' ); ?>
  
	<input type="checkbox" name="terms_enablepop" value="1" <?php checked( '1', get_post_meta( $object->ID, 'terms_enablepop', true ) ); ?>>Yes
  
  <?php 
  echo '<br /><br />';
  _e( "Terms to show to visitors before this post/page is loaded:", 'wp-terms-popup' );
  echo '<br /><br />';
  
  $isselected = get_post_meta( $object->ID, 'terms_selectedterms', true );
  
  wp_dropdown_pages("name=terms_selectedterms&post_type=termpopup&show_option_none=".__('- Select -')."&selected=" .$isselected);
  
}


function terms_popup_meta( $object, $box ) {

  wp_nonce_field( basename( __FILE__ ), 'terms_popupmeta_nonce' );
  
?>
  
<p>Custom 'I Agree' button text :
<input type="text" name="terms_agreetxt" size="20" value="<?php echo get_post_meta( $object->ID, 'terms_agreetxt', true ); ?>" />
</p>

<p>Custom 'I Do Not Agree' button text :
<input type="text" name="terms_disagreetxt" size="20" value="<?php echo get_post_meta( $object->ID, 'terms_disagreetxt', true ); ?>" />
</p>

<p>URL to redirect to when 'I Do Not Agree' is clicked :
<input type="text" name="terms_redirecturl" size="45" value="<?php echo get_post_meta( $object->ID, 'terms_redirecturl', true ); ?>" />
</p>
  
<?php
}


require_once('terms-options.php');
?>