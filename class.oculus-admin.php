<?php

class Oculus_Admin {

	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'oc-appkey' ) {
            self::log( "enter" );
			self::enter_api_key();
		}
	}

	public static function init_hooks() {

		self::$initiated = true;

		add_action( 'admin_menu', array( 'Oculus_Admin', 'admin_menu' ), 5 ); 
		add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'oculus.php'), array( 'Oculus_Admin', 'admin_plugin_settings_link' ) );
	}

	public static function admin_plugin_settings_link( $links ) { 
  		$settings_link = '<a href="'.self::get_page_url().'">'.__('Settings', 'oculus').'</a>';
  		array_unshift( $links, $settings_link ); 
  		return $links; 
	}

	public static function get_page_url() {

		$args = array( 'page' => 'oculus-key-config' );

		$url = add_query_arg( $args, admin_url( 'options-general.php' ) );

		return $url;
	}


	public static function admin_menu() {
	    self::load_menu();
	}

	public static function load_menu() {
	    $hook = add_options_page( __('Antispam', 'oculus'), __('Antispam', 'oculus'), 'manage_options', 'oculus-key-config', array( 'Oculus_Admin', 'display_page' ) );

	}

	public static function display_page() {
        
        self::display_start_page();
	}

	public static function display_start_page() {
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'delete-key' ) {
					delete_option( 'oculus_api_key' );
			}
		}

		echo '<h2">'.esc_html__('Antispam', 'oculus').'</h2>';

		Oculus::view( 'start' );
	}

	public static function save_key( $appkey , $appsecret , $accesskeyid , $accesskeysecret ) {

        update_option( 'oculus_appkey', $appkey );
        update_option( 'oculus_appsecret', $appsecret );
        update_option( 'oculus_accesskeyid', $accesskeyid );
        update_option( 'oculus_accesskeysecret', $accesskeysecret );
//        self::log( get_option('oculus_appkey')."|".get_option('oculus_appsecret')."|".get_option('oculus_accesskeyid')."|".get_option('oculus_accesskeysecret') );
				
	}

	public static function enter_api_key() {
        self::log( "key" );
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('No Permission', 'oculus'));

        self::save_key( $_POST["appkey"] , $_POST["appsecret"] , $_POST["accesskeyid"] , $_POST["accesskeysecret"]  );

		return true;
	}


	public static function log( $oculus_debug ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG )
			error_log( print_r( compact( 'oculus_debug' ), 1 ) ); 
	}


}


