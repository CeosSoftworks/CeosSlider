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

if(!function_exists('wp_handle_upload')) {
	require_once( ABSPATH . 'wp-admin/includes/file.php');
}

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

$nonceAction = \CEOS\Slider\PLUGIN_PREFIX . '_editslider_' . get_current_user_id();

if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $nonceAction)) {
	$error = 'status=invalid_verification';
	require('footer.php');
	exit;
}

/**
 * In case no title was provided, we'll do the user a favor and give his
 * slider a name. Otherwise, we'll only trim the name to make sure it doesn't
 * accidental space characters at the end or in the beggining of the title.
 */

if(!isset($_POST['title']) || empty(trim($_POST['title']))) {
	$_POST['title'] = 'Slider ' . Date('d/M/Y H:i:s');
} else {
	$_POST['title'] = trim($_POST['title']);
}

/**
 * If no default interval is provided, we'll give it a few seconds. We'll also
 * convert the values from seconds to milliseconds.
 */

if(!isset($_POST['opt-interval']) || !is_numeric($_POST['opt-interval'])) {
	$_POST['opt-interval'] = 7000;
} else {
	$_POST['opt-interval'] *= 1000;
}

/**
 * If no default duration for the transition was given by the user, a default
 * value will be assumed.
 */

if(!isset($_POST['opt-transition-dur']) || !is_numeric($_POST['opt-transition-dur'])) {
	$_POST['opt-transition-dur'] = 1000;
} else {
	$_POST['opt-transition-dur'] *= 1000;
}

/**
 * Converts the given initialization delay to milliseconds. If none was
 * provided by the user, the defaul value of 0 is attributed to the global
 * post variable for later use.
 */

if(!isset($_POST['opt-init-delay']) || !is_numeric($_POST['opt-init-delay'])) {
	$_POST['opt-init-delay'] = 0;
} else {
	$_POST['opt-init-delay'] *= 1000;
}

/**
 * Explode the string sent by the client containing the IDs of elements the user
 * wants removed from the slider
 */

if(!empty($_POST['remove'])) {
	$_POST['remove'] = explode(',', $_POST['remove']);
}

/**
 * Here we create a slider object and attribute the proper values to it.
 */

$slider = new \CEOS\Slider\Slider();

/**
 * It's not obligatory to provide and ID, but if one is provided,
 * it must be a number.
 */

if(isset($_POST['id']) && !empty($_POST['id']) && !is_numeric($_POST['id'])) {
	$error = 'status=invalid_id';
	require('footer.php');
	exit;
} elseif(isset($_POST['id']) && !empty($_POST['id'])) {
	$slider->setID($_POST['id']);
}

$slider->setTitle($_POST['title']);
$slider->setDefaultInterval($_POST['opt-interval']);
$slider->setDefaultTransitionName(@$_POST['opt-transition']);
$slider->setDefaultTransitionDuration(@$_POST['opt-transition-dur']);
$slider->setInitializationDelay(@$_POST['opt-init-delay']);
$slider->setPauseOnMouseOver(@$_POST['opt-pause-mouseover']);
$slider->setShowNextPrev(@$_POST['opt-show-next-prev']);
$slider->setShowNavigation(@$_POST['opt-show-nav']);
$slider->setShowInterval(@$_POST['opt-show-interval']);
$slider->setRestart(@$_POST['opt-restart']);
$slider->setWidth(@$_POST['opt-max-width']);
$slider->setHeight(@$_POST['opt-max-height']);
$slider->setAspectRatio(@$_POST['opt-aspect-ratio']);
$slider->setInitialItem(@$_POST['opt-initial-item']);
$slider->setAdaptative(@$_POST['opt-adaptative']);

/**
 * Here we remove from the DB the items that the client removed from the slider.
 */

if(is_array($_POST['remove'])) {
	foreach($_POST['remove'] as $id) {
		\CEOS\Slider\SliderItem::removeFromDatabase($id);
	}
}

