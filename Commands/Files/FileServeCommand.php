<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Commands\Files;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Container\Container;
use Congraph\Contracts\Filesystem\FileRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * FileServeCommand class
 * 
 * Command for serving file contents
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileServeCommand extends RepositoryCommand
{

	/**
     * Application container
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

	/**
	 * Create new FileServeCommand
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
	 * @return void
	 */
	public function handle()
	{
		// find file and serve its content
		$fileData = $this->repository->fetch($this->id);
		$file = Storage::get($this->id);

		if($this->params && $this->params['version'])
		{
			$key = md5(serialize(['url' => $this->id, 'version' => $this->params['version']]));
			$lifetime = Config::get('cb.files.cache_lifetime');
			$handler = $this->container->make(Config::get('cb.files.image_versions.' . $this->params['version']));
			
			$file = Cache::remember($key, $lifetime, function() use($handler, $file) {
				return $handler->handle($file);
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
