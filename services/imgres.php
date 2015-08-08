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

/**
 * Resizes a given image following the parameters provided.
 */

$src 					= isset($_GET['src']) ? $_GET['src'] : null;
$width 				= isset($_GET['w']) ? $_GET['w'] : null;
$height				= isset($_GET['h']) ? $_GET['h'] : null;

if(empty($src)) {
	header('HTTP/1.1 400 Bad Request');
	print 'No image to process';
	exit;
}

$imgsize = getimagesize($src);

if(!$imgsize) {
	header('HTTP/1.1 400 Bad Request');
	print 'The image given doesn\'t exist';
	exit;
}

/**
 * Identify the image format provided and calls the appropriate function to
 * create a handle
 */

switch(exif_imagetype($src)) {
	case IMAGETYPE_GIF:
		$imgoriginal = imagecreatefromgif($src);
		break;
	case IMAGETYPE_JPEG:
		$imgoriginal = imagecreatefromjpeg($src);
		break;
	case IMAGETYPE_PNG:
		$imgoriginal = imagecreatefrompng($src);
		break;
	case IMAGETYPE_BMP:
		$imgoriginal = imagecreatefromwbmp($src);
		break;

	default:
		header('HTTP/1.1 400 Bad Request');
		print 'Unknown image type';
		exit;
}

/**
 * If no width or height were given by the user, the empty property will be
 * calculated by the script taking into account the original image aspect ratio.
 */

if(empty($width) && empty($height)) {
	$width = $imgsize[0];
	$height = $imgsize[1];
} elseif(empty($width) xor empty($height)) {
	$ratio = $imgsize[1] / $imgsize[0];

	if(empty($width)) {
		$width = $height / $ratio;
	} elseif(empty($height)) {
		$height = $width * $ratio;
	}
}

/**
 * We calculate the top and left corner values needed to center the original
 * image dimension into the new image's.
 */

$imgresource = imagecreatetruecolor($width, $height);

$resresult = imagecopyresampled(
		$imgresource, 
		$imgoriginal, 
		0, 0,
		0, 0,
		$width, $height, 
		$imgsize[0], $imgsize[1]);

if($resresult) {
	header('Content-type: image/jpeg');
	imagejpeg($imgresource, null, 100);
} else {
	header('HTTP/1.1 500 Internal Server Error');
	print 'An error occured while processing the given image';
	exit;
}