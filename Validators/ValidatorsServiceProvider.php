<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Validators;

use Illuminate\Support\ServiceProvider;

use Cookbook\Filesystem\Validators\Files\FileCreateValidator;
use Cookbook\Filesystem\Validators\Files\FileUpdateValidator;
use Cookbook\Filesystem\Validators\Files\FileDeleteValidator;
use Cookbook\Filesystem\Validators\Files\FileFetchValidator;
use Cookbook\Filesystem\Validators\Files\FileGetValidator;

/**
 * ValidatorsServiceProvider service provider for validators
 * 
 * It will register all validators to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class ValidatorsServiceProvider extends ServiceProvider {

	/**
	 * Boot
	 * 
	 * @return void
	 */
	public function boot() {
		$this->mapValidators();
	}


	/**
	 * Register
	 * 
	 * @return void
	 */
	public function register() {
		$this->registerValidators();
	}

	/**
	 * Maps Validators
	 *
	 * @return void
	 */
	public function mapValidators() {
		
		$mappings = [
			// Files
			'Cookbook\Filesystem\Commands\Files\FileCreateCommand' => 
				'Cookbook\Filesystem\Validators\Files\FileCreateValidator@validate',
			'Cookbook\Filesystem\Commands\Files\FileUpdateCommand' => 
				'Cookbook\Filesystem\Validators\Files\FileUpdateValidator@validate',
			'Cookbook\Filesystem\Commands\Files\FileDeleteCommand' => 
				'Cookbook\Filesystem\Validators\Files\FileDeleteValidator@validate',
			'Cookbook\Filesystem\Commands\Files\FileFetchCommand' => 
				'Cookbook\Filesystem\Validators\Files\FileFetchValidator@validate',
			'Cookbook\Filesystem\Commands\Files\FileGetCommand' => 
				'Cookbook\Filesystem\Validators\Files\FileGetValidator@validate'
		];

		$this->app->make('Illuminate\Contracts\Bus\Dispatcher')->mapValidators($mappings);
	}

	/**
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerValidators() {

		// Files
		$this->app->bind('Cookbook\Filesystem\Validators\Files\FileCreateValidator', function($app){
			return new FileCreateValidator();
		});

		$this->app->bind('Cookbook\Filesystem\Validators\Files\FileUpdateValidator', function($app){
			return new FileUpdateValidator();
		});

		$this->app->bind('Cookbook\Filesystem\Validators\Files\FileDeleteValidator', function($app){
			return new FileDeleteValidator();
		});

		$this->app->bind('Cookbook\Filesystem\Validators\Files\FileFetchValidator', function($app){
			return new FileFetchValidator();
		});

		$this->app->bind('Cookbook\Filesystem\Validators\Files\FileGetValidator', function($app){
			return new FileGetValidator();
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
			'Cookbook\Filesystem\Validators\Files\FileCreateValidator',
			'Cookbook\Filesystem\Validators\Files\FileUpdateValidator',
			'Cookbook\Filesystem\Validators\Files\FileDeleteValidator',
			'Cookbook\Filesystem\Validators\Files\FileFetchValidator',
			'Cookbook\Filesystem\Validators\Files\FileGetValidator',

		];
	}
}