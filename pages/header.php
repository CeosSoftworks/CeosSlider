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

	$pluginInfo = get_plugin_data(\CEOS\Slider\PLUGIN_FILE);
	$transitions = \CEOS\Slider\getTransitions();

	wp_enqueue_style('ceos-slider-main', \CEOS\Slider\PLUGIN_PATH_URL . 'pages/styles/main.css');
	wp_enqueue_style('create_slider', \CEOS\Slider\PLUGIN_PATH_URL . 'pages/styles/create_slider.css');

	wp_enqueue_script('create_slider_page', \CEOS\Slider\PLUGIN_PATH_URL . 'pages/scripts/create_slider_page.js');
	wp_enqueue_script('slider_form', \CEOS\Slider\PLUGIN_PATH_URL . 'pages/scripts/slider_form.js');
	wp_enqueue_script('ajax_request', \CEOS\Slider\PLUGIN_PATH_URL . 'pages/scripts/ajax_request.js');

	wp_localize_script('slider_form', 'translations', array(
		'seconds'		=> __('seconds'),
		'chg_img'		=> __('Change image'),
		'title' 		=> __('Title'),
		'desc'			=> __('Description'),
		'url' 			=> __('URL'),
		'interval'		=> __('Interval'),
		'transition'	=> __('Transition'),
		'transition_duration' => __('Transition duration'),
		'remove_confirmation' => __('Are you sure you want to remove this item?'),
		'invalid_form_data' => __("One or more fields in the form were not filled properly. Make sure to:\n- Insert at least one slide into the slider.\n- Select an image for each slider item.\n- Provide a title to the slider."),
		'transition_none' => __('None (use the slider transition)')
		));
?>

<?php if($_GET['page'] != \CEOS\Slider\PLUGIN_PREFIX.'create') : ?>
	<header class="page-header">
		<span id="page-header-branding">
			<h2 class="title"><?= $pluginInfo['Name'] ?></h2>
			<p class="plug-ver"><?= __('Version:') . " {$pluginInfo['Version']}" ?></p>
		</span>
		<span id="page-header-add-wrap" class="wrap">
			<a class="add-new-h2" href="<?= admin_url('admin.php?page='.\CEOS\Slider\PLUGIN_PREFIX.'create') ?>"><?= __('Create new slider') ?></a>
		</span>
	</header>

	<hr />

	<div id="page-header-msg-wrap">
		<?php include('header_msg.php') ?>
	</div>
<?php endif ?>