<?php

// include_once(realpath(__DIR__.'/../LaravelMocks.php'));
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Debug\Dumper;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileTest extends Orchestra\Testbench\TestCase
{

	public function setUp()
	{
		// fwrite(STDOUT, __METHOD__ . "\n");
		parent::setUp();
		// unset($this->app);
		// call migrations specific to our tests, e.g. to seed the db
		// the path option should be relative to the 'path.database'
		// path unless `--path` option is available.
		$this->artisan('migrate', [
			'--database' => 'testbench',
			'--realpath' => realpath(__DIR__.'/../../migrations'),
		]);

		$this->artisan('db:seed', [
			'--class' => 'Cookbook\Filesystem\Seeders\TestDbSeeder'
		]);

		$this->d = new Dumper();


		Storage::deleteDir('files');
		Storage::deleteDir('uploads');

		Storage::copy('temp/test.jpg', 'uploads/test.jpg');

		Storage::copy('temp/test.jpg', 'files/1.jpg');

		// vfsStream::setup('root', null, $fileStructure);

		// $this->app = $this->createApplication();

		// $this->bus = $this->app->make('Illuminate\Contracts\Bus\Dispatcher');

	}

	public function tearDown()
	{
		// fwrite(STDOUT, __METHOD__ . "\n");
		// parent::tearDown();
		
		$this->artisan('migrate:reset');
		// unset($this->app);
		Storage::deleteDir('files');
		Storage::deleteDir('uploads');


		parent::tearDown();
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 *
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', [
			'driver'   	=> 'mysql',
			'host'      => '127.0.0.1',
			'port'		=> '33060',
			'database'	=> 'cookbook_testbench',
			'username'  => 'homestead',
			'password'  => 'secret',
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

		// $config = require(realpath(__DIR__.'/../../config/eav.php'));

		// $app['config']->set(
		// 	'Cookbook::eav', $config
		// );

		// var_dump('CONFIG SETTED');
	}

	protected function getPackageProviders($app)
	{
		return ['Cookbook\Filesystem\FilesystemServiceProvider'];
	}

	public function testCreateFile()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'file' => new UploadedFile(realpath(__DIR__ . '/../storage/uploads/test.jpg'), 'test.jpg', 'image/jpeg', Storage::getSize('uploads/test.jpg'), null, true)
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileCreateCommand($params));
		
		$this->d->dump($result->toArray());
		$this->assertTrue(Storage::has('files/test.jpg'));
		
	}

	/**
	 * @expectedException \Cookbook\Core\Exceptions\ValidationException
	 */
	public function testCreateException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$params = [
			'file' => new UploadedFile(realpath(__DIR__ . '/../storage/uploads/test.jpg'), 'test.jpg', 'image/jpeg')
		];


		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		
		$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileCreateCommand($params));
	}

	public function testUpdateFile()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$params = [
			'caption' => 'test file',
			'description' => 'test description'
		];
		
		$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileUpdateCommand($params, 1) );
		
		$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals($result->caption, 'test file');
		$this->assertEquals($result->description, 'test description');
		$this->d->dump($result->toArray());
	}

	// /**
	//  * @expectedException \Cookbook\Core\Exceptions\ValidationException
	//  */
	// public function testUpdateException()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$params = [
			
	// 	];

	// 	$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileUpdateCommand($params, 1) );
	// }

	public function testDeleteFile()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileDeleteCommand([], 1) );

		$this->assertEquals($result, 1);
		$this->assertFalse(Storage::has('files/1.jpg'));
		$this->d->dump($result);

	}

	/**
	 * @expectedException \Cookbook\Core\Exceptions\NotFoundException
	 */
	public function testDeleteException()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileDeleteCommand([], 133) );
	}
	
	public function testFetchFile()
	{

		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

		$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileFetchCommand([], 1));

		$this->assertTrue($result instanceof Cookbook\Core\Repositories\Model);
		$this->assertTrue(is_int($result->id));
		$this->assertEquals($result->url, 'files/1.jpg');
		$this->d->dump($result->toArray());
		

	}

	
	public function testGetFiles()
	{
		fwrite(STDOUT, __METHOD__ . "\n");

		$app = $this->createApplication();
		$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
		$result = $bus->dispatch( new Cookbook\Filesystem\Commands\Files\FileGetCommand([]));

		$this->assertTrue($result instanceof Cookbook\Core\Repositories\Collection);
		$this->assertEquals(count($result), 1);
		$this->d->dump($result->toArray());

	}

	// public function testGetParams()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$result = $bus->dispatch( new Cookbook\Eav\Commands\Attributes\AttributeGetCommand(['sort' => ['-code'], 'limit' => 3, 'offset' => 1]));

	// 	$this->assertTrue(is_array($result));
	// 	$this->assertEquals(count($result), 3);

	// 	$this->d->dump($result);
	// }

	// public function testGetFilters()
	// {
	// 	fwrite(STDOUT, __METHOD__ . "\n");

	// 	$app = $this->createApplication();
	// 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

	// 	$filter = [ 'id' => 5 ];

	// 	$result = $bus->dispatch( new Cookbook\Eav\Commands\Attributes\AttributeGetCommand(['filter' => $filter]));

	// 	$this->d->dump($result);
		
	// 	$this->assertTrue(is_array($result));
	// 	$this->assertEquals(1, count($result));

		

	// 	$filter = [ 'id' => ['in'=>'5,6,7'] ];

	// 	$result = $bus->dispatch( new Cookbook\Eav\Commands\Attributes\AttributeGetCommand(['filter' => $filter]));

	// 	$this->assertTrue(is_array($result));
	// 	$this->assertEquals(3, count($result));

	// 	$this->d->dump($result);

	// 	$filter = [ 'id' => ['nin'=>[5,6,7,1]] ];

	// 	$result = $bus->dispatch( new Cookbook\Eav\Commands\Attributes\AttributeGetCommand(['filter' => $filter]));

	// 	$this->assertTrue(is_array($result));
	// 	$this->assertEquals(3, count($result));

	// 	$this->d->dump($result);

	// 	$filter = [ 'id' => ['lt'=>3] ];

	// 	$result = $bus->dispatch( new Cookbook\Eav\Commands\Attributes\AttributeGetCommand(['filter' => $filter]));

	// 	$this->assertTrue(is_array($result));
	// 	$this->assertEquals(2, count($result));

	// 	$this->d->dump($result);

	// 	$filter = [ 'id' => ['lte'=>3] ];

	// 	$result = $bus->dispatch( new Cookbook\Eav\Commands\Attributes\AttributeGetCommand(['filter' => $filter]));

	// 	$this->assertTrue(is_array($result));
	// 	$this->assertEquals(3, count($result));

	// 	$this->d->dump($result);

	// 	$filter = [ 'id' => ['ne'=>3] ];

	// 	$result = $bus->dispatch( new Cookbook\Eav\Commands\Attributes\AttributeGetCommand(['filter' => $filter]));

	// 	$this->assertTrue(is_array($result));
	// 	$this->assertEquals(6, count($result));

	// 	$this->d->dump($result);
	// }

}