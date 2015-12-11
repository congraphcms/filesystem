<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


return array(
	/**
	 *	path to directory where uploaded files will be stored
	 */
	'uploads_path' => 'files',

	
	'image_versions' => [
		'admin_thumb' => 'Cookbook\Filesystem\Handlers\Images\AdminThumbHandler'
	],

	'cache_lifetime' => 1440

);