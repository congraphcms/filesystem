<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Handlers\Images;

use Congraph\Contracts\Filesystem\ImageVersionHandlerContract;
use Intervention\Image\Facades\Image;

/**
 * AdminThumbHandler class
 * 
 * Creates admin thumb image version
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class AdminImageHandler implements ImageVersionHandlerContract
{

	/**
	 * Create admin image version
	 * 
	 * @param $imageData
	 * 
	 * @return string - image content
	 */
	public function handle($imageData)
	{
		$image = Image::make($imageData);
		$image->fit(300, 200);
		return (string) $image->encode();
	}
}