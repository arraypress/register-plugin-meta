<?php
/**
 * Initializes and registers the Plugin_Meta class to manage custom plugin action links and row meta links within
 * the WordPress admin Plugins page.
 *
 * This utility function sets up a Plugin_Meta instance with a given array of external links and optional UTM
 * parameters
 * for enhancing plugin listings with additional action links or information. It simplifies the process of adding
 * custom
 * links such as 'Support', 'Donate', or 'Settings' to the plugin's action links or the row meta beneath the plugin's
 * description.
 *
 * The function expects an array of external links, each specifying the URL, label, and whether to append UTM
 * parameters, and an optional callable for error handling. Initialization errors are caught and managed, either
 * silently or using the provided error callback function.
 *
 * Example usage:
 * $external_links = [
 *     [
 *         'action' => true,
 *         'label' => __('Support', 'text-domain'),
 *         'url' => 'https://example.com/support',
 *         'utm' => true,
 *     ],
 *     // ... other links
 * ];
 * $utm_args = [
 *     'utm_source' => 'plugin-listing',
 *     'utm_medium' => 'link',
 *     'utm_campaign' => 'support'
 * ];
 * register_plugin_meta(__FILE__, $external_links, $utm_args, function($exception) {
 *     // Error handling logic here
 * });
 *
 * By invoking this function, the Plugin_Meta is registered to filter hooks 'plugin_action_links' and
 * 'plugin_row_meta',
 * allowing the specified links to be added to the plugin's listing on the WordPress admin Plugins page.
 *
 * @package     ArrayPress/Utils/WP/Plugin_Meta
 * @copyright   Copyright (c) 2023, ArrayPress Limited
 * @license     GPL2+
 * @since       1.0.0
 * @author      David Sherlock
 */

namespace ArrayPress\Utils\WP;

use Exception;

if ( ! function_exists( 'register_plugin_meta' ) ) {
	/**
	 * Initializes the Plugin_Meta with given links and UTM parameters and handles exceptions.
	 *
	 * @param string        $file           The plugin file path.
	 * @param array         $external_links Array of external links.
	 * @param array         $utm_args       Array of UTM arguments.
	 * @param callable|null $error_callback Callback function for error handling.
	 *
	 * @return Plugin_Meta|null The initialized Plugin_Meta or null on failure.
	 */
	function register_plugin_meta(
		string $file,
		array $external_links = [],
		array $utm_args = [],
		?callable $error_callback = null
	): ?Plugin_Meta {
		try {
			return new Plugin_Meta( $file, $external_links, $utm_args );
		} catch ( Exception $e ) {
			if ( is_callable( $error_callback ) ) {
				call_user_func( $error_callback, $e );
			}

			// Handle the exception or log it if needed
			return null; // Return null on failure
		}
	}
}

if ( ! function_exists( 'register_edd_plugin_meta' ) ) {
	/**
	 * Initializes the Plugin_Meta with EDD specific links and UTM parameters.
	 *
	 * @param string        $file             The plugin file path.
	 * @param string        $settings_tab     The EDD settings tab identifier.
	 * @param string        $settings_section The EDD settings section identifier.
	 * @param array         $external_links   Array of external links.
	 * @param array         $utm_args         Array of UTM arguments.
	 * @param callable|null $error_callback   Callback function for error handling.
	 *
	 * @return Plugin_Meta|null The initialized Plugin_Meta or null on failure.
	 */
	function register_edd_plugin_meta(
		string $file,
		string $settings_tab = '',
		string $settings_section = '',
		array $external_links = [],
		array $utm_args = [],
		?callable $error_callback = null
	): ?Plugin_Meta {
// Default links specific to ArrayPress.
		$default_links = [
			'support'    => [
				'label' => __( 'Support', 'arraypress' ),
				'url'   => 'https://arraypress.com/support',
			],
			'extensions' => [
				'label' => __( 'Extensions', 'arraypress' ),
				'url'   => 'https://arraypress.com/products',
			],
		];

		// Merge the provided external links with the defaults.
		$external_links = wp_parse_args( $external_links, $default_links );

		// Add EDD-specific 'Settings' link if provided.
		if ( function_exists( 'edd_get_admin_url' ) && $settings_tab && $settings_section ) {
			$external_links['settings'] = [
				'action' => true,
				'label'  => __( 'Settings', 'arraypress' ),
				'url'    => edd_get_admin_url( [
					'page'    => 'edd-settings',
					'tab'     => $settings_tab,
					'section' => $settings_section,
				] ),
				'utm'    => true,
			];
		}

		// Initialize the Plugin_Meta with the merged links and UTM parameters.
		return register_plugin_meta( $file, $external_links, $utm_args, $error_callback );
	}
}
