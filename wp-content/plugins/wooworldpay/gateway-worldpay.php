<?php

    /* 
    Plugin Name: WorldPay Gateway for WooCommerce
    Plugin URI: http://www.patsatech.com 
    Description: WooCommerce Plugin for accepting payment through WorldPay Gateway.
    Author: PatSaTECH
    Version: 1.7
    Author URI: http://www.patsatech.com 
    */  
add_action('plugins_loaded', 'init_woocommerce_worldpay', 0);

function init_woocommerce_worldpay() {

    if ( ! class_exists( 'WC_Payment_Gateway' ) ) { return; }

class woocommerce_worldpay extends WC_Payment_Gateway {


	public function __construct() { 
		global $woocommerce;
		
        $this->id			= 'worldpay';
        $this->method_title = __( 'WorldPay', 'woocommerce' );
        $this->icon 		= plugins_url() . '/wooworldpay/worldpay.png';
        $this->has_fields 	= false;
        $this->liveurl 		= 'https://secure.worldpay.com/wcc/purchase';
		$this->testurl 		= 'https://secure-test.worldpay.com/wcc/purchase';
        $this->notify_url   = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'woocommerce_worldpay', home_url( '/' ) ) );
        
		// Load the form fields.
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();
		
		// Define user set variables
		$this->title 		= $this->settings['title'];
		$this->description 	= $this->settings['description'];
		$this->instid 		= $this->settings['instid'];
		$this->testmode		= $this->settings['testmode'];	
        $this->woo_version 	= $this->get_woo_version();
		
		// Logs
		if ($this->debug=='yes') $this->log = $woocommerce->logger();
		
		// Actions
		add_action( 'init', array(&$this, 'successful_request') );
		add_action( 'woocommerce_api_woocommerce_worldpay', array( &$this, 'successful_request' ) );
		add_action('woocommerce_receipt_worldpay', array(&$this, 'receipt_page'));
		add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
		
		if ( !$this->is_valid_for_use() ) $this->enabled = false;
    } 
    
     /**
     * Check if this gateway is enabled and available in the user's country
     */
    function is_valid_for_use() {
        if (!in_array(get_option('woocommerce_currency'), array('AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP'))) return false;

        return true;
    }
    
	/**
	 * Admin Panel Options 
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {

    	?>
    	<h3><?php _e('WorldPay', 'woocommerce'); ?></h3>
    	<p><?php _e('WorldPay works by sending the user to WorldPay to enter their payment information. Add this Notify URL in your WorldPay Settings : ' . trailingslashit(home_url()).'?worldpay=ipn?', 'woocommerce'); ?></p>
    	<table class="form-table">
    	<?php
    		if ( $this->is_valid_for_use() ) :
    	
    			// Generate the HTML For the settings form.
    			$this->generate_settings_html();
    		
    		else :
    		
    			?>
            		<div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woocommerce' ); ?></strong>: <?php _e( 'WorldPay does not support your store currency.', 'woocommerce' ); ?></p></div>
        		<?php
        		
    		endif;
    	?>
		</table><!--/.form-table-->
    	<?php
    } // End admin_options()
    
	/**
     * Initialise Gateway Settings Form Fields
     */
    function init_form_fields() {
    
    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'woocommerce' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable WorldPay', 'woocommerce' ), 
							'default' => 'yes'
						), 
			'title' => array(
							'title' => __( 'Title', 'woocommerce' ), 
							'type' => 'text', 
							'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ), 
							'default' => __( 'WorldPay', 'woocommerce' )
						),
			'description' => array(
							'title' => __( 'Description', 'woocommerce' ), 
							'type' => 'textarea', 
							'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ), 
							'default' => __("Pay via WorldPay; you can pay with your credit card if you don't have a WorldPay account", 'woocommerce')
						),
			'instid' => array(
							'title' => __( 'WorldPay Installation ID', 'woocommerce' ), 
							'type' => 'text', 
							'description' => __( 'Please enter your WorldPay Installation ID address; this is needed in order to take payment.', 'woocommerce' ), 
							'default' => ''
						),
			'testmode' => array(
							'title' => __( 'WorldPay Sandbox', 'woocommerce' ), 
							'type' => 'checkbox', 
							'label' => __( 'Enable WorldPay Sandbox', 'woocommerce' ), 
							'default' => 'yes'
						)
			);
    
    } // End init_form_fields()
    
    /**
	 * There are no payment fields for worldpay, but we want to show the description if set.
	 **/
    function payment_fields() {
    	if ($this->description) echo wpautop(wptexturize($this->description));
    }
    
	/**
	 * Generate the worldpay button link
	 **/
    public function generate_worldpay_form( $order_id ) {
		global $woocommerce;
		
		$order = new WC_Order( $order_id );
		
		if ( $this->testmode == 'yes' ):
			$worldpay_adr = $this->testurl;
			$test = '100';
		else :
			$worldpay_adr = $this->liveurl;
			$test = '';		
		endif;
		
		if ($this->debug=='yes') $this->log->add( 'worldpay', 'Generating payment form for order #' . $order_id . '. Notify URL: ' . trailingslashit(home_url()).'?worldpay=ipn?');
		
		$worldpay_args = array_merge(
			array(			
				'testMode'			=> $test,	
				'instId' 			=> $this->instid,
				'currency' 			=> get_option('woocommerce_currency'),
				'MC_returnurl' 		=> $this->get_return_url( $order ),
				'MC_cancelurl'		=> $order->get_cancel_order_url(),
				
				// Order key
				'cartId'			=> $order_id,
				
				// IPN
				'MC_callback'		=> $this->notify_url,
				
				// Address info
				'name'				=> $order->billing_first_name.' '.$order->billing_last_name,
				'address1'			=> $order->billing_address_1,
				'address2'			=> $order->billing_address_2,
				'town'				=> $order->billing_city,
				'region'			=> $order->billing_state,
				'postcode'			=> $order->billing_postcode,
				'country'			=> $order->billing_country,
				'email'				=> $order->billing_email,
				'tel' 				=> $order->billing_phone,
				'desc'				=> sprintf(__('Order #%s' , 'woothemes'), $order->id),
				'amount'			=> number_format($order->order_total, 2, '.', ''),
				'hideContact'		=> 'true',
				'noLanguageMenu'	=> 'true',
				'lang'				=> 'en',
	
					// Payment Info
				'MC_orderkey' 		=> $order->order_key
			)
		);
				
		$worldpay_args_array = array();

		foreach ($worldpay_args as $key => $value) {
			$worldpay_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
		}
		
		$woocommerce->add_inline_js('
			jQuery("body").block({ 
					message: "<img src=\"'.esc_url( $woocommerce->plugin_url() ).'/assets/images/ajax-loader.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" />'.__('Thank you for your order. We are now redirecting you to WorldPay to make payment.', 'woocommerce').'", 
					overlayCSS: 
					{ 
						background: "#fff", 
						opacity: 0.6 
					},
					css: { 
				        padding:        20, 
				        textAlign:      "center", 
				        color:          "#555", 
				        border:         "3px solid #aaa", 
				        backgroundColor:"#fff", 
				        cursor:         "wait",
				        lineHeight:		"32px"
				    } 
				});
			jQuery("#submit_worldpay_payment_form").click();
		');
		
		return '<form action="'.esc_url( $worldpay_adr ).'" method="post" id="worldpay_payment_form">
				' . implode('', $worldpay_args_array) . '
				<input type="submit" class="button-alt" id="submit_worldpay_payment_form" value="'.__('Pay via WorldPay', 'woocommerce').'" /> <a class="button cancel" href="'.esc_url( $order->get_cancel_order_url() ).'">'.__('Cancel order &amp; restore cart', 'woocommerce').'</a>
			</form>';
		
	}
	
	/**
	 * Process the payment and return the result
	 **/
	function process_payment( $order_id ) {
		
		$order = new WC_Order( $order_id );
		
		if($this->woo_version >= 2.1){
			$redirect = $order->get_checkout_payment_url( true );			
		}else if( $woo_version < 2.1 ){
			$redirect = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('woocommerce_pay_page_id'))));
		}else{
			$redirect = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('woocommerce_pay_page_id'))));
		}
		
		return array(
			'result' 	=> 'success',
			'redirect'	=> $redirect
		);
		
		
	}
	
	/**
	 * receipt_page
	 **/
	function receipt_page( $order ) {
		
		echo '<p>'.__('Thank you for your order, please click the button below to pay with WorldPay.', 'woocommerce').'</p>';
		
		echo $this->generate_worldpay_form( $order );
		
	}
	
	/**
	 * Successful Payment!
	 **/
	function successful_request() {
		global $woocommerce;
		
		// Custom holds post ID
	    if ( !empty($_POST['cartId']) && !empty($_POST['MC_orderkey']) ) {
	
			$order = new WC_Order( (int) $_POST['cartId'] );
	        if ($order->order_key!==$_POST['MC_orderkey']) :
	        	if ($this->debug=='yes') $this->log->add( 'worldpay', 'Error: Order Key does not match MC_orderkey.' );
	        	exit;
	        endif;
	        
			if($_POST['transStatus'] == 'Y' && $_POST['instId'] == $this->instid)
			{
				$status = 'completed';       	
			}
			else
			{
				$status = 'failed';  
			}     
	        
	        if ($this->debug=='yes') $this->log->add( 'worldpay', 'Payment status: ' . $status );
	        
	        // We are here so lets check status and do actions
	        switch ($status) :
	            case 'completed' :
	            	
	            	// Check order not already completed
	            	if ($order->status == 'completed') :
	            		 if ($this->debug=='yes') $this->log->add( 'worldpay', 'Aborting, Order #' . $_POST['cartId'] . ' is already complete.' );
	            		 exit;
	            	endif;
	            	
	            	// Payment completed
					$order->add_order_note(sprintf(__('WorldPay Payment Completed. The Transaction Id is %s.', 'woothemes'), $_POST['transId']));
	                $order->payment_complete();
	                
	                if ($this->debug=='yes') $this->log->add( 'worldpay', 'Payment complete.' );
	                
	                // Store PP Details
	                update_post_meta( (int) $_POST['cartId'], 'Payer Email Address', $_POST['email']);
	                update_post_meta( (int) $_POST['cartId'], 'Transaction ID', $_POST['transId']);
	                update_post_meta( (int) $_POST['cartId'], 'Payer first name', $_POST['name']);
	                update_post_meta( (int) $_POST['cartId'], 'WorldPay Message', $_POST['rawAuthMessage']); 
					header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200); exit;
	                
	            break;
	            case 'failed' :
	                // Order failed
	                $order->update_status('failed', sprintf(__('Payment %s via IPN.', 'woocommerce'), strtolower($status) ) );
					header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK', true, 200); exit;
	            break;
	            default:
	            	// No action
	            break;
	        endswitch;
			
			exit;
			
	    }
		
	}
	
	function get_woo_version() {
	    
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
	    // Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';
		
		// If the plugin version number is set, return it 
		if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
			return $plugin_folder[$plugin_file]['Version'];
	
		} else {
			// Otherwise return null
			return NULL;
		}
	}
	
}

/**
 * Add the gateway to WooCommerce
 **/
function add_worldpay_gateway( $methods ) {
	$methods[] = 'woocommerce_worldpay'; return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_worldpay_gateway' );

}