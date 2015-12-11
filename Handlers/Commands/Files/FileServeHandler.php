<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Handlers\Commands\Files;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Cookbook\Filesystem\Commands\Files\FileServeCommand;
use Illuminate\Contracts\Container\Container;

/**
 * FileServeHandler class
 * 
 * Handling command for serving file
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileServeHandler
{

	/**
     * Application container
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;


	/**
	 * Create new FileServeHandler
	 * 
	 * @param \Illuminate\Contracts\Container\Container $container
	 * 
	 * @return void
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Handle FileServeCommand
	 * 
	 * @param \Cookbook\Filesystem\Commands\Files\FileServeCommand $command
	 * 
	 * @return void
	 */
	public function handle(FileServeCommand $command)
	{
		// find file and serve its content
		$file = Storage::get($command->url);

		if($command->version)
		{
			$handler = $this->container->make(Config::get('cb.files.image_versions.' . $command->version));
			$file = $handler->handle($file);
		}

		return $file;
		// check if file is an image
		// if it is, check if you should serve original or version
		// check if version exist
		// check if version is in cache
		// create version
		// put in cache
		// serve image
	}
}