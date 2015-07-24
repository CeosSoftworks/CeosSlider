<?php
/**
 * Plugin Name: CEOS Slider
 * Plugin URI: http://www.ceossoftworks.com.br/products/wordpress_slider
 * Version: 1.0
 * Description: A simple and somewhat useful Wordpress slider.
 * Author: Jeferson Oliveira @ CEOS Softworks
 * Author URI: http://www.ceossoftworks.com.br
 * License: Copyright© 2015, CEOS Softworks. Licensed under the Apache License, Version 2.0.
 * License URI: http://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: ceos_slider
 */

/**
 * @version 1.0.0.0
 * @author Jeferson Oliveira <contato@ceossoftworks.com.br>
 * @copyright Copyright(c) 2015, Jeferson Oliveira. Licensed under the Apache
 *     License, Version 2.0 (the "License"); you may not use this file except
 *     in compliance with the License. You may obtain a copy of the License at
 *     
 *     http://www.apache.org/licenses/LICENSE-2.0
 *     
 *     Unless required by applicable law or agreed to in writing, software
 *     distributed under the License is distributed on an "AS IS" BASIS,
 *     WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *     See the License for the specific language governing permissions and
 *     limitations under the License.
 */

namespace CEOS\Slider;

error_reporting(-1);
ini_set('display_errors', 'On');

/**
 * Deny direct access to the plugin file.
 */

defined('ABSPATH') or die ('This is not for you. It never was for you.');

/**
 * For internationalization purposes.
 */

load_plugin_textdomain('ceos_slider');

/**
 * Prefix used to identify tables and other elements belonging to this plugin.
 */

define(__NAMESPACE__.'\PLUGIN_PREFIX', 'ceos_slider_');

/**
 * Provides access to the main plugin filepath to other scripts within
 * the namespace.
 */

define(__NAMESPACE__.'\PLUGIN_FILE', __FILE__);

/**
 * Provides access to the path of the plugin directory to other scripts within
 * the namespace.
 */

define(__NAMESPACE__.'\PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Provides access to the URL of the plugin directory to other scripts within
 * the namespace.
 */

define(__NAMESPACE__.'\PLUGIN_PATH_URL', plugin_dir_url(__FILE__));

/**
 * A few essential fu