<?php

/**
 * This class is intended to represent an item of a slider. There isn't much
 * to talk about it really.
 *
 * I know I should've documented at least a few things in the source code, but
 * I was tired then and I'm tired now to do so, so read the code and have fun.
 * It isn't a complex class or anything, you might get the grasp of it pretty
 * easily. I have faith in your talent.
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

class SliderItem {
	private $id;
	private $sliderID;
	private $position;
	private $imgSrc;
	private $title;
	private $desc;
	private $url;
	private $interval;
	private $transition;
	private $transitionDur;

	function setID($id) {
		if(!is_numeric($id)) {
			throw new \Exception("ID must be numeric", 1);
		}

		$this->id = $id;
	}

	function setSliderID($id) {
		$this->sliderID = $id;
	}

	function setPosition($position) {
		$this->position = $position;
	}
	
	function setImageSource($src) {
		$this->imgSrc = trim($src);
	}

	function setTitle($title) {
		$this->title = trim($title);
	}
	
	function setDescription($desc) {
		$this->desc = trim($desc);
	}

	function setURL($url) {
		$this->url = trim($url);
	}

	function setInterval($interval) {
		$this->interval = $interval;
	}

	function setTransitionName($transitionName) {
		$this->transition = trim($transitionName);
	}

	function setTransitionDuration($transitionInt) {
		$this->transitionDur = $transitionInt;
	}

	function getID() {
		return $this->id;
	}

	function getSliderID() {
		return $this->sliderID;
	}

	function getPosition() {
		return $this->position;
	}

	function getImageSource() {
		return $this->imgSrc;
	}

	function getTitle() {
		return $this->title;
	}

	function getDescription() {
		return $this->desc;
	}

	function getURL() {
		return $this->url;
	}

	function getInterval() {
		return $this->interval;
	}

	function getTransitionName() {
		return $this->transition;
	}

	function getTransitionDuration() {
		return $this->transitionDur;
	}

	function insertIntoDatabase() {
		global $wpdb;

		$sql =
			"INSERT INTO ".\CEOS\Slider\PLUGIN_PREFIX."items
				SET
					item_id = %d,
					item_slider_id = %d,
					item_title = %s,
					item_desc = %s,
					item_url = %s,
					item_imgsrc = %s,
					item_transition = %s,
					item_transition_duration = %d,
					item_interval = %d";

		$result = $wpdb->query($wpdb->prepare($sql,
			$this->getID(),
			$this->getSliderID(),
			$this->getTitle(),
			$this->getDescription(),
			$this->getURL(),
			$this->getImageSource(),
			$this->getTransitionName(),
			$this->getTransitionDuration(),
			$this->getInterval()
		));

		print $wpdb->last_error
;		if($result || empty($wpdb->last_error)) {
			$this->setID($wpdb->insert_id);

			return true;
		} else {
			return false;
		}
	}

	function updateOnDatabase() {
		global $wpdb;

		$sql =
			"UPDATE ".\CEOS\Slider\PLUGIN_PREFIX."items
				SET
					item_slider_id = %d,
					item_title = %s,
					item_desc = %s,
					item_url = %s,
					item_imgsrc = %s,
					item_transition = %s,
					item_transition_duration = %d,
					item_interval = %d
				WHERE item_id = %d
				LIMIT 1";

		$result = $wpdb->query($wpdb->prepare($sql,
			$this->getSliderID(),
			$this->getTitle(),
			$this->getDescription(),
			$this->getURL(),
			$this->getImageSource(),
			$this->getTransitionName(),
			$this->getTransitionDuration(),
			$this->getInterval(),
			$this->getID()
			));

		print $wpdb->last_error;
		if($result || empty($wpdb->last_error)) {
			return true;
		} else {
			return false;
		}
	}

	function pushIntoDatabase() {
		if(empty($this->getID())) {
			$this->insertIntoDatabase();
		} else {
			$this->updateOnDatabase();
		}
	}

	function loadFromDatabase($id) {
		global $wpdb;

		$sql =
			"SELECT * 
				FROM ".\CEOS\Slider\PLUGIN_PREFIX."items
				WHERE item_id = %d
				LIMIT 1";

		$result = $wpdb->get_row($wpdb->prepare($sql, $id));

		if($result) {
			$this->setID($result->item_id);
			$this->setSliderID($result->item_slider_id);
			$this->setTitle($result->item_title);
			$this->setDescription($result->item_desc);
			$this->setURL($result->item_url);
			$this->setImageSource($result->item_imgsrc);
			$this->setTransitionName($result->item_transition);
			$this->setTransitionDuration($result->item_transition_duration);
			$this->setInterval($result->item_interval);

			return true;
		} else {
			return false;
		}
	}

	static function removeFromDatabase($id) {
		global $wpdb;

		$sql =
			"DELETE FROM ".\CEOS\Slider\PLUGIN_PREFIX."items WHERE item_id = %d LIMIT 1";

		return $wpdb->query($wpdb->prepare($sql, $id));
	}
}