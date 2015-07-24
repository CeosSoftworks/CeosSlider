<?php

/**
 * This class represents the star of the show -the slider. It comes with
 * default values for many members so an object can be easily instantiated and
 * used within an object of SliderPrinter on the fly.
 *
 * I didn't comment much of the source because I think it is pretty much self
 * explanatory. Yeah, I hope I'm not wrong about this assumption.
 *
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

class Slider {
	/**
	 * ID of the slider
	 * @var integer
	 */
	private $id;

	/**
	 * Title of the slider.
	 * @var string
	 */
	private $title;

	/**
	 * Not actually being used. Intended to be used for a shortcode feature that
	 * was never implemented (Hey, why don't you implement it for the good of
	 * the whole world?!)
	 * @var string
	 */
	private $filterName;

	/**
	 * Time (in milliseconds) that takes to change to the next item on the slider
	 * @var integer
	 */
	private $defInterval = 7000;

	/**
	 * Default items transition. Used by items that doesn't have one specified.
	 * @var string
	 */
	private $defTransitionName;

	/**
	 * Default period (in milliseconds) that takes for a transition to finish.
	 * @var integer
	 */
	private $defTransitionDur = 1000;

	/**
	 * Milliseconds that take for the slider to start transitioning.
	 * @var integer
	 */
	private $initDelay = 0;

	/**
	 * Specifies if the slider should pause while the mouse pointer is inside
	 * its boundaries.
	 * @var boolean
	 */
	private $optMouseOverPause = true;

	/**
	 * Specifies if a next and previous button should be made available to the
	 * user, so he can navigate between the slider items.
	 * @var boolean
	 */
	private $optShowNextPrev = true;

	/**
	 * Specifies if a group of navigation buttons should be displayed to the user,
	 * so he can jump from one slider item to another without order.
	 * @var boolean
	 */
	private $optShowNav = true;

	/**
	 * Specifies if the time until the next slider cicle should be displayed to
	 * the user.
	 * @var boolean
	 */
	private $optShowInterval = true;

	/**
	 * Specifies if the slider should start from the beginning after the last
	 * item's cicle.
	 * @var boolean
	 */
	private $optRestart = true;

	/**
	 * Width of which the images of this slider should have. The images are
	 * dynamically resized to follow this directive, if $adaptative is set to
	 * FALSE or 0. Otherwise, this value is ignored and the actual area of the
	 * slider in the page is used.
	 * @var integer
	 */
	private $optWidth;

	/**
	 * Height of which the images of this slider should have. The images are
	 * dynamically resized to follow this directive, if $adaptative is set to
	 * FALSE or 0. Otherwise, this value is ignored and the actual area of the
	 * slider in the page is used.
	 * @var integer
	 */
	private $optHeight;

	/**
	 * Ratio by which the images of this slider should obey. The images are
	 * dynamically resized to follow this directive, if $adaptative is set to
	 * FALSE or 0. Otherwise, this value is ignored and the actual ratio of the
	 * slider in the page is used.
	 * @var integer
	 */
	private $aspectRatio = 0.32;

	/**
	 * Determines from which item the slider should begin from.
	 * @var integer
	 */
	private $initialItem = 0;

	/**
	 * Determines if the images of this slider should be resized to the actual
	 * visible area of the slider or to the width and height set by the user
	 * previously. This directive is particularly useful for preventing large
	 * images of being loaded on devices that can't actually display them at
	 * their full size.
	 * @var integer
	 */
	private $adaptative = 1;

	/**
	 * The slider items.
	 * @var SliderItem[]
	 */
	private $items;

	private function pushItemsIntoDatabase() {
		$ids = array();

		foreach($this->items as $item) {
			if($item->pushIntoDatabase()) {
				array_push($ids, $item->getID());
			}
		}

		return implode(',', $ids);
	}

	private function loadItemsFromDatabase() {
		$retVal = false;

		global $wpdb;

		$sql =
			"SELECT item_id
				FROM ".\CEOS\Slider\PLUGIN_PREFIX."items
				WHERE item_slider_id = %d";

		$results = $wpdb->get_results($wpdb->prepare($sql, $this->getID()));

		if(is_array($results)) {
			foreach($results as $result) {
				$item = new \CEOS\Slider\SliderItem();
				$item->loadFromDatabase($result->item_id);

				$this->addItem($item);
			}
			$retVal = true;
		} else {
			$retVal = false;
		}
	}

	function __construct() {
		$this->items = array();
	}

	function loadFromDatabase($sliderID) {
		global $wpdb;

		$sql =
			"SELECT *
				FROM ".\CEOS\Slider\PLUGIN_PREFIX."sliders
				WHERE slid_id = %d
				LIMIT 1";

		$result = $wpdb->get_row($wpdb->prepare($sql, $sliderID));

		if($result) {
			$this->setID($sliderID);
			$this->setTitle($result->slid_title);
			$this->setDefaultInterval($result->slid_opt_interval);
			$this->setDefaultTransitionName($result->slid_opt_transition_name);
			$this->setDefaultTransitionDuration($result->slid_opt_transition_duration);
			$this->setInitializationDelay($result->slid_opt_init_delay);
			$this->setPauseOnMouseOver($result->slid_opt_mouseover_pause);
			$this->setShowNextPrev($result->slid_opt_show_next_prev);
			$this->setShowNavigation($result->slid_opt_show_nav);
			$this->setShowInterval($result->slid_opt_show_interval);
			$this->setRestart($result->slid_opt_restart);
			$this->setWidth($result->slid_opt_width);
			$this->setHeight($result->slid_opt_height);
			$this->setAspectRatio($result->slid_opt_aspect_ratio);
			$this->setInitialItem($result->slid_opt_initial_item);
			$this->setAdaptative($result->slid_opt_adaptative);

			if($this->loadItemsFromDatabase()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function insertIntoDatabase() {
		global $wpdb;

		$sql = 
			"INSERT INTO ".\CEOS\Slider\PLUGIN_PREFIX."sliders
				SET
					slid_id = %d,
					slid_title = %s,
					slid_opt_interval = %d,
					slid_opt_transition_name = %s,
					slid_opt_transition_duration = %d,
					slid_opt_init_delay = %d,
					slid_opt_mouseover_pause = %d,
					slid_opt_show_next_prev = %d,
					slid_opt_show_nav = %d,
					slid_opt_show_interval = %d,
					slid_opt_restart = %d,
					slid_opt_width = %d,
					slid_opt_height = %d,
					slid_opt_aspect_ratio = %f,
					slid_opt_initial_item = %d,
					slid_opt_adaptative = %d";

		$result = $wpdb->query($wpdb->prepare($sql,
			$this->getID(),
			$this->getTitle(),
			$this->getDefaultInterval(),
			$this->getDefaultTransitionName(),
			$this->getDefaultTransitionDuration(),
			$this->getInitializationDelay(),
			$this->getPauseOnMouseOver(),
			$this->getShowNextPrev(),
			$this->getShowNavigation(),
			$this->getShowInterval(),
			$this->getRestart(),
			$this->getWidth(),
			$this->getHeight(),
			$this->getAspectRatio(),
			$this->getInitialItem(),
			$this->getAdaptative()
			));

		if($result) {
			$this->setID($wpdb->insert_id);

			foreach($this->items as $item) {
				$item->setSliderID($this->getID());
			}

			$this->pushItemsIntoDatabase();

			return true;
		} else {
			return false;
		}
	}

	function updateInDatabase() {
		global $wpdb;

		$sql = 
			"UPDATE ".\CEOS\Slider\PLUGIN_PREFIX."sliders
				SET
					slid_title = %s,
					slid_opt_interval = %d,
					slid_opt_transition_name = %s,
					slid_opt_transition_duration = %d,
					slid_opt_init_delay = %d,
					slid_opt_mouseover_pause = %d,
					slid_opt_show_next_prev = %d,
					slid_opt_show_nav = %d,
					slid_opt_show_interval = %d,
					slid_opt_restart = %d,
					slid_opt_width = %d,
					slid_opt_height = %d,
					slid_opt_aspect_ratio = %f,
					slid_opt_initial_item = %d,
					slid_opt_adaptative = %d
				WHERE slid_id = %d
				LIMIT 1";

		$result = $wpdb->query($wpdb->prepare($sql,
			$this->getTitle(),
			$this->getDefaultInterval(),
			$this->getDefaultTransitionName(),
			$this->getDefaultTransitionDuration(),
			$this->getInitializationDelay(),
			$this->getPauseOnMouseOver(),
			$this->getShowNextPrev(),
			$this->getShowNavigation(),
			$this->getShowInterval(),
			$this->getRestart(),
			$this->getWidth(),
			$this->getHeight(),
			$this->getAspectRatio(),
			$this->getInitialItem(),
			$this->getAdaptative(),
			$this->getID()));

		if($result || empty($wpdb->last_error)) {
			foreach($this->items as $item) {
				$item->setSliderID($this->getID());
			}
			
			$this->pushItemsIntoDatabase();

			return true;
		} else {
			return false;
		}
	}

	function pushIntoDatabase() {
		$retVal = false;

		if(!is_null($this->getID())) {
			$retVal = $this->updateInDatabase();
		} else {
			$retVal = $this->insertIntoDatabase();
		}

		return $retVal;
	}

	function setID($id) {
		if(!is_numeric($id)) {
			throw new \Exception("ID must be numeric", 1);
		}

		$this->id = $id;
	}
	
	function setTitle($title) {
		if(empty(trim($title))) {
			throw new \Exception("A title must be provided", 1);
		}
		
		$this->title = $title;
	}

	function setDefaultInterval($interval) {
		if(!is_numeric($interval)) {
			throw new \Exception("Interval must be numeric", 1);
		}

		if($interval < 0) {
			throw new \Exception("Interval must be either 0 or bigger", 1);
		}

		$this->defInterval = $interval;
	}
	
	function setDefaultTransitionName($transitionName) {
		$this->defTransitionName = $transitionName;
	}

	function setDefaultTransitionDuration($transitionDur) {
		$this->defTransitionDur = $transitionDur;
	}

	function setInitializationDelay($delay) {
		$this->initDelay = $delay;
	}

	function setPauseOnMouseOver($val) {
		$this->optMouseOverPause = $val;
	}

	function setShowNextPrev($val) {
		$this->optShowNextPrev = $val;
	}

	function setShowNavigation($val) {
		$this->optShowNav = $val;
	}

	function setShowInterval($val) {
		$this->optShowInterval = $val;
	}

	function setRestart($val) {
		$this->optRestart = $val;
	}

	function setAspectRatio($ratio) {
		$this->aspectRatio = $ratio;
	}

	function setWidth($width) {
		$this->optWidth = $width;
	}

	function setHeight($height) {
		$this->optHeight = $height;
	}

	function setInitialItem($index) {
		$this->initialItem = $index;
	}

	function setAdaptative($value) {
		$this->adaptative = $value;
	}

	function getID() {
		return $this->id;
	}

	function getTitle() {
		return $this->title;
	}

	function getDefaultInterval() {
		return $this->defInterval;
	}

	function getDefaultTransitionName() {
		return $this->defTransitionName;
	}

	function getDefaultTransitionDuration() {
		return $this->defTransitionDur;
	}

	function getInitializationDelay() {
		return $this->initDelay;
	}

	function getPauseOnMouseOver() {
		return $this->optMouseOverPause;
	}

	function getShowNextPrev() {
		return $this->optShowNextPrev;
	}

	function getShowNavigation() {
		return $this->optShowNav;
	}

	function getShowInterval() {
		return $this->optShowInterval;
	}

	function getRestart() {
		return $this->optRestart;
	}

	function getAspectRatio() {
		return $this->aspectRatio;
	}

	function getWidth() {
		return $this->optWidth;
	}

	function getHeight() {
		return $this->optHeight;
	}

	function getInitialItem() {
		return $this->initialItem;
	}

	function getAdaptative() {
		return $this->adaptative;
	}

	function addItem(SliderItem $item) {
		$item->setSliderID($this->getID());
		$this->items[] = $item;
	}

	function getItem($index) {
		return $this->items[$index];
	}
	
	function removeItem($itemIndex) {
		$result = array();

		for($i = $this->items - 1; $i >= 0; $i--) {
			if($i != $itemIndex) {
				array_push($result, $this->items[$i]);
			}
		}

		$this->items = $result;
	}

	function countItems() {
		return sizeof($this->items);
	}

	static function removeFromDatabase($id) {
		global $wpdb;

		$itemsDelete = true;

		$slider = new Slider();
		$slider->loadFromDatabase($id);

		foreach($slider->items as $item) {
			$itemsDelete = $itemsDelete &&
				\CEOS\Slider\SliderItem::removeFromDatabase($item->getID());
		}

		$sql =
			"DELETE FROM ".\CEOS\Slider\PLUGIN_PREFIX."sliders WHERE slid_id = %d LIMIT 1";

		return $itemsDelete && $wpdb->query($wpdb->prepare($sql, $id));
	}

	static function getSliders() {
		global $wpdb;

		$sql =
			"SELECT ".\CEOS\Slider\PLUGIN_PREFIX."sliders.*,
					COUNT(item_slider_id) as count 
				FROM ".\CEOS\Slider\PLUGIN_PREFIX."sliders
				LEFT JOIN ".\CEOS\Slider\PLUGIN_PREFIX."items ON slid_id = item_slider_id
				GROUP BY slid_id";

		$results = $wpdb->get_results($sql);

		return $results;
	}

	static function getSliderTitle($id) {
		global $wpdb;

		$sql = 
			"SELECT slid_title 
				FROM ".\CEOS\Slider\PLUGIN_PREFIX."sliders 
				WHERE slid_id = %d 
				LIMIT 1";

		$result = $wpdb->get_row($wpdb->prepare($sql, $id));

		if(!$result) {
			return false;
		}

		return $result->slid_title;
	}
}