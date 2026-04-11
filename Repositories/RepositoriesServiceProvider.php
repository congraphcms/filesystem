<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Repositories;

use Congraph\Contracts\Core\ObjectResolverContract;
use Congraph\Contracts\Filesystem\FileRepositoryContract;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoriesServiceProvider service provider for managers
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
class RepositoriesServiceProvider extends ServiceProvider {

	/**
	 * Boot
	 * @return void
	 */
	public function boot()
	{
		$this->mapObjectResolvers();
	}
	
	/**
	 * Register
	 * 
	 * @return void
	 */
	public function register() {
		$this->registerRepositories();
	}

	/**
	 * Register the Attribute Repository
	 *
	 * @return void
	 */
	public function registerRepositories() {
		$this->app->singleton(FileRepository::class, function($app) {
			return new FileRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			FileRepository::class, FileRepositoryContract::class
		);
	}

	/**
	 * Map repositories to object resolver
	 *
	 * @return void
	 */
	public function mapObjectResolvers() {
		$mappings = [
			'file' => FileRepository::class,
		];

		$this->app->make(ObjectResolverContract::class)->maps($mappings);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			FileRepository::class,
			FileRepositoryContract::class,
		];
	}


}
