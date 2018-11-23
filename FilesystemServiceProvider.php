<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem;

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
 * @package 	congraph/filesystem
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
		$this->mergeConfigFrom(realpath(__DIR__ . '/config/config.php'), 'cb.files');
		$this->registerServiceProviders();
	}

	/**
	 * Boot
	 * 
	 * @return void
	 */
	public function boot() {
		$this->publishes([
			__DIR__.'/config/config.php' => config_path('cb.files.php'),
			__DIR__.'/database/migrations' => database_path('/migrations'),
		]);
	}

	/**
	 * Register Service Providers for this package
	 * 
	 * @return void
	 */
	protected function registerServiceProviders(){

		// Commands
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Filesystem\Commands\CommandsServiceProvider');

		// Handlers
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Filesystem\Handlers\HandlersServiceProvider');

		// Validators
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Filesystem\Validators\ValidatorsServiceProvider');

		// Repositories
		// -----------------------------------------------------------------------------
		$this->app->register('Congraph\Filesystem\Repositories\RepositoriesServiceProvider');

		

		
	}

}