<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem;

use Illuminate\Support\ServiceProvider;

/**
 * EavServiceProvider service provider for EAV package
 * 
 * It will register all manager to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FilesystemServiceProvider extends ServiceProvider {

	/**
	* Register
	* 
	* @return void
	*/
	public function register() {
		$this->mergeConfigFrom(realpath(__DIR__ . '/config/cookbook.php'), 'cookbook');
		$this->registerServiceProviders();
	}

	/**
	 * Boot
	 * 
	 * @return void
	 */
	public function boot() {
		$this->publishes([
			__DIR__.'/config/cookbook.php' => config_path('cookbook.php'),
		]);
	}

	/**
	 * Register Service Providers for this package
	 * 
	 * @return void
	 */
	protected function registerServiceProviders(){

		// Core
		// -----------------------------------------------------------------------------
		$this->app->register('Cookbook\Core\CoreServiceProvider');

		// Commands
		// -----------------------------------------------------------------------------
		$this->app->register('Cookbook\Filesystem\Commands\CommandsServiceProvider');

		// Handlers
		// -----------------------------------------------------------------------------
		$this->app->register('Cookbook\Filesystem\Handlers\HandlersServiceProvider');

		// Validators
		// -----------------------------------------------------------------------------
		$this->app->register('Cookbook\Filesystem\Validators\ValidatorsServiceProvider');

		// Repositories
		// -----------------------------------------------------------------------------
		$this->app->register('Cookbook\Filesystem\Repositories\RepositoriesServiceProvider');

		

		
	}

}