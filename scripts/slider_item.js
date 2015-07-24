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

CEOS.Slider.SliderItem = function ceosSliderItem(args) {
  this.element        = args.element;
  this.transition     = CEOS.Slider.getTransition(args.transition);
  this.transDuration  = args.transition_duration;
  this.interval       = args.interval;
  this.imgSrc         = args.imgsrc;
  this.parent         = args.parent;
}

CEOS.Slider.SliderItem.prototype.init = function () {
  if(this.parent.optAdaptative) {
    this.adaptToAreaSize();
  }

  var transition = this.transition
    ? this.transition
    : this.parent.defTransition;

  var transDuration = this.transDuration
    ? this.transDuration
    : this.parent.defTransDuration;

  if(transition) {
    transition.init(this.element, transDuration, this);
  }
}

CEOS.Slider.SliderItem.prototype.transitionIn = function () {
  var transition = this.transition
    ? this.transition 
    : this.parent.defTransition;

  var transDuration = this.transDuration
    ? this.transDuration
    : this.parent.defTransDuration;

  if(transition == 'none' || !transition) {
    this.element.style.display = 'block';
  } else {
    transition.transitionIn(this.element, transDuration, this);
  }
}

CEOS.Slider.SliderItem.prototype.transitionOut = function () {
  var transition = this.parent.items[this.parent.to].transition
    ? this.parent.items[this.parent.to].transition 
    : this.parent.defTransition;

  var transDuration = this.parent.items[this.parent.to].transDuration
    ? this.parent.items[this.parent.to].transDuration
    : this.parent.defTransDuration;

  if(transition == 'none' || !transition) {
    this.element.style.display = 'none';
  } else {
    transition.transitionOut(this.element, transDuration, this);

    var el = this.element;
    function animEnd() {
      el.removeEventListener('webkitAnimationEnd', animEnd);
      el.removeEventListener('MSAnimationEnd', animEnd);
      el.removeEventListener('oanimationend', animEnd);
      el.removeEventListener('animationend', animEnd);

      el.removeAttribute('style');

      console.log('Animation Ended');
    }

    this.element.addEventListener('webkitAnimationEnd', animEnd);
    this.element.addEventListener('MSAnimationEnd', animEnd);
    this.element.addEventListener('oanimationend', animEnd);
    this.element.addEventListener('animationend', animEnd);
  }
}

CEOS.Slider.SliderItem.prototype.bindNavigationItem = function() {
  if(this.parent.optShowNavigation) {
    var self = this;
    var elList = this.parent.element.getElementsByClassName('nav-list')[0];
      
    elList = Array.prototype.slice.call(
        elList.getElementsByClassName('nav-item')
      );

    elList.forEach(function(item, i, arr) {
      if(item instanceof HTMLLIElement) {
        var elAnchor = item.getElementsByTagName('a')[0];

        if(elAnchor.getAttribute('data-goto') == i) {
          elAnchor.addEventListener('click', function() {
            self.parent.goTo(i);
            self.parent.pause();
          });
        }
      }
    });
  }
}

CEOS.Slider.SliderItem.prototype.adaptToAreaSize = function() {
  var elParent = this.parent.element;
  var imgBG = this.element.getElementsByClassName('image-bg')[0];

  imgBG.style.backgroundImage = 'url(' + this.imgSrc +
      '&w=' + elParent.clientWidth + ')';
}