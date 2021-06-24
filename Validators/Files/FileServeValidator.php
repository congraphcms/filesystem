<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Validators\Files;

use Congraph\Core\Exceptions\BadRequestException;
use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Validation\Validator;
use Congraph\Filesystem\Commands\Files\FileServeCommand;
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
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileServeValidator
{

	/**
	 * Validate FileServeCommand
	 * 
	 * @param \Congraph\Filesystem\Commands\Files\FileServeCommand $command
	 * 
	 * @todo  Create custom validation for all db related checks (DO THIS FOR ALL VALIDATORS)
	 * @todo  Check all db rules | make validators on repositories
	 * 
	 * @return void
	 */
	public function validate(FileServeCommand $command)
	{
		$imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
		$command->id = str_replace('..', '', strval($command->id));
		
		if( empty($command->id) )
		{
			$e = new BadRequestException();
			$e->setErrorKey('url');
			$e->addErrors('You need to specify url of file.');

			throw $e;
		}

		if( ! Storage::has($command->id) )
		{
			$e = new NotFoundException();
			$e->setErrorKey('url');
			$e->addErrors('File not found.');

			throw $e;
		}

		$command->params['version'] = 
			(empty($command->params) || empty($command->params['version']))
			? null
			: strval($command->params['version']);

		if( ! is_null($command->params['version'])
			&& ! in_array(Storage::getMimetype($command->id), $imageMimeTypes) )
		{
			$e = new BadRequestException();
			$e->setErrorKey('version');
			$e->addErrors('Only images can have versions.');

			throw $e;
		}

		if( ! is_null($command->params['version'])
			&& ! array_key_exists($command->params['version'], Config::get('cb.files.image_versions')) )
		{
			$e = new BadRequestException();
			$e->setErrorKey('version');
			$e->addErrors('This version is not supported.');

			throw $e;
		}

	}
}