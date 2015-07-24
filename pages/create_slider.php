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

namespace CEOS\Slider\Pages;

function createSlider() {
  include('header.php'); 

  \CEOS\Slider\enqueueTransitions();

  wp_enqueue_script('ceos-slider-transition-js', \CEOS\Slider\PLUGIN_PATH_URL . 'scripts/transition.js');

  ?>

  <?php
    $editID = null;

    if(isset($_GET['edit']) && is_numeric($_GET['edit'])) {
      $editID = $_GET['edit'];
    }

    $editObj = new \CEOS\Slider\Slider();
    $editObj->loadFromDatabase($editID);
  ?>

  <div class="ceos-slider admin-page create-slider-page">
    <form id="create-slider-form" method="POST" action="<?= \CEOS\Slider\PLUGIN_PATH_URL ?>services/push_slider.php" enctype="multipart/form-data">
      <section id="slider-main">
        <header id="main-header">
          <h2 id="page-title">
            <label for="title"><?= __('Create new slider') ?></label>
          </h2>
          <div id="titlediv">
            <input id="title" type="text" maxlength="127" name="title" value="<?= $editObj->getTitle() ?>">
          </div>

          <hr />

          <div id="page-header-msg-wrap">
            <?php include('header_msg.php') ?>
          </div>
        </header>

        <div id="slider-content-wrap">
          <h4 id="slider-content-title"><?= __('Items') ?></h4>
          <div id="slider-content-controls">
            <input id="slide-add" type="button" class="button button-primary" value="+">
          </div>
          <ul id="slider-content"></ul>
        </div>
      </section>

      <section id="slider-controls">
        <h4><?= __('Settings') ?></h4>
        
        <input type="hidden" name="id" value="<?= $editObj->getID() ?>">
        <input type="hidden" id="remove" name="remove" value="">
        <input type="hidden" id="nonce" name="nonce" value="<?= wp_create_nonce(\CEOS\Slider\PLUGIN_PREFIX.'_editslider_'.get_current_user_id()) ?>">
        
        <div id="slider-settings">
          <div class="set-row">
            <label class="main" for="opt-transition"><?= __('Transition') ?></label>
            <select id="opt-transition" name="opt-transition">
              <option value="none" <?= $editObj->getDefaultTransitionName() == 'none' ? 'selected' : '' ?>><?= __('None') ?></option>
              <?php if(is_array($transitions)) : ?>
                <?php foreach($transitions as $t) : ?>
                  <option value="<?= $t['name'] ?>" <?= $editObj->getDefaultTransitionName() == $t['name'] ? 'selected' : '' ?>><?= $t['title'] ?></option>
                <?php endforeach ?>
              <?php endif ?>
            </select>
          </div>
          <div class="set-row">
            <label class="main" for="opt-transition-dur"><?= __('Transition duration') ?></label>
            <input type="number" min="0" step="0.1" id="opt-transition-dur" name="opt-transition-dur" value="<?= ($editObj->getDefaultTransitionDuration() ? $editObj->getDefaultTransitionDuration() / 1000 : 1) ?>">
            <label for="opt-transition-dur"><?= __('sec') ?></label>
          </div>
          <div class="set-row">
            <label class="main" for="opt-interval"><?= __('Slider interval') ?></label>
            <input type="number" min="1" step="1" id="opt-interval" name="opt-interval" value="<?= ($editObj->getDefaultInterval() ? $editObj->getDefaultInterval() / 1000 : 7) ?>">
            <label for="opt-interval"><?= __('sec') ?></label>
          </div>
          <div class="set-row">
            <label class="main" for="opt-init-delay"><?= __('Initialization delay') ?></label>
            <input type="number" min="0" step="1" id="opt-init-delay" name="opt-init-delay" value="<?= ($editObj->getInitializationDelay() ? $editObj->getInitializationDelay() / 1000 : 0) ?>">
            <label for="opt-init-delay"><?= __('sec') ?></label>
          </div>
          <div class="set-row">
            <label class="main" for="opt-initial-item"><?= __('Initial item') ?></label>
            <input type="number" min="0" step="1" id="opt-initial-item" name="opt-initial-item" value="<?= ($editObj->getInitialItem() ? $editObj->getInitialItem() : 0) ?>">
          </div>
          <hr />
          <div class="set-row">
            <label class="main" for="opt-pause-mouseover"><?= __('Pause on mouse over') ?></label>
            <input type="checkbox" id="opt-pause-mouseover" name="opt-pause-mouseover" value="1" <?= $editObj->getPauseOnMouseOver() ? 'checked' : '' ?>>
          </div>
          <div class="set-row">
            <label class="main" for="opt-show-next-prev"><?= __('Show Next/Previous controls') ?></label>
            <input type="checkbox" id="opt-show-next-prev" name="opt-show-next-prev" value="1" <?= $editObj->getShowNextPrev() ? 'checked' : '' ?>>
          </div>
          <div class="set-row">
            <label class="main" for="opt-show-nav"><?= __('Show navigation controls') ?></label>
            <input type="checkbox" id="opt-show-nav" name="opt-show-nav" value="1" <?= $editObj->getShowNavigation() ? 'checked' : '' ?>>
          </div>
          <div class="set-row">
            <label class="main" for="opt-show-interval"><?= __('Show interval') ?></label>
            <input type="checkbox" id="opt-show-interval" name="opt-show-interval" value="1" <?= $editObj->getShowInterval() ? 'checked' : '' ?>>
          </div>
          <div class="set-row">
            <label class="main" for="opt-restart"><?= __('Restart when finished') ?></label>
            <input type="checkbox" id="opt-restart" name="opt-restart" value="1" <?= $editObj->getRestart() ? 'checked' : '' ?>>
          </div>

          <hr />
          
          <div class="set-row">
            <h4><?= __('Proportions') ?></h4>
            <div class="set-row">
              <label class="main" for="opt-adaptative"><?= __('Adapt to actual area size') ?></label>
              <input type="checkbox" id="opt-adaptative" name="opt-adaptative" value="1" <?= $editObj->getAdaptative() ? 'checked' : '' ?>>
            </div>
            <div class="set-row inner">
              <span id="prop-width-wrap">
                <label class="main" for="opt-max-width"><?= __('Width') ?></label>
                <input type="number" id="opt-max-width" name="opt-max-width" min="0" step="1" value="<?= ($editObj->getWidth() ? $editObj->getWidth() : 1920) ?>">
                <label>px</label>
              </span>
              <span id="prop-height-wrap">
                <label class="main" for="opt-max-height"><?= __('Height') ?></label>
                <input type="number" id="opt-max-height" name="opt-max-height" min="0" step="1" value="<?= ($editObj->getHeight() ? $editObj->getHeight() : 640) ?>">
                <label>px</label>
              </span>
            </div>
            <div class="set-row inner">
              <label class="main" for="opt-aspect-ratio"><?= __('Aspect ratio') ?></label>
              <input type="number" id="opt-aspect-ratio" name="opt-aspect-ratio" min="0" step="0.0001" value="<?= ($editObj->getAspectRatio() ? $editObj->getAspectRatio() : 0.33) ?>">
            </div>
          </div>
        </div>
        <hr />
        <div id="slider-publish-controls">
          <input id="send-form" type="submit" class="button button-primary" value="<?= __('Publish') ?>">
        </div>
      </section>
    </form>
  </div>

  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function (){
    <?php
      for($i = 0; $i < $editObj->countItems(); $i++) {
        $item = $editObj->getItem($i);

        print 'CEOS.Slider.fillItem({
          id: "'          . $item->getID() . '",
          image: "'       . $item->getImageSource() . '",
          title: "'       . $item->getTitle() . '",
          desc: "'        . $item->getDescription() . '",
          url: "'         . $item->getURL() . '",
          interval: "'    . $item->getInterval() . '",
          transition: "'  . $item->getTransitionName() . '",
          transitionDuration: "' . $item->getTransitionDuration() . '"}); ';
      }
    ?>
    })
  </script>

  <?php include('footer.php');
}