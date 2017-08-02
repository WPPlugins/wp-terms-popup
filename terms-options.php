<?php

function terms_create_admin_menu() {
    add_options_page( __( 'WP Terms Popup', 'terms-popup-plugin' ), __( 'WP Terms Popup', 'terms-popup-plugin' ), 'administrator', __FILE__, 'terms_popup_settings_page' );
}
add_action( 'admin_menu', 'terms_create_admin_menu' );


function terms_popup_settings_page() { ?>

<div class="wrap">
<h2>WP Terms Popup Plugin Settings</h2>

<form name="termsForm" method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>

<h3>Popup General Settings</h3>

<p>&nbsp;</p>

<p style="font-weight:bold">Below are the GENERAL settings for ALL popups. You can override the settings below by editing individual popup.</p>

<p>&nbsp;</p>

<p>Enable popups for logged in users? :
	<input type="checkbox" name="termsopt_adminenabled" value="1" <?php checked( '1', get_option('termsopt_adminenabled') ); ?>>Yes
</p>

<p>Enable only one popup across the site (sitewide)? :
	<input type="checkbox" name="termsopt_sitewide" value="1" <?php checked( '1', get_option('termsopt_sitewide') ); ?>>Yes
</p>

<p>Select terms to be displayed as popup for the above choice (sitewide):
<?php wp_dropdown_pages("name=termsopt_page&post_type=termpopup&show_option_none=".__('- Select -')."&selected=" .get_option('termsopt_page')); ?>
</p>

<p>&nbsp;</p>

<p>Custom 'I Agree' button text :
<input type="text" name="termsopt_agreetxt" size="20" value="<?php echo get_option('termsopt_agreetxt'); ?>" />
</p>

<p>Custom 'I Do Not Agree' button text :
<input type="text" name="termsopt_disagreetxt" size="20" value="<?php echo get_option('termsopt_disagreetxt'); ?>" />
</p>

<p>URL to redirect to when 'I Do Not Agree' is clicked :
<input type="text" name="termsopt_redirecturl" size="45" value="<?php echo get_option('termsopt_redirecturl'); ?>" />
</p>

<p>How long should it be until your visitors see the popup again after they agree? :
<input type="text" name="termsopt_expiry" size="10" value="<?php echo get_option('termsopt_expiry'); ?>" /> (in hours, leave blank if in doubt)
</p>

<p>&nbsp;<p>

<p><input class="button-primary" type="submit" name="Submit" value="Save Options" /></p>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="termsopt_adminenabled,termsopt_sitewide,termsopt_page,termsopt_agreetxt,termsopt_disagreetxt,termsopt_redirecturl,termsopt_expiry" />
</form>

<p>*Upgrade to our <a href="http://termsplugin.com">PRO version</a> if you want to change the style and colors of your popups!</p>

</div>
<?php }

?>