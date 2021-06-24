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

use Congraph\Contracts\Filesystem\FileRepositoryContract;
use Congraph\Core\Bus\RepositoryCommand;

/**
 * FileFetchCommand class
 * 
 * Command for fetching file
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileFetchCommand extends RepositoryCommand
{
	/**
	 * Create new FileFetchCommand
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
	 * @return void
	 */
	public function handle()
	{
		$file = $this->repository->fetch(
			$this->id, 
			(!empty($this->params['include']))?$this->params['include']:[]
		);

		return $file;
	}
}
