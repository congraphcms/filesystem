<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Handlers;

use Illuminate\Support\ServiceProvider;

use Cookbook\Filesystem\Handlers\Commands\Files\FileCreateHandler;
use Cookbook\Filesystem\Handlers\Commands\Files\FileUpdateHandler;
use Cookbook\Filesystem\Handlers\Commands\Files\FileDeleteHandler;
use Cookbook\Filesystem\Handlers\Commands\Files\FileFetchHandler;
use Cookbook\Filesystem\Handlers\Commands\Files\FileGetHandler;
use Cookbook\Filesystem\Handlers\Commands\Files\FileServeHandler;
use Cookbook\Filesystem\Handlers\Images\AdminThumbHandler;

/**
 * HandlersServiceProvider service provider for handlers
 * 
 * It will register all handlers to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
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
		// 'Cookbook\Eav\Events\AttributeSets\AfterAttributeSetFetch' => [
		// 	'Cookbook\Eav\Handlers\Events\AttributeSets\AfterAttributeSetFetchHandler',
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
			'Cookbook\Filesystem\Commands\Files\FileCreateCommand' => 
				'Cookbook\Filesystem\Handlers\Commands\Files\FileCreateHandler@handle',
			'Cookbook\Filesystem\Commands\Files\FileUpdateCommand' => 
				'Cookbook\Filesystem\Handlers\Commands\Files\FileUpdateHandler@handle',
			'Cookbook\Filesystem\Commands\Files\FileDeleteCommand' => 
				'Cookbook\Filesystem\Handlers\Commands\Files\FileDeleteHandler@handle',
			'Cookbook\Filesystem\Commands\Files\FileFetchCommand' => 
				'Cookbook\Filesystem\Handlers\Commands\Files\FileFetchHandler@handle',
			'Cookbook\Filesystem\Commands\Files\FileGetCommand' => 
				'Cookbook\Filesystem\Handlers\Commands\Files\FileGetHandler@handle',
			'Cookbook\Filesystem\Commands\Files\FileServeCommand' => 
				'Cookbook\Filesystem\Handlers\Commands\Files\FileServeHandler@handle'
			
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
		
		$this->app->bind('Cookbook\Filesystem\Handlers\Commands\Files\FileCreateHandler', function($app){
			return new FileCreateHandler($app->make('Cookbook\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Cookbook\Filesystem\Handlers\Commands\Files\FileUpdateHandler', function($app){
			return new FileUpdateHandler($app->make('Cookbook\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Cookbook\Filesystem\Handlers\Commands\Files\FileDeleteHandler', function($app){
			return new FileDeleteHandler($app->make('Cookbook\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Cookbook\Filesystem\Handlers\Commands\Files\FileFetchHandler', function($app){
			return new FileFetchHandler($app->make('Cookbook\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Cookbook\Filesystem\Handlers\Commands\Files\FileGetHandler', function($app){
			return new FileGetHandler($app->make('Cookbook\Contracts\Filesystem\FileRepositoryContract'));
		});

		$this->app->bind('Cookbook\Filesystem\Handlers\Commands\Files\FileServeHandler', function($app){
			return new FileServeHandler($app);
		});

		$this->app->bind('Cookbook\Filesystem\Handlers\Images\AdminThumbHandler', function($app){
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
			'Cookbook\Filesystem\Handlers\Commands\Files\FileCreateHandler',
			'Cookbook\Filesystem\Handlers\Commands\Files\FileUpdateHandler',
			'Cookbook\Filesystem\Handlers\Commands\Files\FileDeleteHandler',
			'Cookbook\Filesystem\Handlers\Commands\Files\FileFetchHandler',
			'Cookbook\Filesystem\Handlers\Commands\Files\FileGetHandler',
			'Cookbook\Filesystem\Handlers\Commands\Files\FileServeHandler'
		];
	}
}