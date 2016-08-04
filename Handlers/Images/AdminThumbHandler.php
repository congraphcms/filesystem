<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Handlers\Images;

use Cookbook\Contracts\Filesystem\ImageVersionHandlerContract;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * AdminThumbHandler class
 * 
 * Creates admin thumb image version
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class AdminThumbHandler implements ImageVersionHandlerContract
{

	/**
	 * Create admin thumb version
	 * 
	 * @param $imageData
	 * 
	 * @return string - image content
	 */
	public function handle($imageData)
	{
		$image = Image::make($imageData);
		$image->fit(200, 150);
		return (string) $image->encode();
	}
}