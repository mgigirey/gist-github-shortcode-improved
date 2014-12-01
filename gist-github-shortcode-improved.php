<?php
/*
 * Plugin Name: Gist GitHub Shortcode Improved
 * Plugin URI: https://github.com/mgigirey/gist-github-shortcode-improved.git
 * Description: Adds Github Gists in your posts via shortcode, improved version.
 * Version: 1.0.0
 * Author: Marcelo Gigirey
 * Author URI: https://updatemycode.com/
 * License: GPLv2 or later
 * Text Domain: gistgsimp
 * Domain Path: /languages/
 */

class Gist_Github_Shortcode_Improved {

	/**
	 * Class construct.
	 */
	public function __construct() {

		// Load the text domain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 0 );

		// Register the shortcode.
		add_shortcode( 'gist', array( $this, 'shortcode' ) );

		// Init the buttons.
		add_action( 'init', array( $this, 'buttons_init' ) );

		// Register the modal dialog ajax request.
		add_action( 'wp_ajax_github_gist_shortcode_improved', array( $this, 'dialog' ) );
	}

	/**
	 * Load Plugin textdomain.
	 *
	 * @return void.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'gistgsimp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Gist shortcode.
	 *
	 * Usage:
	 * [gist id="Gist ID" file="Gist File (optional)"]
	 *
	 * @param  array  $atts Shortcodes attributes (id and file).
	 *
	 * @return string       Gist code.
	 */
	function shortcode( $atts ) {
		extract( shortcode_atts( array(
			'user_id' => '',
			'id' => '',
			'file' => '',
		), $atts ) );

		$gist = sprintf(
			'<script src="https://gist.github.com/%s%s.js%s"></script>',
			$user_id ? esc_attr( $user_id ) . '/' : '',
			esc_attr( $id ),
			$file ? '?file=' . esc_attr( $file ) : ''
		);

		return $gist;
	}

	/**
	 * Add custom buttons in TinyMCE.
	 */
	function register_buttons( $buttons ) {
		array_push( $buttons, '|', 'gist' );
		return $buttons;
	}

	/**
	 * Register button scripts.
	 */
	function add_buttons( $plugin_array ) {
		$plugin_array['gist'] = plugins_url( 'tinymce/gist.js' , __FILE__ );
		return $plugin_array;
	}

	/**
	 * Register buttons in init.
	 */
	function buttons_init() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( true == get_user_option( 'rich_editing') ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_buttons' ) );
			add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );
		}
	}

	/**
	 * Displays the shortcode modal dialog.
	 *
	 * @return string  Modal Dialog HTML.
	 */
	function dialog() {
		@ob_clean();

		include plugin_dir_path( __FILE__ ) . 'tinymce/dialog.php';

		die();
	}

} // close Gist_Github_Shortcode class.

new Gist_Github_Shortcode_Improved;
