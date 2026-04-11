<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Commands;

use Congraph\Contracts\Filesystem\FileRepositoryContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

use Congraph\Filesystem\Commands\Files\FileCreateCommand;
use Congraph\Filesystem\Commands\Files\FileUpdateCommand;
use Congraph\Filesystem\Commands\Files\FileDeleteCommand;
use Congraph\Filesystem\Commands\Files\FileFetchCommand;
use Congraph\Filesystem\Commands\Files\FileGetCommand;
use Congraph\Filesystem\Commands\Files\FileServeCommand;

/**
 * CommandsServiceProvider service provider for commands
 * 
 * It will register all commands to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class CommandsServiceProvider extends ServiceProvider implements DeferrableProvider {


	/**
	* Register
	* 
	* @return void
	*/
	public function register() {
		$this->registerCommands();
	}

	/**
	* Register Commands
	*
	* @return void
	*/
	public function registerCommands() {
		// Files
		
		$this->app->bind(FileCreateCommand::class, function($app){
			return new FileCreateCommand($app->make(FileRepositoryContract::class));
		});

		$this->app->bind(FileUpdateCommand::class, function($app){
			return new FileUpdateCommand($app->make(FileRepositoryContract::class));
		});

		$this->app->bind(FileDeleteCommand::class, function($app){
			return new FileDeleteCommand($app->make(FileRepositoryContract::class));
		});

		$this->app->bind(FileFetchCommand::class, function($app){
			return new FileFetchCommand($app->make(FileRepositoryContract::class));
		});

		$this->app->bind(FileGetCommand::class, function($app){
			return new FileGetCommand($app->make(FileRepositoryContract::class));
		});

		$this->app->bind(FileServeCommand::class, function($app){
			return new FileServeCommand($app->make(Container::class), $app->make(FileRepositoryContract::class));
		});
	}

	/**
     * Get the services provided by the provider.
     *
     * @return array
     */
	public function provides()
	{
		return [
			// Files
			FileCreateCommand::class,
			FileUpdateCommand::class,
			FileDeleteCommand::class,
			FileFetchCommand::class,
			FileGetCommand::class,
			FileServeCommand::class,
		];
	}
}
