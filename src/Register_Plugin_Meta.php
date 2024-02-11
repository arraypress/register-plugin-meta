<?php
/**
 * Register Plugin Meta Class
 *
 * This class handles the registration of custom plugin action links and row meta links,
 * allowing developers to easily add external links to the plugin's action links or row meta section
 * in the WordPress admin Plugins page.
 *
 * @package     arraypress/register-plugin-meta
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 * @description Provides a flexible way to enhance WordPress plugin functionality with custom admin links.
 */

namespace ArrayPress\Utils\WP;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( __NAMESPACE__ . '\\Register_Plugin_Meta' ) ) :

	/**
	 * Class Plugin_Meta
	 *
	 * Handles the registration of custom plugin action links and row meta links.
	 * This class allows developers to add external links to the plugin's action links or row meta section
	 * in the WordPress admin Plugins page.
	 *
	 * Usage:
	 * $external_links = array(
	 *     array(
	 *         'action' => true,          // Whether to use action link (true) or row meta link (false).
	 *         'label' => __('Support', 'your-text-domain'), // Link label.
	 *         'url' => 'https://example.com/support',       // Link URL.
	 *         'utm' => true,              // Whether to add UTM parameters.
	 *     ),
	 *     // Add more link entries as needed.
	 * );
	 *
	 * $utm_args = array(
	 *     'utm_source'   => 'your-source',
	 *     'utm_medium'   => 'your-medium',
	 *     'utm_campaign' => 'your-campaign',
	 * );
	 *
	 * $plugin_meta = new Plugin_Meta( __FILE__, $external_links, $utm_args );
	 */
	class Register_Plugin_Meta {

		/**
		 * @var string Plugin file path.
		 */
		protected string $file = '';

		/**
		 * @var string Plugin basename.
		 */
		protected string $basename = '';

		/**
		 * @var array Array of external links.
		 */
		protected array $external_links = [];

		/**
		 * @var array Array of UTM arguments.
		 */
		protected array $utm_args = [];

		/**
		 * Plugin_Meta constructor.
		 *
		 * @param string $file           Plugin file path.
		 * @param array  $external_links Array of external links.
		 * @param array  $utm_args       Array of UTM arguments.
		 *
		 * @throws \InvalidArgumentException If the plugin file path is empty.
		 */
		public function __construct( string $file = '', array $external_links = [], array $utm_args = [] ) {
			if ( empty( $file ) ) {
				throw new \InvalidArgumentException( 'Plugin file path must be provided.' );
			}

			if ( ! is_array( $external_links ) ) {
				throw new \InvalidArgumentException( '$external_links must be an array.' );
			}

			if ( ! is_array( $utm_args ) ) {
				throw new \InvalidArgumentException( '$utm_args must be an array.' );
			}

			$this->file           = $file;
			$this->basename       = \plugin_basename( $this->file );
			$this->external_links = $external_links;
			$this->utm_args       = $utm_args;

			$this->hooks();
		}

		/**
		 * Register hooks for plugin links.
		 */
		protected function hooks(): void {
			add_filter( 'plugin_action_links', array( $this, 'register_plugin_action_links' ), 10, 4 );
			add_filter( 'plugin_row_meta', array( $this, 'register_plugin_row_meta' ), 10, 4 );
		}

		/**
		 * Register plugin action links.
		 *
		 * @param array  $links       Existing plugin action links.
		 * @param string $plugin_file Current plugin file.
		 *
		 * @return array Updated plugin action links.
		 */
		public function register_plugin_action_links( array $links, string $plugin_file ): array {
			return $this->register_links( $links, $plugin_file, 'action' );
		}

		/**
		 * Register plugin row meta links.
		 *
		 * @param array  $links       Existing plugin row meta links.
		 * @param string $plugin_file Current plugin file.
		 *
		 * @return array Updated plugin row meta links.
		 */
		public function register_plugin_row_meta( array $links, string $plugin_file ): array {
			return $this->register_links( $links, $plugin_file, 'row_meta' );
		}

		/**
		 * Register plugin links.
		 *
		 * @param array  $links       Existing plugin links.
		 * @param string $plugin_file Current plugin file.
		 * @param string $position    The position where the links should be added ('action' or 'row_meta').
		 *
		 * @return array Updated plugin links.
		 */
		protected function register_links( array $links, string $plugin_file, string $position ): array {
			if ( strpos( $plugin_file, $this->basename ) !== false ) {
				foreach ( $this->external_links as $key => $link_data ) {
					$key = sanitize_key( $key );

					$defaults  = [
						'action'  => false,
						'label'   => '',
						'url'     => '',
						'utm'     => true,
						'new_tab' => true, // Default to opening in a new tab
					];
					$link_data = wp_parse_args( $link_data, $defaults );

					if ( $link_data['action'] ) {
						$link_data['position'] = 'action';
					} else {
						$link_data['position'] = 'row_meta';
					}

					if ( $position === $link_data['position'] ) {
						$url            = $link_data['url'];
						$label          = $link_data['label'];
						$should_add_utm = $link_data['utm'];
						$new_tab        = $link_data['new_tab']; // Use the new_tab parameter from link_data

						if ( $url && $label ) {
							$utm_url       = $should_add_utm ? $this->get_plugin_row_utm_url( $url ) : $url;
							$target        = $new_tab ? 'target="_blank"' : ''; // New tab or same tab
							$links[ $key ] = '<a href="' . esc_url( $utm_url ) . '" ' . $target . '>' . esc_html( $label ) . '</a>';
						}
					}
				}
			}

			return $links;
		}

		/**
		 * Get the URL with UTM parameters.
		 *
		 * @param string $url Original URL.
		 *
		 * @return string URL with UTM parameters.
		 */
		private function get_plugin_row_utm_url( string $url = '' ): string {
			$args = wp_parse_args( $this->utm_args, [
				'utm_source'   => 'plugins-page',
				'utm_medium'   => 'plugin-row',
				'utm_campaign' => 'admin',
			] );

			return add_query_arg( $args, $url );
		}
	}

endif;
