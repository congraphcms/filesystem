<?php

require_once __DIR__ . '/../../Fixtures/Commands/FakeFileRepository.php';
require_once __DIR__ . '/../../Fixtures/DatabaseTestCase.php';

use Congraph\Filesystem\Commands\Files\FileDeleteCommand;
use Congraph\Filesystem\Commands\Files\FileFetchCommand;
use Congraph\Filesystem\Commands\Files\FileGetCommand;
use Congraph\Filesystem\Commands\Files\FileUpdateCommand;
use Illuminate\Support\Facades\Storage;

final class FileCommandsTest extends DatabaseTestCase
{
	public function testUpdateCommandDelegatesToRepository(): void
	{
		$repository = new FakeFileRepository();
		$repository->updateResult = (object) ['id' => 1, 'caption' => 'Updated'];

		$command = new FileUpdateCommand($repository);
		$command->setId(1);
		$command->setParams(['caption' => 'Updated']);

		$result = $command->handle();

		$this->assertSame($repository->updateResult, $result);
		$this->assertSame([['update', [1, ['caption' => 'Updated']]]], $repository->calls);
	}

	public function testDeleteCommandReturnsDeletedId(): void
	{
		Storage::put('files/5.jpg', 'content');

		$repository = new FakeFileRepository();
		$repository->deleteResult = (object) ['id' => 5, 'url' => 'files/5.jpg'];

		$command = new FileDeleteCommand($repository);
		$command->setId(5);

		$result = $command->handle();

		$this->assertSame(5, $result);
		$this->assertSame([['delete', [5]]], $repository->calls);
		$this->assertFalse(Storage::exists('files/5.jpg'));
	}

	public function testFetchCommandPassesIncludeToRepository(): void
	{
		$repository = new FakeFileRepository();
		$repository->fetchResult = (object) ['id' => 1];

		$command = new FileFetchCommand($repository);
		$command->setId(1);
		$command->setParams(['include' => ['owner']]);

		$result = $command->handle();

		$this->assertSame($repository->fetchResult, $result);
		$this->assertSame([['fetch', [1, ['owner']]]], $repository->calls);
	}

	public function testGetCommandPassesDefaultsToRepository(): void
	{
		$repository = new FakeFileRepository();
		$repository->getResult = [(object) ['id' => 1]];

		$command = new FileGetCommand($repository);
		$command->setParams([
			'filter' => ['extension' => 'jpg'],
			'offset' => 5,
			'limit' => 10,
			'sort' => ['-created_at'],
			'include' => ['owner'],
		]);

		$result = $command->handle();

		$this->assertSame($repository->getResult, $result);
		$this->assertSame([
			['get', [['extension' => 'jpg'], 5, 10, ['-created_at'], ['owner']]],
		], $repository->calls);
	}
}
