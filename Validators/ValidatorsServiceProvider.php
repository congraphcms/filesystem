<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Validators;

use Illuminate\Support\ServiceProvider;

use Congraph\Filesystem\Validators\Files\FileCreateValidator;
use Congraph\Filesystem\Validators\Files\FileUpdateValidator;
use Congraph\Filesystem\Validators\Files\FileDeleteValidator;
use Congraph\Filesystem\Validators\Files\FileFetchValidator;
use Congraph\Filesystem\Validators\Files\FileGetValidator;
use Congraph\Filesystem\Validators\Files\FileServeValidator;

/**
 * ValidatorsServiceProvider service provider for validators
 * 
 * It will register all validators to app container
 * 
 * @uses   		Illuminate\Support\ServiceProvider
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
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
			'Congraph\Filesystem\Commands\Files\FileCreateCommand' => 
				'Congraph\Filesystem\Validators\Files\FileCreateValidator@validate',
			'Congraph\Filesystem\Commands\Files\FileUpdateCommand' => 
				'Congraph\Filesystem\Validators\Files\FileUpdateValidator@validate',
			'Congraph\Filesystem\Commands\Files\FileDeleteCommand' => 
				'Congraph\Filesystem\Validators\Files\FileDeleteValidator@validate',
			'Congraph\Filesystem\Commands\Files\FileFetchCommand' => 
				'Congraph\Filesystem\Validators\Files\FileFetchValidator@validate',
			'Congraph\Filesystem\Commands\Files\FileGetCommand' => 
				'Congraph\Filesystem\Validators\Files\FileGetValidator@validate',
			'Congraph\Filesystem\Commands\Files\FileServeCommand' => 
				'Congraph\Filesystem\Validators\Files\FileServeValidator@validate'
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
		$this->app->bind('Congraph\Filesystem\Validators\Files\FileCreateValidator', function($app){
			return new FileCreateValidator();
		});

		$this->app->bind('Congraph\Filesystem\Validators\Files\FileUpdateValidator', function($app){
			return new FileUpdateValidator();
		});

		$this->app->bind('Congraph\Filesystem\Validators\Files\FileDeleteValidator', function($app){
			return new FileDeleteValidator();
		});

		$this->app->bind('Congraph\Filesystem\Validators\Files\FileFetchValidator', function($app){
			return new FileFetchValidator();
		});

		$this->app->bind('Congraph\Filesystem\Validators\Files\FileGetValidator', function($app){
			return new FileGetValidator();
		});

		$this->app->bind('Congraph\Filesystem\Validators\Files\FileServeValidator', function($app){
			return new FileServeValidator();
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
			'Congraph\Filesystem\Validators\Files\FileCreateValidator',
			'Congraph\Filesystem\Validators\Files\FileUpdateValidator',
			'Congraph\Filesystem\Validators\Files\FileDeleteValidator',
			'Congraph\Filesystem\Validators\Files\FileFetchValidator',
			'Congraph\Filesystem\Validators\Files\FileGetValidator',
			'Congraph\Filesystem\Validators\Files\FileServeValidator',

		];
	}
}