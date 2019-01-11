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


use Congraph\Contracts\Filesystem\FileRepositoryContract;
use Congraph\Core\Bus\RepositoryCommandHandler;
use Congraph\Core\Bus\RepositoryCommand;
use Illuminate\Support\Facades\Storage;

/**
 * FileFetchHandler class
 * 
 * Handling command for fetching file
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileFetchHandler extends RepositoryCommandHandler
{

	/**
	 * Create new FileFetchHandler
	 * 
	 * @param Congraph\Contracts\Filesystem\FileRepositoryContract $repository
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
	 * @param Congraph\Core\Bus\RepositoryCommand $command
	 * 
	 * @return void
	 */
	public function handle(RepositoryCommand $command)
	{
		$file = $this->repository->fetch(
			$command->id, 
			(!empty($command->params['include']))?$command->params['include']:[]
		);

		return $file;
	}
}