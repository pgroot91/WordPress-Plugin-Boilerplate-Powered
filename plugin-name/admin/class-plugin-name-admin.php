<?php

/**
 * Plugin Name.
 *
 * @package   Plugin_Name_Admin
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package Plugin_Name_Admin
 * @author  Your Name <email@example.com>
 */
class Plugin_Name_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
		  return;
		  } */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Plugin_Name" to the name of your initial plugin class
		 *
		 */
		$plugin = Plugin_Name::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->version = $plugin->get_plugin_version();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		// Load admin style in dashboard for the At glance widget
		add_action( 'admin_head-index.php', array( $this, 'enqueue_admin_styles' ) );

		// At Glance Dashboard widget for your cpts
		add_filter( 'dashboard_glance_items', array( $this, 'cpt_dashboard_support' ), 10, 1 );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		//Add bubble notification for cpt pending
		add_action( 'admin_menu', array( $this, 'pending_cpt_bubble'), 999 );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Choose the Custom Meta Box Library and remove the other
		 * 
		 * @TODO:
		 * 
		 *  Custom meta Boxes by HumanMade | PS: include natively Select2 for select box
		 *  NOTE: Actually not support options page, there is a branch cmb-anywhere-2 in developing
		 * 	https://github.com/humanmade/Custom-Meta-Boxes/		
		  if ( !class_exists( 'cmb_Meta_Box' ) ) {
		  require_once( plugin_dir_path( __FILE__ ) . 'includes/CMB/custom-meta-boxes.php' );
		  }
		 *  Custom Metabox and Fields for Wordpress
		 * 	https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress
		 */
		add_action( 'init', function() {
			if ( !class_exists( 'cmb_Meta_Box' ) ) {
				require_once( plugin_dir_path( __FILE__ ) . 'includes/CMBF/init.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/CMBF-Select2/cmb-field-select2.php' );
			}
		}, 9999 );

		/*
		 * Filter is the same
		 */
		add_filter( 'cmb_meta_boxes', array( $this, 'cmb_demo_metaboxes' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
		  return;
		  } */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id || strpos( $_SERVER[ 'REQUEST_URI' ], 'index.php' ) || strpos( $_SERVER[ 'REQUEST_URI' ], get_bloginfo( 'wpurl' ) . '/wp-admin/' ) ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array( 'dashicons' ), Plugin_Name::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Plugin_Name::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
				__( 'Page Title', $this->plugin_slug ), __( 'Menu Text', $this->plugin_slug ), 'manage_options', $this->plugin_slug, array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
				array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings' ) . '</a>'
				), $links
		);
	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	/**
	 * Add the counter of your CPTs in At Glance widget in the dashboard<br>
	 * NOTE: add in $post_types your cpts, remember to edit the css style (admin/assets/css/admin.css) for change the dashicon<br>
	 *
	 *        Reference:  http://wpsnipp.com/index.php/functions-php/wordpress-post-types-dashboard-at-glance-widget/
	 *
	 * @since    1.0.0
	 */
	public function cpt_dashboard_support( $items = array() ) {
		$post_types = array( 'demo' );
		foreach ( $post_types as $type ) {
			if ( !post_type_exists( $type ) ) {
				continue;
			}
			$num_posts = wp_count_posts( $type );
			if ( $num_posts ) {
				$published = intval( $num_posts->publish );
				$post_type = get_post_type_object( $type );
				$text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $published, $this->plugin_slug );
				$text = sprintf( $text, number_format_i18n( $published ) );
				if ( current_user_can( $post_type->cap->edit_posts ) ) {
					$items[] = '<a class="' . $post_type->name . '-count" href="edit.php?post_type=' . $post_type->name . '">' . sprintf( '%2$s', $type, $text ) . "</a>\n";
				} else {
					$items[] = sprintf( '%2$s', $type, $text ) . "\n";
				}
			}
		}
		return $items;
	}

	/**
	 * Bubble Notification for pending cpt<br>
	 * NOTE: add in $post_types your cpts<br>
	 *
	 *        Reference:  http://wordpress.stackexchange.com/questions/89028/put-update-like-notification-bubble-on-multiple-cpts-menus-for-pending-items/95058
	 *
	 * @since    1.0.0
	 */
	function pending_cpt_bubble() {
		global $menu;

		$post_types = array( 'demo' );
		foreach ( $post_types as $type ) {
			if ( !post_type_exists( $type ) ) {
				continue;
			}
			// Count posts
			$cpt_count = wp_count_posts( $type );

			if ( $cpt_count->pending ) {
				// Menu link suffix, Post is different from the rest
				$suffix = ( 'post' == $type ) ? '' : "?post_type=$type";

				// Locate the key of 
				$key = self::recursive_array_search_php( "edit.php$suffix", $menu );

				// Not found, just in case 
				if ( !$key )
					return;

				// Modify menu item
				$menu[ $key ][ 0 ] .= sprintf(
						'<span class="update-plugins count-%1$s"><span class="plugin-count">%1$s</span></span>', $cpt_count->pending
				);
			}
		}
	}

	/**
	 * Required for the bubble notification<br>
	 *
	 *        Reference:  http://wordpress.stackexchange.com/questions/89028/put-update-like-notification-bubble-on-multiple-cpts-menus-for-pending-items/95058
	 *
	 * @since    1.0.0
	 */
	private function recursive_array_search_php( $needle, $haystack ) {
		foreach ( $haystack as $key => $value ) {
			$current_key = $key;
			if ( $needle === $value OR ( is_array( $value ) && self::recursive_array_search_php( $needle, $value ) !== false) ) {
				return $current_key;
			}
		}
		return false;
	}

	/**
	 * NOTE:     Your metabox on Demo CPT
	 *
	 * @since    1.0.0
	 */
	public function cmb_demo_metaboxes( array $meta_boxes ) {
		$meta_boxes[ 'test_metabox' ] = array(
			'id' => 'test_metabox',
			'title' => __( 'Demo Metabox', $this->plugin_slug ),
			'pages' => array( 'demo', ), // Post type
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true, // Show field names on the left
			'fields' => array(
				array(
					'name' => __( 'Text', $this->plugin_slug ),
					'desc' => __( 'field description (optional)', $this->plugin_slug ),
					'id' => $this->plugin_slug . '_test_text',
					'type' => 'text',
				),
				array(
					'name' => __( 'Text Small', $this->plugin_slug ),
					'desc' => __( 'field description (optional)', $this->plugin_slug ),
					'id' => $this->plugin_slug . '_test_textsmall',
					'type' => 'text_small',
				), ),
		);

		return $meta_boxes;
	}

}
