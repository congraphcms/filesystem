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


use Cookbook\Contracts\Filesystem\FileRepositoryContract;
use Cookbook\Core\Bus\RepositoryCommandHandler;
use Cookbook\Core\Bus\RepositoryCommand;
use Illuminate\Support\Facades\Storage;

/**
 * FileFetchHandler class
 * 
 * Handling command for fetching file
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileFetchHandler extends RepositoryCommandHandler
{

	/**
	 * Create new FileFetchHandler
	 * 
	 * @param Cookbook\Contracts\Filesystem\FileRepositoryContract $repository
	 * 
	 * @return void
	 */
	public function __construct(FileRepositoryContract $repository)
	{
		parent::__construct($repository);
	}

	/**
	 * Handle RepositoryCommand
	 * 
	 * @param Cookbook\Core\Bus\RepositoryCommand $command
	 * 
	 * @return void
	 */
	public function handle(RepositoryCommand $command)
	{
		return $this->repository->fetch(
			$command->id, 
			(!empty($command->params['include']))?$command->params['include']:[]
		);
	}
}