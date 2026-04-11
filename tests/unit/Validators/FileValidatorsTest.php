<?php

require_once __DIR__ . '/../../Fixtures/Validators/ValidatorTestCase.php';

use Congraph\Core\Exceptions\BadRequestException;
use Congraph\Core\Exceptions\ValidationException;
use Congraph\Filesystem\Commands\Files\FileCreateCommand;
use Congraph\Filesystem\Commands\Files\FileGetCommand;
use Congraph\Filesystem\Commands\Files\FileServeCommand;
use Congraph\Filesystem\Repositories\FileRepository;
use Congraph\Filesystem\Validators\Files\FileCreateValidator;
use Congraph\Filesystem\Validators\Files\FileGetValidator;
use Congraph\Filesystem\Validators\Files\FileServeValidator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileValidatorsTest extends ValidatorTestCase
{
	protected function seeders(): array
	{
		return [
			Database\Seeders\TestDbSeeder::class,
		];
	}

	public function testFileCreateValidatorEnrichesParamsFromUploadedFile(): void
	{
		$validator = new FileCreateValidator();
		$command = new FileCreateCommand($this->app->make(FileRepository::class));
		$command->setParams([
			'file' => new UploadedFile(
				realpath(__DIR__ . '/../../storage/uploads/test.jpg'),
				'test.jpg',
				'image/jpeg',
				null,
				true
			),
		]);

		$validator->validate($command);

		$this->assertSame('files/test.jpg', $command->params['url']);
		$this->assertSame('test.jpg', $command->params['name']);
		$this->assertSame('jpg', $command->params['extension']);
		$this->assertSame('image/jpeg', $command->params['mime_type']);
	}

	public function testFileCreateValidatorThrowsForInvalidUpload(): void
	{
		$this->expectException(ValidationException::class);

		$validator = new FileCreateValidator();
		$command = new FileCreateCommand($this->app->make(FileRepository::class));
		$command->setParams([
			'file' => new UploadedFile(
				realpath(__DIR__ . '/../../storage/uploads/test.jpg'),
				'test.jpg',
				'image/jpeg'
			),
		]);

		$validator->validate($command);
	}

	public function testFileGetValidatorNormalizesPagingAndInFilters(): void
	{
		$validator = new FileGetValidator();
		$command = new FileGetCommand($this->app->make(FileRepository::class));
		$command->setParams([
			'filter' => [
				'extension' => [
					'in' => 'jpg,png',
				],
			],
			'offset' => '5',
			'limit' => '10',
			'sort' => '-created_at',
		]);

		$validator->validate($command);

		$this->assertSame(['jpg', 'png'], $command->params['filter']['extension']['in']);
		$this->assertSame(5, $command->params['offset']);
		$this->assertSame(10, $command->params['limit']);
		$this->assertSame(['-created_at'], $command->params['sort']);
	}

	public function testFileGetValidatorRejectsInvalidFilter(): void
	{
		$this->expectException(BadRequestException::class);

		$validator = new FileGetValidator();
		$command = new FileGetCommand($this->app->make(FileRepository::class));
		$command->setParams([
			'filter' => ['invalid' => 'value'],
		]);

		$validator->validate($command);
	}

	public function testFileServeValidatorRejectsUnsupportedVersionForNonImage(): void
	{
		Storage::put('files/test.txt', 'hello');

		$validator = new FileServeValidator();
		$command = new FileServeCommand($this->app, $this->app->make(FileRepository::class));
		$command->setId('files/test.txt');
		$command->setParams(['version' => 'admin_thumb']);

		$this->expectException(BadRequestException::class);

		$validator->validate($command);
	}
}
