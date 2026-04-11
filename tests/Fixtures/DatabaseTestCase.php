<?php

require_once __DIR__ . '/../database/seeders/TestDbSeeder.php';
require_once __DIR__ . '/../database/seeders/ClearDB.php';

use Congraph\Core\Bus\CommandDispatcher;

abstract class DatabaseTestCase extends \Orchestra\Testbench\TestCase
{
	protected function getPackageProviders($app)
	{
		return [
			\Intervention\Image\Laravel\ServiceProvider::class,
			\Congraph\Core\CoreServiceProvider::class,
			\Congraph\Filesystem\FilesystemServiceProvider::class,
		];
	}

	protected function defineEnvironment($app)
	{
		$driver = env('DB_CONNECTION', 'sqlite');

		$connection = [
			'driver' => $driver,
			'prefix' => '',
		];

		if ($driver === 'sqlite') {
			$connection['database'] = env('DB_DATABASE', ':memory:');
			$connection['foreign_key_constraints'] = true;
		} else {
			$connection['host'] = env('DB_HOST', '127.0.0.1');
			$connection['port'] = env('DB_PORT', $driver === 'pgsql' ? '5432' : '3306');
			$connection['database'] = env('DB_DATABASE', 'congraph_test');
			$connection['username'] = env('DB_USERNAME', 'congraph');
			$connection['password'] = env('DB_PASSWORD', 'congraph');
			$connection['charset'] = 'utf8';
			$connection['prefix_indexes'] = true;
			$connection['schema'] = env('DB_SCHEMA', 'public');
			$connection['sslmode'] = env('DB_SSLMODE', 'prefer');

			if ($driver === 'mysql') {
				$connection['charset'] = 'utf8mb4';
				$connection['collation'] = 'utf8mb4_unicode_ci';
			}
		}

		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', $connection);
		$app['config']->set('cache.default', 'file');
		$app['config']->set('cache.stores.file', [
			'driver' => 'file',
			'path' => realpath(__DIR__ . '/../storage/cache/') ?: __DIR__ . '/../storage/cache',
		]);
		$app['config']->set('filesystems.default', 'local');
		$app['config']->set('filesystems.disks.local', [
			'driver' => 'local',
			'root' => realpath(__DIR__ . '/../storage/') ?: __DIR__ . '/../storage',
		]);
	}

	protected function defineDatabaseMigrations()
	{
		$this->loadMigrationsFrom(realpath(__DIR__ . '/../../database/migrations'));

		$this->artisan('migrate', ['--database' => 'testbench'])->run();

		$this->beforeApplicationDestroyed(function () {
			$this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
		});
	}

	protected function setUp(): void
	{
		parent::setUp();

		foreach ($this->seeders() as $seeder) {
			$this->artisan('db:seed', [
				'--class' => $seeder,
			]);
		}

		\Illuminate\Support\Facades\Storage::deleteDirectory('files');
		\Illuminate\Support\Facades\Storage::deleteDirectory('uploads');
		\Illuminate\Support\Facades\Storage::makeDirectory('files');
		\Illuminate\Support\Facades\Storage::makeDirectory('uploads');
		\Illuminate\Support\Facades\Storage::copy('temp/test.jpg', 'uploads/test.jpg');
		\Illuminate\Support\Facades\Storage::copy('temp/test.jpg', 'files/1.jpg');
	}

	protected function tearDown(): void
	{
		foreach ($this->cleanupSeeders() as $seeder) {
			$this->artisan('db:seed', [
				'--class' => $seeder,
			]);
		}

		\Illuminate\Support\Facades\Storage::deleteDirectory('files');
		\Illuminate\Support\Facades\Storage::deleteDirectory('uploads');

		$cacheFiles = \Illuminate\Support\Facades\Storage::files('cache');
		$cacheDirs = \Illuminate\Support\Facades\Storage::directories('cache');
		foreach ($cacheFiles as $file) {
			\Illuminate\Support\Facades\Storage::delete($file);
		}
		foreach ($cacheDirs as $dir) {
			\Illuminate\Support\Facades\Storage::deleteDirectory($dir);
		}

		parent::tearDown();
	}

	protected function seeders(): array
	{
		return [];
	}

	protected function cleanupSeeders(): array
	{
		return [];
	}

	protected function dispatch(object $command): mixed
	{
		return $this->app->make(CommandDispatcher::class)->dispatch($command);
	}
}
