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

function menuPage() {
	include('header.php');

	$sliders = \CEOS\Slider\Slider::getSliders();

	?>
	
	<div class="ceos-slider admin-page menu-page">

		<div class="ceos-table">
			<div class="ceos-table-header">
				<span class="ceos-table-hd"><?= __('Slider') ?></span>
				<span class="ceos-table-hd"><?= __('ID') ?></span>
				<span class="ceos-table-hd"><?= __('Items') ?></span>
			</div>
			<?php if($sliders && sizeof($sliders) > 0) : ?>
			
				<?php foreach($sliders as $slider) : ?>
					<div class="ceos-table-row">
						<span class="ceos-table-td title">
							<b><a href="<?= admin_url('admin.php?page='.\CEOS\Slider\PLUGIN_PREFIX.'create&edit=' . $slider->slid_id) ?>"><?= $slider->slid_title ?></a></b>
							<div class="actions">
								<a href="<?= admin_url('admin.php?page='.\CEOS\Slider\PLUGIN_PREFIX.'create&edit=' . $slider->slid_id) ?>" class="edit"><?= __('Edit') ?></a>
								<span class="sep">|</span>
								<a href="<?= admin_url('admin.php?page='.\CEOS\Slider\PLUGIN_PREFIX.'menu_page&remove=' . $slider->slid_id) ?>" class="trash"><?= __('Remove') ?></a>
							</div>
						</span>
						<span class="ceos-table-td id"><?= $slider->slid_id ?></span>
						<span class="ceos-table-td count"><?= $slider->count ?></span>
					</d