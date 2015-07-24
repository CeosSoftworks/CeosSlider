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

CEOS.Slider.Animation = function ceosSliderAnimation(name, from, to) {
  if(document.getElementById(name)) return;

  function arrayToStr(arr) {
    var s = '';
    if(arr) {
      arr.forEach(function(el, i, arr){ s += el + '; ' });
    }
    return s;
  }

  var keyframesPrefix = CEOS.Slider.getVendorStylePrefix();

  if(false && keyframesPrefix == 'MS') {
    var name = '@keyframes ' + name;
    var str =
        'from { ' + arrayToStr(from) + '} ' +
        'to { ' + arrayToStr(to) + '}';

    var node = {
      name: name,
      css: str
    }
  } else {
    keyframesPrefix = (keyframesPrefix == 'WebKit' ? '-webkit-' : '');

    var str =
      '<style type="text/css" >' +
      '@' + keyframesPrefix + 'keyframes ' + name + ' {' +
        'from { ' + arrayToStr(from) + '} ' +
        'to { ' + arrayToStr(to) + '}' +
      '}' +
      '</style>';

    var node = document.createElement('div');
        node.innerHTML = str;
        node = node.childNodes[0];
        
    document.getElementsByTagName('head')[0].appendChild(node);
  }
}