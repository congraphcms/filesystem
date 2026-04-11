<?php

require_once __DIR__ . '/../../Fixtures/DatabaseTestCase.php';

use Congraph\Filesystem\Handlers\Images\AdminImageHandler;
use Congraph\Filesystem\Handlers\Images\AdminThumbHandler;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

final class ImageHandlersTest extends DatabaseTestCase
{
	public function testAdminThumbHandlerCreatesExpectedThumb(): void
	{
		$handler = $this->app->make(AdminThumbHandler::class);
		$result = $handler->handle(Storage::get('files/1.jpg'));

		$expected = Image::read(realpath(__DIR__ . '/../../storage/files/1.jpg'));
		$expected = $expected->coverDown(50, 50);

		$this->assertSame((string) $expected->encode(), $result);
	}

	public function testAdminImageHandlerCreatesExpectedImage(): void
	{
		$handler = $this->app->make(AdminImageHandler::class);
		$result = $handler->handle(Storage::get('files/1.jpg'));

		$expected = Image::read(realpath(__DIR__ . '/../../storage/files/1.jpg'));
		$expected = $expected->coverDown(300, 200);

		$this->assertSame((string) $expected->encode(), $result);
	}
}
