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
 * FileCreateValidator class
 * 
 * Validating command for creating file
 * 
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileCreateValidator extends Validator
{


	/**
	 * Set of rules for validating file
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * Create new FileCreateValidator
	 * 
	 * @return void
	 */
	public function __construct()
	{


		$this->rules = [
			'url'					=> 'required|unique:files,url',
			'name'					=> 'required|min:3|max:500',
			'extension'				=> 'required|min:1|max:50',
			'mime_type'				=> 'required|min:1|max:250',
			'size'					=> 'required|integer',
			'caption'				=> '',
			'description'			=> '',
			'file'					=> ''
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

		$this->validateFile($command->params);

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}

		$this->setFileInfoParams($command->params);



		$this->validateParams($command->params, $this->rules, true);

		if( $this->exception->hasErrors() )
		{
			throw $this->exception;
		}
	}


	/**
	 * Validate File
	 * 
	 * @param array $params
	 * 
	 * @return void
	 */
	public function validateFile(array $params)
	{
		if( empty($params['file']) || ! $params['file'] instanceof UploadedFile )
		{
			$this->exception->addErrors(['You need to upload a file.']);

			return;
		}

		$file = $params['file'];

		if( ! $file->isValid() )
		{
			$this->exception->addErrors(['There was an error during upload of the file.']);
		}

	}

	/**
	 * Get file info and add data to command params
	 * 
	 * @param array &$params
	 * 
	 * @return void
	 */
	public function setFileInfoParams(array &$params)
	{
		$file = $params['file'];

		$uploadsUrl = Config::get('cb.files.uploads_path');

		$url = FileHelper::normalizeUrl($uploadsUrl . '/' . $file->getClientOriginalName());

		$url = FileHelper::uniqueFilename($url);
		// set url
		$params['url'] = $url;

		// set name
		$name = FileHelper::getFileName($url);
		$params['name'] = $name;

		// set extension
		$params['extension'] = $file->getExtension();
		if(empty($params['extension'])) {
			$params['extension'] = $file->guessExtension();
		}

		// set mime type
		$params['mime_type'] = $file->getMimeType();

		// set size
		$params['size'] = $file->getSize();

	}
}