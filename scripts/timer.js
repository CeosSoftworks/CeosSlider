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

CEOS.Slider.Timer = function ceosTimer(action, interval, paused, repetitions) {
	this.timer 				= null;
	this.paused				= false;
	this.finished			= false;
	this.runCount			= 0;
	this.action				= action;
	
	this.intInit 			= interval;
	this.intEllapsed		= 0;

	this.timeInit 			= Date.now();
	this.timeCicleInit		= this.timeInit;
	this.timeCicleLastInit	= this.timeInit;
	this.timePause 			= null;
	
	this.repeats 			= repetitions || 0;
	this.repeatsRemaining 	= repetitions || 0;
	this.infinite			= (repetitions > 0) ? false : true;

	if(!paused) {
		this.run.call(this);
	}
}

/**
 * Pause the timer
 */

CEOS.Slider.Timer.prototype.pause = function () {
	this.timePause = Date.now();
	this.paused = true;

	this.intEllapsed += Date.now() - this.timeCicleLastInit;

	clearInterval(this.timer);
}

/**
 * Run the timer 
 */

CEOS.Slider.Timer.prototype.run = function (force) {
	if(this.finished && !force){
		return;
	} else if (this.finished && force) {
		this.restart();
	}

	this.timeCicleLastInit = Date.now();
	this.paused = false;

	this.timer = setInterval(
			this.tick.bind(this),
			this.getRemainingCicleTime()
		);

	this.timePause = null;
}

/**
 * Reset the timer data for the initial values
 */

CEOS.Slider.Timer.prototype.cleanUp = function (keepTimer) {
	this.finished = false;
	this.intEllapsed = 0;
	this.timeCicleInit = Date.now();
	this.timeCicleLastInit = this.timeCicleInit;
	this.timePause = null;

	if(!keepTimer) {
		clearInterval(this.timer);
	}
}

/**
 * Restart timer
 */

CEOS.Slider.Timer.prototype.restart = function () {
	this.cleanUp();
	this.repeatsRemaining = this.repeats;
}

/**
 * Restart the current timer cicle
 */

CEOS.Slider.Timer.prototype.restartCicle = function () {
	this.repeatsRemaining++;

	this.cleanUp();
	this.timer = setInterval(
			this.tick.bind(this), 
			this.getRemainingCicleTime()
		);
}

/**
 * Executed every timer cicle
 */

CEOS.Slider.Timer.prototype.tick = function () {
	this.action.call(this);
	
	this.cleanUp(true);

	if(!this.infinite) {
		if(this.repeats > 0) {
			this.repeatsRemaining--;
		}

		if(this.repeatsRemaining == 0) {
			this.cleanUp();
			this.finished = true;
			this.runCount++;
			this.pause();
		}
	}
}

/**
 * Returns the ellapsed milliseconds since the last cicle start.
 */

CEOS.Slider.Timer.prototype.getEllapsedCicleTime = function () {
	return this.paused
		? (this.intEllapsed)
		: (this.intEllapsed + (Date.now() - this.timeCicleLastInit));
}

/**
 * Returns the remaining milliseconds until the next cicle
 */

CEOS.Slider.Timer.prototype.getRemainingCicleTime = function () {
	return (this.intInit - this.getEllapsedCicleTime());
}

/**
 * Returns the ellapsed milliseconds since the timer started running
 */

CEOS.Slider.Timer.prototype.getTotalEllapsedTime = function () {
	return Date.now() - this.timeInit;
}

/**
 * Returns an object with the respective hours, minutes and seconds of the
 * given milliseconds
 */

CEOS.Slider.Timer.prototype.convertToMMSS = function(milliseconds) {
	return {
		minutes: 		Math.floor((milliseconds / 1000) / 60),
		seconds: 		Math.floor((milliseconds / 1000) % 60),
		milliseconds: 	milliseconds
	}
}