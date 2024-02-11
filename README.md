# WordPress Plugin Meta Library

This library handles the registration of custom plugin action links and row meta links, allowing developers to easily
add external links to the plugin's action links or row meta section in the WordPress admin Plugins page.

## Minimum Requirements ##

* **PHP:** 7.4

## Installation ##

Register Plugin Meta is a developer library, not a plugin, which means you need to include it somewhere in your own
project.

You can use Composer:

```bash
composer require arraypress/register-plugin-meta
```

## Using the Plugin Meta Library

The `register_plugin_meta` function simplifies the process of adding custom action links and row meta links to your
WordPress plugin. You can use it to enhance your plugin's functionality by adding external links to the admin Plugins
page. Here's an example of how to use it:

```php 
// Include the Composer-generated autoload file.
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

// Define an array of external links to add to your plugin.
$external_links = array(
	'support'    => array(
		'action' => false,
		'label'  => __( 'Support', 'your-text-domain' ),
		'url'    => 'https://example.com/support',
	),
	'extensions' => array(
		'action' => false,
		'label'  => __( 'Get More Extensions', 'your-text-domain' ),
		'url'    => 'https://example.com/extensions',
	),
	'settings'   => array(
		'action' => true,
		'label'  => __( 'Settings', 'your-text-domain' ),
		'url'    => admin_url( 'options-general.php?page=plugin-settings' ),
		'utm'    => false,
	),
);

// Define UTM parameters if desired.
$utm_args = array(
	'utm_source'   => 'your-source',
	'utm_medium'   => 'your-medium',
	'utm_campaign' => 'your-campaign',
);

// Create an instance of the Plugin_Meta class.
register_plugin_meta( __FILE__, $external_links, $utm_args );
```

1. The `vendor/autoload.php` file is included to autoload classes from Composer.

2. The `$external_links` array defines the external links you want to add to your plugin. Each link is an array with the
   following properties:

| Key       | Description                                                                    | Required | Default Value       | Example                         |
|-----------|--------------------------------------------------------------------------------|----------|---------------------|---------------------------------|
| `action`  | Set to true if it's an action link or false for a row meta link.               | Optional | `false`             | `true`                          |
| `label`   | The label or text for the link.                                                | Required | `''` (empty string) | `'Support'`                     |
| `url`     | The URL the link should point to.                                              | Required | `''` (empty string) | `'https://example.com/support'` |
| `utm`     | Set to true to add UTM parameters or false to omit them.                       | Optional | `true`              | `false`                         |
| `new_tab` | Set to true to open the link in a new tab or false to open it in the same tab. | Optional | `true`              | `false`                         |

3. The `$utm_args` array defines the default UTM parameters for all links.

An instance of the `Plugin_Meta` class is created with the provided settings, adding the external links to your
WordPress plugin's admin page.

## Contributions

Contributions to this library are highly appreciated. Raise issues on GitHub or submit pull requests for bug
fixes or new features. Share feedback and suggestions for improvements.

## License: GPLv2 or later

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.