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
 * FileUpdateCommand class
 * 
 * Command for updating file
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileUpdateCommand extends RepositoryCommand
{
	/**
	 * Create new FileUpdateCommand
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
		$file = $this->repository->update($this->id, $this->params);

		// Storage::put($file->url, file_get_contents($this->params['file']->getRealPath()));

		return $file;
	}
}
