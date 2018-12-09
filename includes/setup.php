<?php

function tp_admin_style() {
  wp_register_style('tp-admin-styles', plugins_url('/../css/overages.css',  __FILE__ ));
  if (!empty($_GET['page']) && $_GET['page'] === 'tp_membership_overages'):
    wp_enqueue_style('tp-admin-styles');
  endif;
}

function tp_admin_init() {
  $shouldReturn = false;

	if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	  add_action('admin_notices', function() { ?>
			<div class="update-nag notice">
			  <p><?php _e( 'Please install and activate the <a href="https://woocommerce.org" title="WooCommerce" target="_blank">WooCommerce plugin</a>. It is required for the <strong>ThinkPawsitive Overages</strong> plugin!', 'tp-membership-overages' ); ?></p>
			</div>
		<?php } );

		$shouldReturn = true;
	}

  if ( !is_plugin_active( 'woocommerce-bookings/woocommerce-bookings.php' ) ) {
	  add_action('admin_notices', function() { ?>
			<div class="update-nag notice">
			  <p><?php _e( 'Please install and activate the <a href="https://docs.woocommerce.com/documentation/plugins/woocommerce/woocommerce-extensions/woocommerce-bookings" title="WooCommerce Bookings" target="_blank">WooCommerce Bookings</a>. It is required for the <strong>ThinkPawsitive Overages</strong> plugin!', 'tp-membership-overages' ); ?></p>
			</div>
		<?php } );

		$shouldReturn = true;
	}

  if ( !is_plugin_active( 'woocommerce-memberships/woocommerce-memberships.php' ) ) {
	  add_action('admin_notices', function() { ?>
			<div class="update-nag notice">
			  <p><?php _e( 'Please install and activate the <a href="https://docs.woocommerce.com/document/woocommerce-memberships" title="WooCommerce Memberships" target="_blank">WooCommerce Memberships</a>. It is required for the <strong>ThinkPawsitive Overages</strong> plugin!', 'tp-membership-overages' ); ?></p>
			</div>
		<?php } );

		$shouldReturn = true;
	}

	// stop the plugin
	if (!$shouldReturn) {
		add_action('admin_enqueue_scripts', 'tp_admin_style');
	}
}

add_action( 'admin_init', 'tp_admin_init' );

?>
