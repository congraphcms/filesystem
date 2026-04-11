<?php

require_once __DIR__ . '/../Fixtures/DatabaseTestCase.php';

use Congraph\Contracts\Filesystem\FileRepositoryContract;
use Congraph\Filesystem\Commands\Files\FileCreateCommand;
use Congraph\Filesystem\Commands\Files\FileServeCommand;
use Congraph\Filesystem\Handlers\Images\AdminImageHandler;
use Congraph\Filesystem\Handlers\Images\AdminThumbHandler;
use Congraph\Filesystem\Repositories\FileRepository;
use Congraph\Filesystem\Validators\Files\FileCreateValidator;
use Congraph\Filesystem\Validators\Files\FileServeValidator;

final class FilesystemServiceProviderTest extends DatabaseTestCase
{
	public function testImportantBindingsResolveFromContainer(): void
	{
		$this->assertInstanceOf(FileRepository::class, $this->app->make(FileRepository::class));
		$this->assertInstanceOf(FileRepositoryContract::class, $this->app->make(FileRepositoryContract::class));
		$this->assertInstanceOf(FileCreateCommand::class, $this->app->make(FileCreateCommand::class));
		$this->assertInstanceOf(FileServeCommand::class, $this->app->make(FileServeCommand::class));
		$this->assertInstanceOf(FileCreateValidator::class, $this->app->make(FileCreateValidator::class));
		$this->assertInstanceOf(FileServeValidator::class, $this->app->make(FileServeValidator::class));
		$this->assertInstanceOf(AdminThumbHandler::class, $this->app->make(AdminThumbHandler::class));
		$this->assertInstanceOf(AdminImageHandler::class, $this->app->make(AdminImageHandler::class));
	}
}
