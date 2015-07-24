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

require('header.php');

header('Content-type: Text/plain');

/**
 * This scripts is ment for pushing a slider into the database. So it only
 * accept POST requests.
 */

if($_SERVER['REQUEST_METHOD'] != 'POST') {
	$error = 'status=invalid_method';
	require('footer.php');
	exit;
}

/**
 * Nonce verification
 */

$nonceAction = \CEOS\Slider\PLUGIN_PREFIX . '_remove_' . $_POST['id'] . get_current_user_id();

if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $nonceAction)) {
	$error = 'status=invalid_verification';
	require('footer.php');
	exit;
}

if(!isset($_POST['id']) 
	|| !\CEOS\Slider\Slider::removeFromDatabase($_POST['id'])) {
	$error = 'status=remove_error';
	require('footer.php');
	exit;
}

require('footer.php');