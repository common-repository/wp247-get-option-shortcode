<?php
/*
	Program: WP247 Get Option Shortcode
	Version: 1.0.1
	Author: Wes Cleveland
	Author URI: http://wp247.net/
	Uses: weDevs Settings API wrapper class from http://tareq.weDevs.com Tareq's Planet
*/

if ( !class_exists( 'WP247_get_option_shortcode_settings' ) )
{
	define( 'WP247_GET_OPTION_SHORTCODE_PLUGIN_ADMIN_PATH', plugin_dir_path( __FILE__ ) );

	require_once WP247_GET_OPTION_SHORTCODE_PLUGIN_ADMIN_PATH . 'wp247-settings-api/wp247-settings-api.php';

/* Skip namespace usage due to errors
	class WP247_get_option_shortcode_settings extends \wp247sapi\WP247_settings_API
*/
	class WP247_get_option_shortcode_settings extends WP247_settings_API_2
	{

		/**
		 * WP247XNS Corequisite Notice
		 */
		private $wp247xns_client_corequisite_notice;

		/**
		 * Class Constructor
		 *
		 * @return void
		 */
		function __construct()
		{
			if ( WP247_GET_OPTION_SHORTCODE_COREQUISITE_NOTICE and is_admin() and current_user_can( 'manage_options' ) )
			{
				require_once WP247_GET_OPTION_SHORTCODE_PLUGIN_ADMIN_PATH . 'wp247xns-client-corequisite-notice/wp247xns-client-corequisite-notice.php';
				$this->wp247xns_client_corequisite_notice = new WP247XNS_Client_Corequisite_Notice( WP247_GET_OPTION_SHORTCODE_PLUGIN_NAME, '7 days' );
			}
			parent::__construct();
		}

		/**
		 * Returns Admin Menu
		 *
		 * @return array settings sections
		 */
		function get_settings_admin_menu()
		{
			return array( 'parent_slug'	=> 'tools'
						, 'page_title'	=> 'WP247 Get Option Shortcode'
						, 'menu_title'	=> 'Get Option Shortcode'
						, 'capability'	=> 'manage_options'
						, 'menu_slug'	=> 'wp247_get_option_shortcode_options'
						);
		}

		/**
		 * Returns all the settings sections
		 *
		 * @return array settings sections
		 */
		function get_settings_sections()
		{
			$shortcode = 'wp247_get_option';
			$sections = array(
				array(
					'id' => 'wp247_get_option_shortcode_usage',
					'save' => 'no',
					'title' => __( 'Usage', 'wp247-get-option-shortcode' ),
					'desc' => '<p>' . sprintf( __( 'WP247 Get Option Shortcode allows you to place WordPress option values anywhere shortcodes can be placed. Simply add the %s shortcode wherever you want to see a particular WordPress option.', 'wp247-get-option-shortcode' ), $shortcode ) . '</p><p>' . __( 'Syntax', 'wp247-get-option-shortcode' ) . ':</p><p style="margin-left: 32px;">[' . $shortcode . ' option="<strong>option</strong>" default="<strong>default</strong>" scope="<strong>scope</strong>"]</p><p>' . __( 'Where', 'wp247-get-option-shortcode' ) . ':</p><p style="margin-left: 32px;">' . __( '<strong>option</strong> is the name of the option whose value is to be inserted<br/><br/><strong>default</strong> is the value to be returned if <strong>option</strong> doesn\'t exist<br/><br/><strong>scope</strong> indicates which type of option is to be retrieved. <strong>scope="site"</strong> will retrieve options using the WordPress <strong>get_site_option</strong> function. All other values are ignored and the WordPress <strong>get_option</strong> function will be used to retrieve the desired <strong>option</strong> value.' ) . '</p><p>' . __( 'Example', 'wp247-get-option-shortcode' ) . ':</p><p style="margin-left: 32px;">' . __( 'Set a copyright notice with a link to your website in a text widget using WordPress options instead of hard-coding:', 'wp247-get-option-shortcode'  ) . '</p><p style="margin-left: 48px;"><strong>Copyright &lt;a href="[' . $shortcode . ' option=\'siteurl\']"&gt;[' . $shortcode  . ' option=\'blogname\']"&lt;/a&gt;. All rights reserved.</strong></p><p style="margin-left: 48px;">' . __( 'will be changed into', 'wp247-get-option-shortcode' ) . ':<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Copyright &lt;a href="' . get_option( 'siteurl' ) . '"&gt;' . get_option( 'blogname' ) . '&lt;/a&gt;. All rights reserved.</strong><br/><br/>' . __( 'and will appear as', 'wp247-get-option-shortcode' ) . ':<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Copyright <a href="' . get_option( 'siteurl' ) . '">' . get_option( 'blogname' ) . '</a>. All rights reserved.</strong></p>',
				),
				array(
					'id' => 'wp247_get_option_shortcode',
					'title' => __( 'Settings', 'wp247-get-option-shortcode' ),
					'desc' => __( 'WP247 Get Option Shortcode Settings', 'wp247-get-option-shortcode' ),
				),
			);
			return $sections;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields()
		{
			$settings_fields = array(
				'wp247_get_option_shortcode' => array(
					array(
						'name' => 'shortcode',
						'label' => __( 'Shortcode', 'wp247-get-option-shortcode' ),
//						'intro' => __( '', 'wp247-get-option-shortcode' ),
						'desc' => '<br/>Shortcode to be used throught the site.',
						'type' => 'text',
						'sanitize_callback' => array( &$this, 'sanitize_callback' )
//						'options' => array(
//						),
					),
					array(
						'name' => 'execution',
						'label' => __( 'Execution', 'wp247-get-option-shortcode' ),
						'intro' => __( 'WP247 Get Option Shortcode can be executed in a number of areas. By default, all areas are set. Eliminating unnecessary areas will improve your site\'s performance.<br/><br/>Execute WP247 Get Option Shortcode in:<br/>', 'wp247-get-option-shortcode' ),
						'type' => 'multicheck',
						'options' => array(
							'widget_text' => 'Widget text',
							'comment_excerpt' => 'Comment excerpts',
							'comment_text' => 'Comment text',
							'the_content_rss' => 'Content RSS',
							'the_content_feed' => 'Content feed',
							'the_excerpt_rss' => 'Excerpt RSS',
							'comment_text_rss' => 'Comment RSS text',
						),
					),
				),
			);

			return $settings_fields;
		}

		public function sanitize_callback( $value, $option )
		{
			global $wp247_option_shortcode;
			$value = str_replace( array( ' ', '-' ), '_', strip_tags( trim( $value, ' _-' ) ) );
			if ( empty( $value ) ) $value = $wp247_option_shortcode->get_option( $option );
			return $value;
		}

		/**
		 * Returns all the settings infobar
		 *
		 * @return array settings infobar
		 */
		function get_settings_infobar()
		{
			return array(
 'Version:' => '<ul>' . WP247_GET_OPTION_SHORTCODE_VERSION . '</ul>'
,'About this plugin' => '
<ul>
	<li><a href="http://wp247.net/wp247-get-option-shortcode/" target="_blank">Plugin background</a></li>
	<li><a href="http://wordpress.org/support/plugin/wp247-get-option-shortcode" target="_blank">Plugin support</a></li>
	<li><a href="http://wordpress.org/support/view/plugin-reviews/wp247-get-option-shortcode" target="_blank">Review this plugin</a></li>
</ul>'
, 'Enjoy this plugin?' => '
<p>If you find this plugin useful, would you consider making a donation to one or more of my favorite causes?</p>
<p><a class="wp247sapi-button button-primary" href="http://www.ijm.org/" target="_blank">Help rescue the oppressed</a></p>
<p><a class="wp247sapi-button button-primary" href="http://www.compassion.com/donate.htm" target="_blank">Show compassion on an impoverished child</a></p>
<p><a class="wp247sapi-button button-primary" href="https://thelastwell.org/" target="_blank">Give someone clean and safe drinking water</a></p>
<p><a class="wp247sapi-button button-primary" href="https://www.tbmtx.org//" target="_blank">Help provide disaster relief</a></p>
<p><a class="wp247sapi-button button-primary" href="https://www.samaritanspurse.org//" target="_blank">Help provide humanitarian aid</a></p>
<p><a class="wp247sapi-button button-primary" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RM26LBV2K6NAU" target="_blank">Buy me a coffee :)</a></p>
' );
		}

		/**
		 * Returns the infobar width
		 *
		 * @return integer infobar width
		 */
		function get_infobar_width()
		{
			return 15;
		}

		/**
		 * Enqueue scripts and styles
		 */
/*
		function enqueue_scripts()
		{
			wp_enqueue_style( 'wp247-get-option-shortcode-admin-styles', plugins_url( 'wp247-get-option-shortcode-admin.css', __FILE__ ) );
		}
*/

		/**
		 * Returns the head scripts and styles
		 *
		 * @return string head scripts and styles
		 * @return array  head scripts and styles
		 */
		function get_head_scripts()
		{
			return array();
		}

	}

	$wp247_get_option_shortcode_settings = new WP247_get_option_shortcode_settings();
}