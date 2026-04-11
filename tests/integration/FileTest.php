<?php

require_once __DIR__ . '/../Fixtures/DatabaseTestCase.php';

use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Core\Repositories\Collection;
use Congraph\Core\Repositories\Model;
use Congraph\Filesystem\Commands\Files\FileCreateCommand;
use Congraph\Filesystem\Commands\Files\FileDeleteCommand;
use Congraph\Filesystem\Commands\Files\FileFetchCommand;
use Congraph\Filesystem\Commands\Files\FileGetCommand;
use Congraph\Filesystem\Commands\Files\FileServeCommand;
use Congraph\Filesystem\Commands\Files\FileUpdateCommand;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileTest extends DatabaseTestCase
{
	protected function seeders(): array
	{
		return [
			Database\Seeders\TestDbSeeder::class,
		];
	}

	protected function cleanupSeeders(): array
	{
		return [
			Database\Seeders\ClearDB::class,
		];
	}

	public function testCreateFile(): void
	{
		$command = $this->app->make(FileCreateCommand::class);
		$command->setParams([
			'file' => new UploadedFile(
				realpath(__DIR__ . '/../storage/uploads/test.jpg'),
				'test.jpg',
				'image/jpeg',
				null,
				true
			),
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertTrue(Storage::exists('files/test.jpg'));
	}

	public function testCreateFileThrowsValidationException(): void
	{
		$this->expectException(ValidationException::class);

		$command = $this->app->make(FileCreateCommand::class);
		$command->setParams([
			'file' => new UploadedFile(
				realpath(__DIR__ . '/../storage/uploads/test.jpg'),
				'test.jpg',
				'image/jpeg'
			),
		]);

		$this->dispatch($command);
	}

	public function testUpdateFile(): void
	{
		$command = $this->app->make(FileUpdateCommand::class);
		$command->setId(1);
		$command->setParams([
			'caption' => 'test file',
			'description' => 'test description',
		]);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame('test file', $result->caption);
		$this->assertSame('test description', $result->description);
	}

	public function testDeleteFile(): void
	{
		$command = $this->app->make(FileDeleteCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertSame(1, $result);
		$this->assertFalse(Storage::exists('files/1.jpg'));
	}

	public function testDeleteFileThrowsWhenMissing(): void
	{
		$this->expectException(NotFoundException::class);

		$command = $this->app->make(FileDeleteCommand::class);
		$command->setId(133);

		$this->dispatch($command);
	}

	public function testFetchFileById(): void
	{
		$command = $this->app->make(FileFetchCommand::class);
		$command->setId(1);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame('files/1.jpg', $result->url);
	}

	public function testFetchFileByUrl(): void
	{
		$command = $this->app->make(FileFetchCommand::class);
		$command->setId('files/1.jpg');

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Model::class, $result);
		$this->assertSame(1, $result->id);
		$this->assertSame('files/1.jpg', $result->url);
	}

	public function testGetFiles(): void
	{
		$command = $this->app->make(FileGetCommand::class);

		$result = $this->dispatch($command);

		$this->assertInstanceOf(Collection::class, $result);
		$this->assertCount(1, $result);
	}

	public function testServeOriginalFile(): void
	{
		$command = $this->app->make(FileServeCommand::class);
		$command->setId('files/1.jpg');

		$result = $this->dispatch($command);

		$this->assertSame(Storage::get('files/1.jpg'), $result['content']);
		$this->assertSame('image/jpeg', $result['mime_type']);
	}

	public function testServeVersionedImage(): void
	{
		$command = $this->app->make(FileServeCommand::class);
		$command->setId('files/1.jpg');
		$command->setParams(['version' => 'admin_thumb']);

		$result = $this->dispatch($command);

		$thumb = Image::read(realpath(__DIR__ . '/../storage/files/1.jpg'));
		$thumb = $thumb->coverDown(50, 50);
		$this->assertSame((string) $thumb->encode(), $result['content']);
	}
}
