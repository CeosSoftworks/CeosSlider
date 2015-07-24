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

var CEOS = CEOS || {};
  CEOS.Slider = CEOS.Slider || {};
  
CEOS.Slider.Sliders = CEOS.Slider.Sliders = [];

CEOS.Slider.Slider = function ceosSlider(args) {
  CEOS.Slider.Sliders.push(this);

  this.element            = args.element;

  this.curItemIndex       = -1;
  this.curItem            = null;
  this.from               = null;
  this.to                 = null;
  
  this.items              = args.items || [];

  this.defTransition      = CEOS.Slider.getTransition(args.transition);
  this.defTransDuration   = args.transition_duration || .4;
  this.defInterval        = args.interval || 7000;
  this.initDelay          = args.init_delay || 0;
  
  this.optPauseMouseOver  = args.pause_mouseover || false;
  this.optShowTimer       = args.show_timer || false;
  this.optShowNextPrev    = args.show_next_prev || false;
  this.optShowNavigation  = args.show_navigation || false;
  this.optRestartOnFinish = args.restart || true;
  this.optInitialItem     = args.initial_item || 0;
  this.optAdaptative      = args.adaptative || true;
  
  this.ratio              = args.ratio || 0.3333;
  this.timer;

  this.attributeParenthood();
  this.init();
}

CEOS.Slider.Slider.prototype.init = function () {
  this.adjustVerticalHeight();
  
  this.items.forEach(function(el, i, arr) {
    el.element.style.display = 'none';
    el.init();
  });

  this.bindPauseOnMouseOver();
  this.bindShowNextPrev();

  window.addEventListener('resize', this.adjustVerticalHeight.bind(this));

  setTimeout(
    this.goTo.bind(this, this.optInitialItem), 
    this.initDelay
  );
}

CEOS.Slider.Slider.prototype.setUpTimer = function () {
  if(this.timer) {
    this.timer.pause();
    this.timer = null;
  }

  var interval = this.curItem.interval
    ? this.curItem.interval 
    : this.defInterval;

  var transition = this.curItem.transition
    ? this.curItem.transition
    : this.defTransition;

  if(transition == 'none' || !transition) {
    var transDuration = 0;
  } else {
    var transDuration = this.curItem.transDuration
      ? this.curItem.transDuration 
      : this.defTransDuration;
  }

  this.timer = new CEOS.Slider.Timer(
    this.showNextItem.bind(this),
    interval + transDuration
  );

  this.bindTimerFeedback();
}

CEOS.Slider.Slider.prototype.run = function() {
  if(!this.timer){
    this.init();
  }

  this.timer.run();
}

CEOS.Slider.Slider.prototype.pause = function() {
  this.timer.pause();
}

CEOS.Slider.Slider.prototype.restart = function() {
  this.goTo(this.optInitialItem);
}

CEOS.Slider.Slider.prototype.attributeParenthood = function () {
  var self = this;

  this.items.forEach(function(el, i, arr){
    el.parent = self;
    el.bindNavigationItem();
  });
}

CEOS.Slider.Slider.prototype.goTo = function(index) {
  this.from   = this.curItemIndex;
  this.to   = index;

  if(this.curItem) {
    this.curItem.transitionOut(index);
  }

  this.curItemIndex = index;
  this.curItem = this.items[index];

  if(this.timer) {
    this.timer.pause();
    this.timer = null;
  }
  
  this.curItem.transitionIn();
  
  this.setUpTimer();
}

CEOS.Slider.Slider.prototype.showNextItem = function() {
  if(this.optRestartOnFinish
    && (this.curItemIndex + 1 >= this.items.length)) {
    this.goTo(0);
  } else {
    this.goTo(this.curItemIndex + 1);
  }
}

CEOS.Slider.Slider.prototype.showPreviousItem = function() {
  if(this.optRestartOnFinish && (this.curItemIndex - 1 < 0)) {
    this.goTo(this.items.length - 1);
  } else {
    this.goTo(this.curItemIndex - 1);
  }
}

CEOS.Slider.Slider.prototype.getNextItem = function() {
  if(this.curItemIndex + 1 >= this.items.length) {
    return this.items[0];
  } else {
    return this.items[this.curItemIndex + 1];
  }
}

CEOS.Slider.Slider.prototype.getPreviousItem = function() {
  if(this.curItemIndex - 1 < 0) {
    return this.items[this.items.length - 1];
  } else {
    return this.items[this.curItemIndex - 1];
  }
}

CEOS.Slider.Slider.prototype.bindTimerFeedback = function() {
  if(this.optShowTimer) {
    var self = this;

    var elMain = this.element.getElementsByClassName('interval')[0];
    var elProgress = elMain.getElementsByClassName('progress')[0];
    var elTime = elMain.getElementsByClassName('time')[0];

    var interval = setInterval(function() {
      if(self.timer) {
        var percentage =
          self.timer.getEllapsedCicleTime() / self.timer.intInit * 100;

        elProgress.style.width = percentage + '%';

        var conversion = 
          CEOS.Slider.Timer.prototype.convertToMMSS(
            self.timer.getRemainingCicleTime()
          );

        elTime.innerHTML = 
          ((conversion.minutes < 10) ? '0' : '') + 
          conversion.minutes + ':' + 
          ((conversion.seconds < 10) ? '0' : '') + 
          conversion.seconds;
      } else{
        clearInterval(interval);
      }
    }, 10);
  }
}

CEOS.Slider.Slider.prototype.bindPauseOnMouseOver = function() {
  if(this.optPauseMouseOver) {
    var self = this;

    self.element.addEventListener('mouseenter', function() {
      self.timer.pause();
    });

    self.element.addEventListener('mouseleave', function() {
      self.timer.run();
    });
  }
}

CEOS.Slider.Slider.prototype.bindShowNextPrev = function() {
  if(this.optShowNextPrev) {
    var self = this;
    
    var prevCtrl = 
      this.element.getElementsByClassName('next-prev-link previous')[0];
    
    var nextCtrl = 
      this.element.getElementsByClassName('next-prev-link next')[0];

    prevCtrl.addEventListener('click', function() {
      self.showPreviousItem();
      self.pause();
    });

    nextCtrl.addEventListener('click', function() {
      self.showNextItem();
      self.pause();
    });
  }
}

CEOS.Slider.Slider.prototype.adjustVerticalHeight = function() {
  this.element.style.paddingTop = 0;
  this.element.style.height =
    this.element.getBoundingClientRect().width * this.ratio + 'px';
}