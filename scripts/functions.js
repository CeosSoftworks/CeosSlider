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

CEOS.Slider.getTransition = function (transitionName) {
	for(var name in CEOS.Slider.Transitions) {
		if(name == transitionName) {
			return CEOS.Slider.Transitions[name];
		}
	}
}

CEOS.Slider.getVendorCSSPrefix = function() {
	return '-' + CEOS.Slider.getVendorStylePrefix().toLowerCase() + '-';
}
CEOS.Slider.getVendorStylePrefix = function () {
	var engines = {
		webkit	: 'webkit',
		trident	: 'MS',
		opera	: 'O',
		gecko	: 'Moz'
	};

	var userAgent 		= navigator.userAgent;
	var currentEngine 	= null;

	for(var engine in engines) {
		if((new RegExp(engine, 'i')).test(userAgent)) {
			currentEngine = engines[engine];
			break;
		}
	};

	return currentEngine || '';
}

CEOS.Slider.getVendorAnimationPrefix = function() {
	var prefix = CEOS.Slider.getVendorStylePrefix();

	switch(prefix) {
		case 'MS':
		case 'Moz': return 'animation'; break;
		case 'webkit': return 'webkitAnimation'; break;
		case 'O': return 'oAnimation'; break;
		default: return prefix;
	}
}

CEOS.Slider.getVendorTransitionPrefix = function() {
	var prefix = CEOS.Slider.getVendorStylePrefix();

	switch(prefix) {
		case 'Moz': return ''; break;
		case 'MS':
		case 'O' : return prefix.toLowerCase(); break;
		default: return prefix;
	}
}