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
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
	protected $defer = true;

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
		$this->app->singleton('Congraph\Filesystem\Repositories\FileRepository', function($app) {
			return new FileRepository(
				$app['db']->connection()
			);
		});

		$this->app->alias(
			'Congraph\Filesystem\Repositories\FileRepository', 'Congraph\Contracts\Filesystem\FileRepositoryContract'
		);
	}

	/**
	 * Map repositories to object resolver
	 *
	 * @return void
	 */
	public function mapObjectResolvers() {
		$mappings = [
			'file' => 'Congraph\Filesystem\Repositories\FileRepository',
		];

		$this->app->make('Congraph\Contracts\Core\ObjectResolverContract')->maps($mappings);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'Congraph\Filesystem\Repositories\FileRepository',
			'Congraph\Contracts\Filesystem\FileRepositoryContract'
		];
	}


}