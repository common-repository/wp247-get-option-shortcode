<?php
/*
	Plugin Name: WP247 Get Option Shortcode
	Version: 1.2
	Description: Include WordPress options anywhere shortcodes are accepted
	Tags: options, shortcode, get_option
	Author: wp247
	Author URI: http://wp247.net/
	Text domain: wp247-get-option-shortcode
	Uses: weDevs Settings API wrapper class from http://tareq.weDevs.com Tareq's Planet
*/

if ( !class_exists( 'WP247_get_option_shortcode' ) )
{
	define( 'WP247_GET_OPTION_SHORTCODE_VERSION', '1.2' );
	define( 'WP247_GET_OPTION_SHORTCODE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	define( 'WP247_GET_OPTION_SHORTCODE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'WP247_GET_OPTION_SHORTCODE_PLUGIN_NAME', 'WP247 Get Option Shortcode' );
	define( 'WP247_GET_OPTION_SHORTCODE_PLUGIN_ID', basename( dirname( __FILE__ ) ) );
	define( 'WP247_GET_OPTION_SHORTCODE_PLUGIN_TEXT_DOMAIN', WP247_GET_OPTION_SHORTCODE_PLUGIN_ID );
	define( 'WP247_GET_OPTION_SHORTCODE_COREQUISITE_NOTICE', false );

	class WP247_get_option_shortcode
	{
		private $options;
		
		private $defaults = array( 'shortcode' => 'wp247_get_option'
								  ,'execution' => array( 'widget_text'		=> 'widget_text'
														,'comment_excerpt'	=> 'comment_excerpt'
														,'comment_text'		=> 'comment_text'
														,'the_content_rss'	=> 'the_content_rss'
														,'the_content_feed'	=> 'the_content_feed'
														,'the_excerpt_rss'	=> 'the_excerpt_rss'
														,'comment_text_rss'	=> 'comment_text_rss'
														)
								 );
		
		private $shortcode_atts = array( 'option' => '', 'default' => '', 'scope' => '', 'translate' => '' );
		
		function __construct()
		{
			add_action( 'wp_loaded', array( $this, 'do_action_wp_loaded' ) );
			add_action( 'admin_head', array( $this, 'do_action_admin_head' ) );
			add_filter( 'wp247xns_client_extension_poll_plugin_'.WP247_GET_OPTION_SHORTCODE_PLUGIN_ID, array( $this, 'do_filter_wp247xns_client_extension_poll' ) );
			$shortcode = $this->get_option( 'shortcode' );
			if ( !empty( $shortcode ) )
			{
				add_shortcode( $shortcode, array( $this, 'do_shortcode' ) );
				if ( isset( $this->options[ 'execution' ] ) and is_array( $this->options[ 'execution' ] ) )
				{
					foreach ( $this->options[ 'execution' ] as $key => $value ) add_filter( $key, 'do_shortcode' );
				}
			}
		}

		public function do_action_wp_loaded()
		{
			if ( current_user_can( 'manage_options' ) )
				require_once WP247_GET_OPTION_SHORTCODE_PLUGIN_PATH . 'admin/wp247-get-option-shortcode-admin.php';
		}

		private function get_options()
		{
			if ( empty( $this->options ) )
			{
				$this->options = get_option( 'wp247_get_option_shortcode', null );
				if ( empty( $this->options ) )
				{
					$this->options = $this->defaults;
					add_option( 'wp247_get_option_shortcode', $this->options );
				}
			}
			return $this->options;
		}

		public function get_option( $option, $default = null )
		{
			$options = $this->get_options();
			if ( isset( $options[ $option ] ) and !empty( $options[ $option ] ) ) $value  = $options[ $option ];
			else if ( !empty( $default ) ) $value = $default;
			else if ( isset( $this->defaults[ $option ] ) ) $value = $this->defaults[ $option ];
			else $value = null;
			if ( is_array( $value ) ) $value = serialize( $value );
			return $value;
		}

		public function do_shortcode( $atts, $content = null, $tag = null )
		{
			extract( shortcode_atts( $this->shortcode_atts, $atts ) );
			if ( 'site' == strtolower( $scope ) )
				$value = empty( $option ) ? '' : get_site_option( $option, $default );
			else $value = empty( $option ) ? '' : get_option( $option, $default );
			if ( !empty( $translate ) )
			{
			}
			return $value;
		}

		/*
		 * Tell WP247 Extension Notification Client about us
		 */
		function do_filter_wp247xns_client_extension_poll( $extensions )
		{
			return array(
						 'id'			=> WP247_GET_OPTION_SHORTCODE_PLUGIN_ID
						,'version'		=> WP247_GET_OPTION_SHORTCODE_VERSION
						,'name'			=> 'WP247 Get Option Shortcode'
						,'type'			=> 'plugin'
						,'server_url'	=> 'http://wp247.net/wp-admin/admin-ajax.php'
						,'frequency'	=> '1 day'
					);
		}

		/*
		 * Check to see if WP247 Extension Notification Client is loaded
		 *
		 * @return array extensions
		 */
		function do_action_admin_head()
		{
			if ( current_user_can( 'manage_options' )
			 and is_admin()
			 and !is_plugin_active( 'wp247-extension-notification-client/wp247-extension-notification-client.php' )
			) add_thickbox();
		}

	}

	global $wp247_option_shortcode;
	$wp247_option_shortcode = new WP247_get_option_shortcode();
}