/**
 * Here we insert the slider items provided by the user, if any, into the slider
 * object. Below we also check if the upload of the slider image has succeed.
 */

if(is_array($_POST['items'])) {
	/**
	 * Reorder the items sent by the client so no array boundary violations
	 * occur. 
	 */
	
	$newOrder = array();
	
	foreach($_POST['items'] as $item) {
		array_push($newOrder, $item);
	}

	$_POST['items'] = $newOrder;

	/**
	 * Process the data sent by the client for storing
	 */

	for($pos = 0; $pos < sizeof($_POST['items']); $pos++) {
		$item = $_POST['items'][$pos];

		if(is_array($_POST['remove']) && in_array($item['id'], $_POST['remove'])) {
			continue;
		}

		/**
		 * Gather the information provided by the server about the uploaded
		 * image regarding the actual item being processed.
		 */
		
		foreach($_FILES['items'] as $key => $arr) {	
			$item['img'][$key] = $arr[$pos]['img'];
		}

		if(!$slider->getID() || $item['img']['size'] > 0) {
			/**
			 * Below we rename the sent image prepending the slider name into
			 * the image's name. We do this for organization sake.
			 */
			
			$item['img']['name'] =
				sanitize_file_name($item['title'].'-'.$item['img']['name']);

			/**
			 * Move the uploaded image to the default uploads folder used by
			 * Wordpress.
			 */
			
			$uploadHandle =
				wp_handle_upload($item['img'], array('test_form' => false));

			/**
			 * Check if the process of moving the image file to the uploads folder
			 * has succeed. If it did, the uploaded image url is stored for later
			 * retrieval. Otherwise, headers indicating a server error are set
			 * and returned to the client. After that, the script is terminated. 
			 */

			if($uploadHandle && !isset($uploadHandle['error'])) {
				$item['img']['src'] = $uploadHandle['url'];
			} else{
				$error = 'status=upload_error&msg='.$uploadHandle['error'];
				require('footer.php');
				exit;
			}
		}

		/**
		 * If the default transition duration of the current slider item was not
		 * provided by the user, we give it a default value.
		 */
		
		if(!isset($item['transition_duration'])
			|| !is_numeric($item['transition_duration'])) {
			
			$item['transition_duration'] = 1000;
		} else {
			$item['transition_duration'] *= 1000;
		}

		/**
		 * Here we set a SliderItem object with the information provided and
		 * insert it into the Slider object previously created.
		 */

		$sldItem = new \CEOS\Slider\SliderItem();

		if(isset($item['id']) && is_numeric($item['id'])) {
			$sldItem->loadFromDatabase($item['id']);
		}

		$sldItem->setPosition($pos);
		$sldItem->setTitle(@$item['title']);
		$sldItem->setDescription(@$item['desc']);
		$sldItem->setURL(@$item['url']);
		$sldItem->setTransitionName(@$item['transition']);

		if(isset($item['transition-dur']) && is_numeric($item['transition-dur'])) {
			$sldItem->setTransitionDuration($item['transition-dur'] * 1000);
		}
		
		if(isset($item['interval']) && is_numeric($item['interval'])) {
			$sldItem->setInterval($item['interval'] * 1000);	
		}

		if(isset($item['img']['src']) && !empty($item['img']['src'])) {
			$sldItem->setImageSource($item['img']['src']);
		}

		if(isset($item['slider-id']) && is_numeric($item['slider-id'])) {
			$sldItem->setSliderID($item['slider-id']);
		}

		$slider->addItem($sldItem);
	}

	/**
	 * Here we try to push the slider into the database. If, for some reason,
	 * this procedure fails, a server error is returned to the client. Otherwise
	 * we let the script finish its course, which will return a success
	 * response to the client.
	 */
	
	if(!$slider->pushIntoDatabase()) {
		$error = 'status=push_error';
		require('footer.php');
		exit;
	}

	$query .= '&edit=' . $slider->getID();
}

require('footer.php');