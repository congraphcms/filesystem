<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Commands\Files;

use Cookbook\Core\Bus\Command;

/**
 * FileServeCommand class
 * 
 * Command for serving file contents
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileServeCommand extends Command
{
	/**
	 * File url
	 * 
	 * @var string
	 */
	public $url;

	/**
	 * File version
	 * 
	 * @var string
	 */
	public $version;

	/**
	 * Create new FileServeCommand
	 *
	 * @param string 	$url
	 * 
	 * @return void
	 */
	public function __construct($url, $version = null)
	{		
		$this->url = $url;
		$this->version = $version;
	}
}
