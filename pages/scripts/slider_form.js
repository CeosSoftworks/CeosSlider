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

CEOS.Slider.displaySlideImage = function (src, elDisplay) {
	if(src.length == 0) return;

	var fReader = new FileReader();
		fReader.readAsDataURL(src);

	fReader.onload = function (e) {
		elDisplay.style.backgroundImage = 'url(' + e.target.result + ')';
	}
}

CEOS.Slider.createSliderForm = function () {
	

	var self = this;
	var rnd = (Math.random() * (Math.pow(2,53) - 1));

	var index = document.getElementById('slider-content').childNodes.length;
	var el = document.createElement('div');
		el.innerHTML =
			'<li class="slider-item" id="slider-item-' + rnd + '">' +
				'<input type="hidden" name="items[' + index + '][id]" class="slider-item-id">' +
				'<div class="slider-item-img" id="slider-item-img-' + rnd + '">' +
					'<div class="slider-chg-img-wrap"  id="slider-chg-img-wrap-' + rnd + '">' +
						'<input id="slider-file-input-' + rnd +'" name="items[' + index + '][img]" type="file" style="display: none" class="slider-file-input">' +
						'<label for="slider-file-input-' + rnd +'" class="button button-secondary">' + translations.chg_img + '</label>' +
					'</div>' +
				'</div>' +
				'<div class="slider-item-content" id="slider-item-content-' + rnd + '">' +
					'<div class="slider-item-row title" id="slider-row-title-' + rnd + '">' +
						'<label for="slider-title-' + rnd + '">' + translations.title + '</label>' +
						'<input id="slider-title-' + rnd + '" name="items[' + index + '][title]" type="text" class="slider-item-title" value="">' +
					'</div>' +
					'<div class="slider-item-row desc" id="slider-row-desc-' + rnd + '">' +
						'<label for="slider-desc-' + rnd + '">' + translations.desc + '</label>' +
						'<input id="slider-desc-' + rnd + '" name="items[' + index + '][desc]" type="text" class="slider-item-desc" value="">' +
					'</div>' +
					'<div class="slider-item-row url" id="slider-row-url-' + rnd + '">' +
						'<label for="slider-url-' + rnd + '">' + translations.url + '</label>' +
						'<input id="slider-url-' + rnd + '" name="items[' + index + '][url]" type="text" class="slider-item-url" value="">' +
					'</div>' +
					'<div class="slider-item-row interval" id="slider-row-interval-' + rnd + '">' +
						'<label for="slider-interval-' + rnd + '">' + translations.interval + '</label>' +
						'<input id="slider-interval-' + rnd + '" name="items[' + index + '][interval]" type="number" class="slider-item-interval" min="0" step="any" value="">' +
						'<label for="slider-interval-' + rnd + '">&nbsp;' + translations.seconds + '</label>' +
					'</div>' +
					'<div class="slider-item-row transition" id="slider-row-transition-' + rnd + '">' +
						'<label for="slider-transition-' + rnd + '">' + translations.transition + '</label>' +
						'<select id="slider-transition-' + rnd + '" name="items[' + index + '][transition]" class="slider-item-transition">' +
							'<option value="none">' + translations.transition_none + '</option>' +
							(function transOptionObjs(){
								var s = '';
								for(var trans in CEOS.Slider.Transitions){
									s += '<option value="' + trans + '">' + CEOS.Slider.Transitions[trans].title + '</option>';
								}
								return s;
							})() +
						'</select>' +
					'</div>' +
					'<div class="slider-item-row transition-dur" id="slider-row-transition-dur-' + rnd + '">' +
						'<label for="slider-transition-dur-' + rnd + '">' + translations.transition_duration + '</label>' +
						'<input id="slider-transition-dur-' + rnd + '" name="items[' + index + '][transition-dur]" type="number" class="slider-item-transition-dur" min="0" step="0.1" value="">' +
						'<label for="slider-transition-dur-' + rnd + '">&nbsp;' + translations.seconds + '</label>' +
					'</div>' +
				'</div>' +
				'<input type="button" class="slider-item-remove" value="x">'
			'</li>';

	el = el.childNodes[0];

	var fileInput = el.getElementsByClassName('slider-file-input')[0];
	var display = el.getElementsByClassName('slider-item-img')[0];

	fileInput.addEventListener('change', function () {
		CEOS.Slider.displaySlideImage(fileInput.files[0], display);
	});

	var removeBtn = el.getElementsByClassName('slider-item-remove')[0];

	removeBtn.addEventListener('click', function () {
		if(confirm(translations.remove_confirmation)) {
			var elID = el.getElementsByClassName('slider-item-id')[0];

			if(elID && elID.value > 0) {
				CEOS.Slider.markForRemoval(elID.value);
			}

			el.remove();
		}
	});

	return el;
}

CEOS.Slider.markForRemoval = function (id) {
	var elRemove = document.getElementById('remove');

	if(elRemove.value.length > 0) {
		elRemove.value += ',' + id;
	} else {
		elRemove.value = id;
	}
}

CEOS.Slider.changePosition = function (el, pos) {
	Array.prototype.slice.call(el.childNodes).forEach(function(node, i, arr) {
		if(node.name) {
			node.name =
				node.name.replace(/items\[\d+\]/i, 'items[' + pos + ']');
		}

		if(node.childNodes.length > 0) {
			CEOS.Slider.changePosition(node, pos);
		}
	});
}

CEOS.Slider.dragStart = function (el) {
	el.style.transform = 'scale(.8)';
}

CEOS.Slider.dragEnd = function (el) {
	el.style.transform = 'scale(1)';
}

CEOS.Slider.monitorMouseMovement = function (e) {
	CEOS.Slider.mousePosition = {
		x: e.clientX,
		y: e.clientY
	}
}

CEOS.Slider.addSliderForm = function () {
	var sliderForm = CEOS.Slider.createSliderForm();

	document.getElementById('slider-content').appendChild(sliderForm);
	
	CEOS.Slider.calculateProportions();

	return sliderForm;
}

CEOS.Slider.fillItem = function (args) {
	var el = CEOS.Slider.addSliderForm();

	var elID = el.getElementsByClassName('slider-item-id')[0];
	var elImg = el.getElementsByClassName('slider-item-img')[0];
	var elImgInput = el.getElementsByClassName('slider-file-input')[0];
	var elTitle = el.getElementsByClassName('slider-item-title')[0];
	var elDesc = el.getElementsByClassName('slider-item-desc')[0];
	var elURL = el.getElementsByClassName('slider-item-url')[0];
	var elInterval = el.getElementsByClassName('slider-item-interval')[0];
	var elTransition = el.getElementsByClassName('slider-item-transition')[0];
	var elTransitionDur = el.getElementsByClassName('slider-item-transition-dur')[0];

	elID.value = args.id || '';
	elImg.style.backgroundImage = 'url(' + args.image + ')';
	elImgInput.setAttribute('nocheck', 'true');
	elTitle.value = args.title;
	elDesc.value = args.desc;
	elURL.value = args.url;
	elInterval.value = args.interval / 1000;
	elTransitionDur.value = args.transitionDuration / 1000;

	for(var i = 0; i < elTransition.options.length; i++) {
		if(args.transition == elTransition.options[i].value) {
			elTransition.options.selectedIndex = i;
			break;
		}
	}
}