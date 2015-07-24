<?php

/**
 * This class is intended to provide a method for printing a slider into any
 * part of a webpage with the possibility of printing the same slider many
 * times without the need of cloning it or creating a similar slider.
 *
 * I tried to make this class as maleable as possible (by that I mean I left
 * the member that holds the slider object public, so it can be modified on
 * the fly), but it still might not be enough for some cases, so feel free to
 * mess around with it, improve it or ditch it all together.
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

class SliderPrint {
	static $sliderIDs = array();
	static $itemIDs = array();

	public $sliderObject;

	private $sliderID;
	private $javascripts = array();
	private $javascriptItems = array();

	/**
	 * Generates an unique slider ID based on the ID provided.
	 *
	 * This function is necessary to avoid duplicates during the printing
	 * process, which would lead to the javascript procedures not capturing
	 * the right elements.
	 * 
	 * @param  integer $id ID of the slider which will be used as a base for
	 * the creation of a new unique one.
	 * 
	 * @return string Unique ID
	 */
	
	private function generateSliderID($id) {
		if(in_array($id, self::$sliderIDs)) {
			$increment = 2;

			do {
				$newID = $id .'-'. $increment++;
			} while(in_array($newID, self::$sliderIDs, true));
		
			$id = $newID;
		}

		array_push(self::$sliderIDs, $id);

		return $id;
	}

	/**
	 * Generates an unique item ID based on the ID provided.
	 *
	 * This function is necessary to avoid duplicates during the printing
	 * process, which would lead to the javascript procedures not capturing
	 * the right elements.
	 * 
	 * @param  integer $id ID of the item which will be used as a base for
	 * the creation of a new unique one.
	 * 
	 * @return string Unique ID
	 */

	private function generateItemID($id) {
		if(in_array($id, self::$itemIDs)) {
			$increment = 2;

			do {
				$newID = $id .'-'. $increment++;
			} while(in_array($newID, self::$itemIDs, true));
		
			$id = $newID;
		}

		array_push(self::$itemIDs, $id);

		return $id;
	}

	/**
	 * Prints the given slider item.
	 *
	 * @param SliderItem $item Item which will be printed.
	 */

	private function printItem(SliderItem $item) {
		$ID = $this->generateItemID($item->getID());

		$imgsrc = \CEOS\Slider\PLUGIN_PATH_URL . 'services/imgres.php'
					. '?src=' . $item->getImageSource();

		if(!$this->sliderObject->getAdaptative()) {
			$imgsrc .=
				'&w=' . $this->sliderObject->getWidth()
				. '&h=' . $this->sliderObject->getHeight();
		}

		?>

		<li id="slider-item-<?= $ID ?>" class="ceos-slider item slider-item-<?= $ID ?>">
			<?php if(!empty($item->getURL())) : ?>
				<a href="<?= $item->getURL() ?>">
			<?php endif?>
			
			<article class="ceos-slider inner">
				<div class="ceos-slider image-wrap">
					<div class="ceos-slider image-bg" style="background-image: url(<?= $imgsrc ?>)"></div>
				</div>
				<header class="ceos-slider item-header">
					<?php if(!empty($item->getTitle())) : ?>
						<h1 class="ceos-slider item-title"><?= $item->getTitle() ?></h1>
					<?php endif ?>

					<?php if(!empty($item->getDescription())) : ?>
						<h3 class="ceos-slider item-desc"><?= $item->getDescription() ?></h3>
					<?php endif ?>
				</header>
			</article>

			<?php if(!empty($item->getURL())) : ?>
				</a>
			<?php endif?>
		</li>

		<?php

		array_push($this->javascriptItems, 
			'new CEOS.Slider.SliderItem ({'
			. 'element:'              . 'document.getElementById("slider-item-'.$ID.'"), '
			. 'transition:'           . (empty($item->getTransitionName()) ? 'null' : '"'.$item->getTransitionName().'"') . ', '
			. 'transition_duration:'  . (empty($item->getTransitionDuration()) ? 'null' : $item->getTransitionDuration()) . ', '
			. 'interval:'             . (empty($item->getInterval()) ? 'null' : $item->getInterval()) . ', '
			. 'imgsrc:'								. '"' . $imgsrc . '"'
			. '})'
		);
	}

	/**
	 * Prints all items from the slider indicated during the instantiation of
	 * this object.
	 */

	private function printAllItems() {
		$javascript = '';

		for($i = 0; $i < $this->sliderObject->countItems(); $i++) {
			$item = $this->sliderObject->getItem($i);

			$this->printItem($item);
		}
	}

	/**
	 * Prints the navigation controls of the slider indicated during the
	 * instantiation of this object. This function will only print the
	 * navigation controls if the slider has such option set by the user.
	 */

	private function printNavigation() {
		if($this->sliderObject->getShowNavigation()
			&& $this->sliderObject->countItems() > 1) :
		?>

		<div class="ceos-slider nav">
			<ul class="ceos-slider nav-list">
			<?php for($i = 0; $i < $this->sliderObject->countItems(); $i++) : ?>
				<li class="ceos-slider nav-item">
					<a href="javascript:void(0)" data-goto="<?= $i ?>"></a>
				</li>
			<?php endfor ?>
			</ul>
		</div>

		<?php
		endif;
	}

	/**
	 * Prints the "next" and "previous" controls of the slider indicated during
	 * the instantiation of this object. This function will only print the
	 * next and previous controls if the slider has such option set by the user.
	 */

	private function printNextPrev() {
		if($this->sliderObject->getShowNextPrev()
			&& $this->sliderObject->countItems() > 1) :
		?>

		<div class="ceos-slider next-prev">
			<a class="ceos-slider next-prev-link previous" href="javascript:void(0)">
				<span class="ceos-slider next-prev-icon"></span>
				<span class="ceos-slider next-prev-label"><?= __('Previous') ?></span>
			</a>
			<a class="ceos-slider next-prev-link next" href="javascript:void(0)">
				<span class="ceos-slider next-prev-icon"></span>
				<span class="ceos-slider next-prev-label"><?= __('Next') ?></span>
			</a>
		</div>

		<?php
		endif;
	}

	/**
	 * Prints the interval of the current cicle in the slider indicated during
	 * the instantiation of this object. This function will only perform such
	 * action if the slider has this option set by the user.
	 */

	private function printInterval() {
		if($this->sliderObject->getShowInterval()
			&& $this->sliderObject->countItems() > 1) :
		?>

		<div class="ceos-slider interval">
			<div class="ceos-slider container">
				<div class="ceos-slider progress-bar">
					<span class="ceos-slider progress">
						<span class="ceos-slider time">0:00</span>
					</span>
				</div>
			</div>
		</div>

		<?php
		endif;
	}

	/**
	 * Loads the slider which has the given ID. If the second parameter is
	 * set to TRUE, it will also print the slider in the position where the
	 * object was instantiated.
	 */

	function __construct($id = null, $print = true) {
		wp_enqueue_script('ceos-slider-functions-js',	\CEOS\Slider\PLUGIN_PATH_URL . 'scripts/functions.js');
		wp_enqueue_script('ceos-slider-animation-js',	\CEOS\Slider\PLUGIN_PATH_URL . 'scripts/animation.js');
		wp_enqueue_script('ceos-slider-slider-js', 		\CEOS\Slider\PLUGIN_PATH_URL . 'scripts/slider.js');
		wp_enqueue_script('ceos-slider-slider-item-js',	\CEOS\Slider\PLUGIN_PATH_URL . 'scripts/slider_item.js');
		wp_enqueue_script('ceos-slider-timer-js', 		\CEOS\Slider\PLUGIN_PATH_URL . 'scripts/timer.js');
		wp_enqueue_script('ceos-slider-transition-js', 	\CEOS\Slider\PLUGIN_PATH_URL . 'scripts/transition.js');

		\CEOS\Slider\enqueueTransitions();

		wp_enqueue_style('ceos-slider-style', \CEOS\Slider\PLUGIN_PATH_URL . 'style.css');

		if(isset($id)) {
			$this->sliderID = $id;

			$this->sliderObject = new Slider();
			$this->sliderObject->loadFromDatabase($id);

			if($print) {
				$this->printSlider();
			}
		}
	}

	/**
	 * Prints the javascript statements needed for proper functioning of the
	 * slider element.
	 */

	function printJavascript() {
		if(sizeof($this->javascripts)): ?>
		
		<script type="text/javascript">
			<?php foreach($this->javascripts as $js) : ?>
				<?php print $js ?>
			<?php endforeach ?>
		</script>

		<?php endif;
	}

	/**
	 * Prints the slider indicated during the instantiation of the current
	 * object.
	 */

	function printSlider() {
		if($this->sliderObject->countItems() <= 1) {
			$this->sliderObject->setShowInterval(0);
			$this->sliderObject->setShowNextPrev(0);
			$this->sliderObject->setShowNavigation(0);
			$this->sliderObject->setRestart(0);
		}
		$ratio = $this->sliderObject->getAspectRatio();

		$sliderID = $this->generateSliderID($this->sliderObject->getID());

		?>

		<div id="ceos-slider-<?= $sliderID ?>" class="ceos-slider outer" <?php if(!empty($ratio)) print 'style="padding-top: '.($ratio * 100).'%"' ?>>
			<div class="ceos-slider inner">
				<div class="ceos-slider item-list-wrap">
					<ul class="ceos-slider item-list">
						<?php $this->javascriptItems = array() ?>
						<?php $this->printAllItems() ?>
					</ul>
				</div>

				<?php
					$this->printInterval();
					$this->printNavigation();
					$this->printNextPrev();
				?>
			</div>
		</div>

		<?php

		$slider = $this->sliderObject;

		if(!empty($this->javascriptItems) && sizeof($this->javascriptItems)) {
			$this->javascriptItems = '[' . implode(', ', $this->javascriptItems) . ']';
		}

		array_push($this->javascripts, 'new CEOS.Slider.Slider({'
			. 'element:'          . 'document.getElementById("ceos-slider-'.$sliderID.'"), '
			. 'transition:'       . '"' . $slider->getDefaultTransitionName() . '", '
			. 'transition_duration:' . $slider->getDefaultTransitionDuration() . ', '
			. 'interval:'         . $slider->getDefaultInterval() . ', '
			. 'init_delay:'       . $slider->getInitializationDelay() . ', '
			. 'pause_mouseover:'  . $slider->getPauseOnMouseOver() . ', '
			. 'show_timer:'       . $slider->getShowInterval() . ', '
			. 'show_next_prev:'	  . $slider->getShowNextPrev() . ', '
			. 'show_navigation:'  . $slider->getShowNavigation() . ', '
			. 'restart:'          . $slider->getRestart() . ', '
			. 'ratio:'            . $slider->getAspectRatio() . ', '
			. 'initial_item:'     . $slider->getInitialItem() . ', '
			. 'items:'            . $this->javascriptItems
			. '}); '
		);

		add_action('wp_footer', array($this, 'printJavascript'), 99,0);
	}
}