<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Handlers\Commands\Files;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Congraph\Filesystem\Commands\Files\FileServeCommand;
use Illuminate\Contracts\Container\Container;
use Congraph\Contracts\Filesystem\FileRepositoryContract;


/**
 * FileServeHandler class
 * 
 * Handling command for serving file
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
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
	 * File Repository
	 *
	 * @var FileRepositoryContract
	 */
	protected $repository;


	/**
	 * Create new FileServeHandler
	 * 
	 * @param \Illuminate\Contracts\Container\Container $container
	 * @param FileRepositoryContract $repository
	 * 
	 * @return void
	 */
	public function __construct(
		Container $container,
		FileRepositoryContract $repository) 
	{
		$this->container = $container;
		$this->repository = $repository;
	}

	/**
	 * Handle FileServeCommand
	 * 
	 * @param \Congraph\Filesystem\Commands\Files\FileServeCommand $command
	 * 
	 * @return void
	 */
	public function handle(FileServeCommand $command)
	{
		// find file and serve its content
		$fileData = $this->repository->fetch($command->url);
		$file = Storage::get($command->url);

		if($command->version)
		{
			$key = md5(serialize(['url' => $command->url, 'version' => $command->version]));
			$lifetime = Config::get('cb.files.cache_lifetime');
			$handler = $this->container->make(Config::get('cb.files.image_versions.' . $command->version));
			
			$file = Cache::remember($key, $lifetime, function() use($handler, $file) {
				$file = $handler->handle($file);

			});
		}

		return [
            'content' => $file,
            'mime_type' => $fileData->mime_type || finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $file),
            'last_modified' => $fileData->updated_at
        ];
;
	}
}