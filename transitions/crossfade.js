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

CEOS.Slider.Transitions.Crossfade = new CEOS.Slider.Transition({
	title: "Crossfade",
	
	init: function(el, dur, sender) {
		new CEOS.Slider.Animation(
			'ceos-trans-fade-in',
			['opacity: 0'],
			['opacity: 1']
		);
		
		new CEOS.Slider.Animation(
			'ceos-trans-fade-hide',
			['opacity: 1'],
			['opacity: 0']
		);
	},

	transitionIn: function(el, dur, sender) {
		el.style.zIndex = 1;
		el.style.display = 'block';

		var animation = CEOS.Slider.getVendorAnimationPrefix();

		el.style[animation + 'Name'] 						= 'ceos-trans-fade-in'
		el.style[animation + 'Duration'] 				= dur / 1000 + 's';
		el.style[animation + 'TimingFunction'] 	= 'ease';
		el.style[animation + 'Delay'] 					= '0s';
		el.style[animation + 'IterationCount'] 	= 1;
		el.style[animation + 'Direction'] 			= 'both';
		el.style[animation + 'PlayState'] 			= 'running';
	},
	
	transitionOut: function(el, dur, sender) {
		el.style.zIndex = 0;
		
		var animation = CEOS.Slider.getVendorAnimationPrefix();

		el.style[animation + 'Name'] 						= 'ceos-trans-fade-hide'
		el.style[animation + 'Duration'] 				= '0s';
		el.style[animation + 'TimingFunction'] 	= 'ease';
		el.style[animation + 'Delay'] 					= dur / 1000 + 's';
		el.style[animation + 'IterationCount'] 	= 1;
		el.style[animation + 'Direction'] 			= 'both';
		el.style[animation + 'PlayState'] 			= 'running';
	}
});