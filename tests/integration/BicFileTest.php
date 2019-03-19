<?php

// include_once(realpath(__DIR__.'/../LaravelMocks.php'));
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Debug\Dumper;
// use org\bovigo\vfs\vfsStream;
// use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BicFileTest extends Orchestra\Testbench\TestCase
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
            '--realpath' => realpath(__DIR__.'/../../database/migrations/bic'),
        ]);

        $this->artisan('db:seed', [
            '--class' => 'Congraph\Filesystem\Seeders\BicTestDbSeeder'
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
        
        // $this->artisan('migrate:reset');
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
            'port'		=> '3306',
            'database'	=> 'bic_test',
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

        // $config = require(realpath(__DIR__.'/../../config/eav.php'));

        // $app['config']->set(
        // 	'Cookbook::eav', $config
        // );

        // var_dump('CONFIG SETTED');
    }

    protected function getPackageProviders($app)
    {
        return [
            'Intervention\Image\ImageServiceProvider',
            'Congraph\Core\CoreServiceProvider',
            'Congraph\Filesystem\FilesystemServiceProvider'
        ];
    }

    public function testCreateFile()
    {
        fwrite(STDOUT, __METHOD__ . "\n");

        $params = [
            'file' => new UploadedFile(realpath(__DIR__ . '/../storage/uploads/test.jpg'), 'test.jpg', 'image/jpeg', Storage::getSize('uploads/test.jpg'), null, true)
        ];


        $app = $this->createApplication();
        $bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
        
        $result = $bus->dispatch(new Congraph\Filesystem\Commands\Files\FileCreateCommand($params));
        
        $this->d->dump($result->toArray());
        $this->assertTrue(Storage::has('files/test.jpg'));
    }

    /**
     * @expectedException \Congraph\Core\Exceptions\ValidationException
     */
    public function testCreateException()
    {
        fwrite(STDOUT, __METHOD__ . "\n");

        $params = [
            'file' => new UploadedFile(realpath(__DIR__ . '/../storage/uploads/test.jpg'), 'test.jpg', 'image/jpeg')
        ];


        $app = $this->createApplication();
        $bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
        
        $result = $bus->dispatch(new Congraph\Filesystem\Commands\Files\FileCreateCommand($params));
    }

    public function testDeleteFile()
    {
        fwrite(STDOUT, __METHOD__ . "\n");

        $app = $this->createApplication();
        $bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

        $result = $bus->dispatch(new Congraph\Filesystem\Commands\Files\FileDeleteCommand([], 1));

        $this->assertEquals($result, 1);
        $this->assertFalse(Storage::has('files/1.jpg'));
        $this->d->dump($result);
    }

    /**
     * @expectedException \Congraph\Core\Exceptions\NotFoundException
     */
    public function testDeleteException()
    {
        fwrite(STDOUT, __METHOD__ . "\n");

        $app = $this->createApplication();
        $bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

        $result = $bus->dispatch(new Congraph\Filesystem\Commands\Files\FileDeleteCommand([], 133));
    }
    
    public function testFetchFile()
    {
        fwrite(STDOUT, __METHOD__ . "\n");

        $app = $this->createApplication();
        $bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');

        $result = $bus->dispatch(new Congraph\Filesystem\Commands\Files\FileFetchCommand([], 1));

        $this->assertTrue($result instanceof Congraph\Core\Repositories\Model);
        $this->assertTrue(is_int($result->id));
        $this->assertEquals($result->url, 'files/1.jpg');
        $this->d->dump($result->toArray());
    }

    
    public function testGetFiles()
    {
        fwrite(STDOUT, __METHOD__ . "\n");

        $app = $this->createApplication();
        $bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
        $result = $bus->dispatch(new Congraph\Filesystem\Commands\Files\FileGetCommand([]));

        $this->assertTrue($result instanceof Congraph\Core\Repositories\Collection);
        $this->assertEquals(count($result), 1);
        $this->d->dump($result->toArray());
    }

    public function testFileServe()
    {
        fwrite(STDOUT, __METHOD__ . "\n");

        $app = $this->createApplication();
        $bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
        $content = $bus->dispatch(new Congraph\Filesystem\Commands\Files\FileServeCommand('files/1.jpg', null));

        $this->assertEquals(Storage::get('files/1.jpg'), $content);
    }

    // public function testFileServeVersion()
    // {
    // 	fwrite(STDOUT, __METHOD__ . "\n");
        
    // 	$app = $this->createApplication();
    // 	$bus = $app->make('Illuminate\Contracts\Bus\Dispatcher');
    // 	$content = $bus->dispatch( new Congraph\Filesystem\Commands\Files\FileServeCommand('files/1.jpg', 'admin_thumb'));

    // 	$thumbUrl = realpath(__DIR__ . '/../storage/') . '/files/1.jpg';
    // 	$thumb = Image::make($thumbUrl);
    // 	$thumb->fit(200, 150);
    // 	$thumbContent = (string) $thumb->encode();
    // 	// $this->assertEquals($thumbContent, $content);
    // }
}
