<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Validators\Files;

use Cookbook\Core\Exceptions\BadRequestException;
use Cookbook\Core\Exceptions\NotFoundException;
use Cookbook\Core\Validation\Validator;
use Cookbook\Filesystem\Commands\Files\FileServeCommand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

/**
 * FileServeValidator class
 * 
 * Validating command for serving file
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileServeValidator
{

	/**
	 * Validate FileServeCommand
	 * 
	 * @param \Cookbook\Filesystem\Commands\Files\FileServeCommand $command
	 * 
	 * @todo  Create custom validation for all db related checks (DO THIS FOR ALL VALIDATORS)
	 * @todo  Check all db rules | make validators on repositories
	 * 
	 * @return void
	 */
	public function validate(FileServeCommand $command)
	{
		$imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
		$command->url = str_replace('..', '', strval($command->url));
		if( empty($command->url) )
		{
			$e = new BadRequestException();
			$e->setErrorKey('url');
			$e->addErrors('You need to specify url of file.');

			throw $e;
		}

		if( ! Storage::has($command->url) )
		{
			$e = new NotFoundException();
			$e->setErrorKey('url');
			$e->addErrors('File not found.');
		}

		$command->version = (is_null($command->version))?$command->version:strval($command->version);

		if( ! is_null($command->version) && ! in_array(Storage::getMimetype($command->url), $imageMimeTypes) )
		{
			$e = new BadRequestException();
			$e->setErrorKey('version');
			$e->addErrors('Only images can have versions.');

			throw $e;
		}

		if( ! is_null($command->version) && ! array_key_exists($command->version, Config::get('cb.files.image_versions')) )
		{
			$e = new BadRequestException();
			$e->setErrorKey('version');
			$e->addErrors('This version is not supported.');

			throw $e;
		}

	}
}