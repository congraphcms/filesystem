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

use Congraph\Filesystem\Handlers\Images\AdminThumbHandler;
use Congraph\Filesystem\Handlers\Images\AdminImageHandler;

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
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
	protected $defer = true;

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
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerCommandHandlers() {

		$this->app->bind('Congraph\Filesystem\Handlers\Images\AdminThumbHandler', function($app){
			return new AdminThumbHandler();
		});
		$this->app->bind('Congraph\Filesystem\Handlers\Images\AdminImageHandler', function($app){
			return new AdminImageHandler();
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
			'Congraph\Filesystem\Handlers\Images\AdminThumbHandler',
			'Congraph\Filesystem\Handlers\Images\AdminImageHandler'
		];
	}
}