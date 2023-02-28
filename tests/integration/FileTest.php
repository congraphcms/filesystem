<?php

use Illuminate\Support\Facades\Cache;
use Symfony\Component\VarDumper\VarDumper as Dumper;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

require_once(__DIR__ . '/../database/seeders/TestDbSeeder.php');
require_once(__DIR__ . '/../database/seeders/ClearDB.php');

class FileTest extends Orchestra\Testbench\TestCase
{

	// ----------------------------------------
    // ENVIRONMENT
    // ----------------------------------------

    protected function getPackageProviders($app)
	{
		return [
			'Intervention\Image\ImageServiceProvider', 
			'Congraph\Core\CoreServiceProvider', 
			'Congraph\Filesystem\FilesystemServiceProvider'
		];
	}

    /**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 *
	 * @return void
	 */
	protected function defineEnvironment($app)
	{
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', [
			'driver'   	=> 'mysql',
			'host'      => '127.0.0.1',
			'port'		=> '3306',
			'database'	=> 'congraph_testbench',
			'username'  => 'root',
			'password'  => '',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		]);

		$app['config']->set('cache.default', 'file');

		$app['config']->set('cache.stores.file', [
			'driver'	=> 'file',
			'path'   	=> realpath(__DIR__ . '/../storage/cache/'),
		]);

		$app['config']->set('filesystems.default', 'local');

		$app['config']->set('filesystems.disks.local', [
			'driver'	=> 'local',
			'root'   	=> realpath(__DIR__ . '/../storage/'),
		]);

	}

    // ----------------------------------------
    // DATABASE
    // ----------------------------------------

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../../database/migrations'));

        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
        });
    }


    // ----------------------------------------
    // SETUP
    // ----------------------------------------

    public function setUp(): void {
		parent::setUp();

		$this->d = new Dumper();

        $this->artisan('db:seed', [
			'--class' => 'TestDbSeeder'
		]);

		Storage::deleteDirectory('files');
		Storage::deleteDirectory('uploads');

		Storage::copy('temp/test.jpg', 'uploads/test.jpg');

		Storage::copy('temp/test.jpg', 'files/1.jpg');
	}

	public function tearDown(): void {
		$this->artisan('db:seed', [
			'--class' => 'ClearDB'
		]);

		Storage::deleteDirectory('files');
		Storage::deleteDirectory('uploads');

		$cacheFiles = Storage::files('cache');
		$cacheDirs = Storage::directories('cache');

		foreach ($cacheFiles as $file) {
			Storage::delete($file);
		}
		foreach ($cacheDirs as $dir) {
			Storage::deleteDirectory($dir);
		}
		parent::tearDown();
	}

    // ----------------------------------------
    // TESTS **********************************
    // ----------------------------------------

	public function testCreateFile()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'file' => new UploadedFile(
				realpath(__DIR__ . '/../storage/uploads/test.jpg'),
				'test.jpg',
				'image/jpeg',
				null,
				true
			)
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileCreateCommand::class);
		$command->setParams($params);

		try {
			$result = $bus->dispatch($command);
		} catch(\Congraph\Core\Exceptions\ValidationException $e) {
			$this->d->dump($e->getErrors());
		}
		
		// $this->d->dump($result->toArray());
		$this->assertTrue(Storage::has('files/test.jpg'));
		
	}

	
	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\ValidationException::class);

		$params = [
			'file' => new UploadedFile(
				realpath(__DIR__ . '/../storage/uploads/test.jpg'),
				'test.jpg',
				'image/jpeg'
			)
		];


		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileCreateCommand::class);
		$command->setParams($params);

		$result = $bus->dispatch($command);
	}

	public function testUpdateFile()
	{
		fwrite(STDOUT, __METHOD__ . "\n");
		
		$params = [
			'caption' => 'test file',
			'description' => 'test description'
		];

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileUpdateCommand::class);
		$command->setParams($params);
		$command->setId(1);

		$result = $bus->dispatch($command);
		
		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals($result->caption, 'test file');
		$this->assertEquals($result->description, 'test description');
		// $this->d->dump($result->toArray());
	}

	public function testDeleteFile()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileDeleteCommand::class);
		$command->setId(1);

		$result = $bus->dispatch($command);

		$this->assertEquals($result, 1);
		$this->assertFalse(Storage::has('files/1.jpg'));
		// $this->d->dump($result);

	}

	
	public function testDeleteException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$this->expectException(\Congraph\Core\Exceptions\NotFoundException::class);

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileDeleteCommand::class);
		$command->setId(133);

		$result = $bus->dispatch($command);
	}
	
	public function testFetchFile()
	{

		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileFetchCommand::class);
		$command->setId(1);

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals($result->url, 'files/1.jpg');
		// $this->d->dump($result->toArray());
	}

	public function testFetchFileByUrl()
	{

		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileFetchCommand::class);
		$command->setId('files/1.jpg');

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals( 'files/1.jpg', $result->url);
		$this->assertEquals(1, $result->id);
		// $this->d->dump($result->toArray());
	}

	
	public function testGetFiles()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileGetCommand::class);

		$result = $bus->dispatch($command);

		$this->assertTrue($result instanceof Congraph\Core\Repositories\Collection);
		$this->assertEquals(count($result), 1);
		// $this->d->dump($result->toArray());

	}

	public function testFileServe()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileServeCommand::class);
		$command->setId('files/1.jpg');

		$result = $bus->dispatch($command);

		$this->assertEquals(Storage::get('files/1.jpg'), $result['content']);
	}

	public function testFileServeVersion()
	{
		fwrite(STDOUT, __METHOD__ . "\n");
		
		$app = $this->createApplication();
		$bus = $app->make('Congraph\Core\Bus\CommandDispatcher');
		$command = $app->make(\Congraph\Filesystem\Commands\Files\FileServeCommand::class);
		$command->setParams(['version' => 'admin_thumb']);
		$command->setId('files/1.jpg');

		$result = $bus->dispatch($command);

		$thumbUrl = realpath(__DIR__ . '/../storage/') . '/files/1.jpg';
		$thumb = Image::make($thumbUrl);
		$thumb->fit(50, 50);
		$thumbContent = (string) $thumb->encode();
		$this->assertEquals($thumbContent, $result['content']);
	}

}