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
class CommandsServiceProvider extends ServiceProvider {

	/**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
	protected $defer = true;


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
		
		$this->app->bind('Congraph\Filesystem\Commands\Files\FileCreateCommand', function($app){
			return new FileCreateCommand($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Commands\Files\FileUpdateCommand', function($app){
			return new FileUpdateCommand($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Commands\Files\FileDeleteCommand', function($app){
			return new FileDeleteCommand($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Commands\Files\FileFetchCommand', function($app){
			return new FileFetchCommand($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Commands\Files\FileGetCommand', function($app){
			return new FileGetCommand($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Commands\Files\FileServeCommand', function($app){
			return new FileServeCommand($app, $app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
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
			'Congraph\Filesystem\Commands\Files\FileCreateCommand',
			'Congraph\Filesystem\Commands\Files\FileUpdateCommand',
			'Congraph\Filesystem\Commands\Files\FileDeleteCommand',
			'Congraph\Filesystem\Commands\Files\FileFetchCommand',
			'Congraph\Filesystem\Commands\Files\FileGetCommand',
			'Congraph\Filesystem\Commands\Files\FileServeCommand',
		];
	}
}