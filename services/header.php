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

/**
 * Let's make sure that, in case something goes wrong either with the script
 * or with the expected data by it, the server doesn't send any headers before
 * we set everything the way we want.
 */

ob_start();

/**
 * Look for the script necessary to load Wordpress' API and requires it.
 */

$dir = dirname(__FILE__);
do {
	if(file_exists("{$dir}/wp-config.php")) {
		include("{$dir}/wp-config.php");
		break;
	}
} while ($dir = realpath("{$dir}/.."));

/**
 * If the ABSPATH isn't defined, the prior procedure didn't do it's job as
 * expected, so we return an error to the client and terminate the script
 * execution.
 */

if(!defined('ABSPATH')) {
	header('HTTP/1.1 500 Internal Server Error');
	header('service-details: Could not find ABSPATH definition');
	exit;
}


/**
 * We might need those
 */

require_once(\CEOS\Slider\PLUGIN_PATH . 'classes/slider.php');
require_once(\CEOS\Slider\PLUGIN_PATH . 'classes/slider_item.php');

/**
 * Acquire the page name from which the client sent its request and stores
 * it in the query variable that will be used to redirect the client when
 * the script finishes running
 */

preg_match('/page=(?<page>\w+)[&$]?/', $_SERVER['HTTP_REFERER'], $matches);

$query = 'admin.php?page=' . $matches['page'];