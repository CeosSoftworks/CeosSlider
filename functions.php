<?php
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

/**
 * Register the plugin pages, so they can be accessed through the admin panel.
 */

function setupPages() {
	/**
	 * Menu page
	 */
	
	add_utility_page(
		'CEOS Slider',
		__('Sliders', 'ceos_slider'),
		'manage_options',
		\CEOS\Slider\PLUGIN_PREFIX . 'menu_page',
		'CEOS\Slider\Pages\menuPage',
		\CEOS\Slider\PLUGIN_PATH_URL . 'icon.png');

	/**
	 * Create new slider
	 */
	
	add_submenu_page(
		\CEOS\Slider\PLUGIN_PREFIX . 'menu_page',
		__('Create new slider') . ' » CEOS Slider',
		__('Create new slider'),
		'manage_options',
		\CEOS\Slider\PLUGIN_PREFIX . 'create',
		'CEOS\Slider\Pages\createSlider');
}

/**
 * Creates the tables needed for proper functioning of the plugin.
 * @return bool TRUE in case of success, FALSE otherwise.
 */

function createTables() {
	global $wpdb;

	$sql =
		"CREATE TABLE IF NOT EXISTS `".\CEOS\Slider\PLUGIN_PREFIX."sliders` (
			`slid_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`slid_title` TINYTEXT NOT NULL,
			`slid_opt_interval` FLOAT UNSIGNED NOT NULL DEFAULT '8000',
			`slid_opt_transition_duration` FLOAT UNSIGNED NOT NULL DEFAULT '300',
			`slid_opt_transition_name` VARCHAR(64) NOT NULL DEFAULT 'fade crossover',
			`slid_opt_init_delay` FLOAT UNSIGNED NOT NULL DEFAULT '0',
			`slid_opt_mouseover_pause` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			`slid_opt_show_next_prev` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			`slid_opt_show_nav` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			`slid_opt_show_interval` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			`slid_opt_restart` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			`slid_opt_width` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '1920',
			`slid_opt_height` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '640',
			`slid_opt_aspect_ratio` FLOAT UNSIGNED NOT NULL DEFAULT '0.3232',
			`slid_opt_initial_item` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
			`slid_opt_adaptative` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			PRIMARY KEY (`slid_id`)
		) ENGINE=InnoDB;";
    
  if(!$wpdb->query($sql)) {
    deactivate_plugin(\CEOS\Slider\PLUGIN_FILE);
    wp_die(__('An error occurred while trying to create the necessary resources for CEOS Slider.'));
  }
    
  $sql =
    "CREATE TABLE IF NOT EXISTS `".\CEOS\Slider\PLUGIN_PREFIX."items` (
			`item_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`item_slider_id` BIGINT(20) UNSIGNED NOT NULL,
			`item_title` TINYTEXT NOT NULL,
			`item_desc` TEXT NOT NULL,
			`item_url` TINYTEXT NOT NULL,
			`item_imgsrc` TEXT NOT NULL,
			`item_transition` CHAR(64) NOT NULL,
			`item_transition_duration` FLOAT UNSIGNED NOT NULL,
			`item_interval` FLOAT UNSIGNED NOT NULL,
			PRIMARY KEY (`item_id`),
			INDEX `parent_slider` (`item_slider_id`)
		) ENGINE=InnoDB;";

  if(!$wpdb->query($sql)) {
    deactivate_plugin(\CEOS\Slider\PLUGIN_FILE);
    wp_die(__('An error occurred while trying to create the necessary resources for CEOS Slider.'));
  }
}

function enqueueTransitions() {
	foreach(glob(\CEOS\Slider\PLUGIN_PATH . "transitions/*.js") as $filepath) {
		$filepath = str_replace(\CEOS\Slider\PLUGIN_PATH, \CEOS\Slider\PLUGIN_PATH_URL, $filepath);
		$filename = str_replace(\CEOS\Slider\PLUGIN_PATH_URL, '', $filepath);

		wp_enqueue_script(
			'ceos-transition-'.$filename, 
			$filepath, 
			array('ceos-slider-transition-js'),
			null,
			true);
	}
}

function getTransitions() {
	$transitions = array();

	foreach(glob(\CEOS\Slider\PLUGIN_PATH . "transitions/*.js") as $filepath) {
		$content = file_get_contents($filepath);

		preg_match(
			'/CEOS\.Slider\.Transitions\.(?<name>\w+)/iu', 
			$content,
			$name);

		preg_match(
			'/title\s*?\:\s*?[\'|\"](?<title>[^\'\"]+)[\'|\"]/iu',
			$content,
			$title);

		$name = @$name['name'];
		$title = @$title['title'];

		array_push($transitions, array(
			'name' => $name,
			'title' => $title));
	}

	return $transitions;
}