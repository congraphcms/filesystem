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

use Congraph\Core\Bus\RepositoryCommand;
use Congraph\Core\Validation\Validator;
use Congraph\Core\Helpers\FileHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\Config;


/**
 * FileUpdateValidator class
 * 
 * Validating command for updating file
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileUpdateValidator extends Validator
{


	/**
	 * Set of rules for validating file
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * Create new FileUpdateValidator
	 * 
	 * @return void
	 */
	public function __construct()
	{


		$this->rules = [
			'caption'				=> '',
			'description'			=> ''
		];

		parent::__construct();

		$this->exception->setErrorKey('files');
	}


	/**
	 * Validate RepositoryCommand
	 * 
	 * @param Congraph\Core\Bus\RepositoryCommand $command
	 * 
	 * @todo  Create custom validation for all db related checks (DO THIS FOR ALL VALIDATORS)
	 * @todo  Check all db rules | make validators on repositories
	 * 
	 * @return void
	 */
	public function validate(RepositoryCommand $command)
	{
		$this->validateParams($command->params, $this->rules, true);

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}


	// /**
	//  * Validate File
	//  * 
	//  * @param array $params
	//  * 
	//  * @return void
	//  */
	// public function validateFile(array $params)
	// {
	// 	if( empty($params['file']) || ! $params['file'] instanceof UploadedFile )
	// 	{
	// 		$this->exception->addErrors(['You need to upload a file.']);

	// 		return;
	// 	}

	// 	$file = $params['file'];

	// 	if( ! $file->isValid() )
	// 	{
	// 		$this->exception->addErrors(['There was an error during upload of the file.']);
	// 	}

	// }

	// /**
	//  * Get file info and add data to command params
	//  * 
	//  * @param array &$params
	//  * 
	//  * @return void
	//  */
	// public function setFileInfoParams(array &$params)
	// {
	// 	$file = $params['file'];

	// 	$uploadsUrl = Config::get('congraph.uploads_path');

	// 	$url = FileHelper::normalizeUrl($uploadsUrl . '/' . $file->getFilename());

	// 	$url = FileHelper::uniqueFilename($url);
	// 	// set url
	// 	$params['url'] = $url;

	// 	// set name
	// 	$name = FileHelper::getFileName($url);
	// 	$params['name'] = $name;

	// 	// set extension
	// 	$params['extension'] = $file->getExtension();

	// 	// set mime type
	// 	$params['mime_type'] = $file->getMimeType();

	// 	// set size
	// 	$params['size'] = $file->getSize();


	// }
}