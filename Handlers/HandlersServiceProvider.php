<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Handlers;

use Illuminate\Support\ServiceProvider;

use Congraph\Filesystem\Handlers\Commands\Files\FileCreateHandler;
use Congraph\Filesystem\Handlers\Commands\Files\FileUpdateHandler;
use Congraph\Filesystem\Handlers\Commands\Files\FileDeleteHandler;
use Congraph\Filesystem\Handlers\Commands\Files\FileFetchHandler;
use Congraph\Filesystem\Handlers\Commands\Files\FileGetHandler;
use Congraph\Filesystem\Handlers\Commands\Files\FileServeHandler;
use Congraph\Filesystem\Handlers\Images\AdminThumbHandler;

/**
 * HandlersServiceProvider service provider for handlers
 * 
 * It will register all handlers to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class HandlersServiceProvider extends ServiceProvider {

	/**
	 * The event listener mappings for package.
	 *
	 * @var array
	 */
	protected $listen = [
		// 'Congraph\Eav\Events\AttributeSets\AfterAttributeSetFetch' => [
		// 	'Congraph\Eav\Handlers\Events\AttributeSets\AfterAttributeSetFetchHandler',
		// ],
	];


	/**
	 * Boot
	 * 
	 * @return void
	 */
	public function boot() {
		$this->mapCommandHandlers();
	}


	/**
	 * Register
	 * 
	 * @return void
	 */
	public function register() {
		$this->registerCommandHandlers();
	}

	/**
	 * Maps Command Handlers
	 *
	 * @return void
	 */
	public function mapCommandHandlers() {
		
		$mappings = [
			// Files
			'Congraph\Filesystem\Commands\Files\FileCreateCommand' => 
				'Congraph\Filesystem\Handlers\Commands\Files\FileCreateHandler@handle',
			'Congraph\Filesystem\Commands\Files\FileUpdateCommand' => 
				'Congraph\Filesystem\Handlers\Commands\Files\FileUpdateHandler@handle',
			'Congraph\Filesystem\Commands\Files\FileDeleteCommand' => 
				'Congraph\Filesystem\Handlers\Commands\Files\FileDeleteHandler@handle',
			'Congraph\Filesystem\Commands\Files\FileFetchCommand' => 
				'Congraph\Filesystem\Handlers\Commands\Files\FileFetchHandler@handle',
			'Congraph\Filesystem\Commands\Files\FileGetCommand' => 
				'Congraph\Filesystem\Handlers\Commands\Files\FileGetHandler@handle',
			'Congraph\Filesystem\Commands\Files\FileServeCommand' => 
				'Congraph\Filesystem\Handlers\Commands\Files\FileServeHandler@handle'
			
		];

		$this->app->make('Illuminate\Contracts\Bus\Dispatcher')->maps($mappings);
	}

	/**
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerCommandHandlers() {
		
		// Files
		
		$this->app->bind('Congraph\Filesystem\Handlers\Commands\Files\FileCreateHandler', function($app){
			return new FileCreateHandler($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Handlers\Commands\Files\FileUpdateHandler', function($app){
			return new FileUpdateHandler($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Handlers\Commands\Files\FileDeleteHandler', function($app){
			return new FileDeleteHandler($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Handlers\Commands\Files\FileFetchHandler', function($app){
			return new FileFetchHandler($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Handlers\Commands\Files\FileGetHandler', function($app){
			return new FileGetHandler($app->make('Congraph\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Congraph\Filesystem\Handlers\Commands\Files\FileServeHandler', function($app){
			return new FileServeHandler($app);
		});

		$this->app->bind('Congraph\Filesystem\Handlers\Images\AdminThumbHandler', function($app){
			return new AdminThumbHandler();
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
			'Congraph\Filesystem\Handlers\Commands\Files\FileCreateHandler',
			'Congraph\Filesystem\Handlers\Commands\Files\FileUpdateHandler',
			'Congraph\Filesystem\Handlers\Commands\Files\FileDeleteHandler',
			'Congraph\Filesystem\Handlers\Commands\Files\FileFetchHandler',
			'Congraph\Filesystem\Handlers\Commands\Files\FileGetHandler',
			'Congraph\Filesystem\Handlers\Commands\Files\FileServeHandler'
		];
	}
}