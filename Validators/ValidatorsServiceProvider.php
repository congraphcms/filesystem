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

use Congraph\Filesystem\Commands\Files\FileCreateCommand;
use Congraph\Filesystem\Commands\Files\FileDeleteCommand;
use Congraph\Filesystem\Commands\Files\FileFetchCommand;
use Congraph\Filesystem\Commands\Files\FileGetCommand;
use Congraph\Filesystem\Commands\Files\FileServeCommand;
use Congraph\Filesystem\Commands\Files\FileUpdateCommand;
use Illuminate\Contracts\Bus\Dispatcher;
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
			FileCreateCommand::class => FileCreateValidator::class . '@validate',
			FileUpdateCommand::class => FileUpdateValidator::class . '@validate',
			FileDeleteCommand::class => FileDeleteValidator::class . '@validate',
			FileFetchCommand::class => FileFetchValidator::class . '@validate',
			FileGetCommand::class => FileGetValidator::class . '@validate',
			FileServeCommand::class => FileServeValidator::class . '@validate'
		];

		$this->app->make(Dispatcher::class)->mapValidators($mappings);
	}

	/**
	 * Registers Command Handlers
	 *
	 * @return void
	 */
	public function registerValidators() {

		// Files
		$this->app->bind(FileCreateValidator::class, function($app){
			return new FileCreateValidator();
		});

		$this->app->bind(FileUpdateValidator::class, function($app){
			return new FileUpdateValidator();
		});

		$this->app->bind(FileDeleteValidator::class, function($app){
			return new FileDeleteValidator();
		});

		$this->app->bind(FileFetchValidator::class, function($app){
			return new FileFetchValidator();
		});

		$this->app->bind(FileGetValidator::class, function($app){
			return new FileGetValidator();
		});

		$this->app->bind(FileServeValidator::class, function($app){
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
			FileCreateValidator::class,
			FileUpdateValidator::class,
			FileDeleteValidator::class,
			FileFetchValidator::class,
			FileGetValidator::class,
			FileServeValidator::class,

		];
	}
}
