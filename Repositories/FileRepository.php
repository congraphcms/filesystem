<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Repositories;

use Cookbook\Contracts\Filesystem\FileRepositoryContract;
use Cookbook\Core\Exceptions\Exception;
use Cookbook\Core\Exceptions\NotFoundException;
use Cookbook\Core\Repositories\AbstractRepository;
use Cookbook\Core\Repositories\UsesCache;
use Illuminate\Database\Connection;


/**
 * FileRepository class
 * 
 * Repository for file database queries
 * 
 * @uses   		Illuminate\Database\Connection
 * @uses   		Cookbook\Core\Repository\AbstractRepository
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class FileRepository extends AbstractRepository implements FileRepositoryContract//, UsesCache
{

// ----------------------------------------------------------------------------------------------
// PARAMS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Create new FileRepository
	 * 
	 * @param Illuminate\Database\Connection $db
	 * 
	 * @return void
	 */
	public function __construct(Connection $db)
	{
		$this->type = 'files';

		// AbstractRepository constructor
		parent::__construct($db);
	}

// ----------------------------------------------------------------------------------------------
// CRUD
// ----------------------------------------------------------------------------------------------
// 
// 
// 


	/**
	 * Create new file
	 * 
	 * @param array $model - file params (url, name, size...)
	 * 
	 * @return mixed
	 * 
	 * @throws Exception
	 */
	protected function _create($model)
	{
		unset($model['file']);
		$model['created_at'] = $model['updated_at'] = date('Y-m-d H:i:s');

		// insert file in database
		$fileId = $this->db->table('files')->insertGetId($model);

		// get file
		$file = $this->fetch($fileId);

		if(!$file)
		{
			throw new \Exception('Failed to insert file');
		}

		// and return newly created file
		return $file;
		
	}

	/**
	 * Update file
	 * 
	 * @param array $model - file params (url, name, size...)
	 *
	 * @return mixed
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _update($id, $model)
	{

		// find file with that ID
		$file = $this->fetch($id);

		if( ! $file )
		{
			throw new NotFoundException(['There is no file with that ID.']);
		}

		$model['updated_at'] = date('Y-m-d H:i:s');

		$this->db->table('files')->where('id', '=', $id)->update($model);

		$file = $this->fetch($id);

		// and return file
		return $file;
	}

	/**
	 * Delete file from database
	 * 
	 * @param integer $id - ID of file that will be deleted
	 * 
	 * @return boolean
	 * 
	 * @throws Cookbook\Core\Exceptions\NotFoundException
	 */
	protected function _delete($id)
	{
		// get the file
		$file = $this->fetch($id);
		if(!$file)
		{
			throw new NotFoundException(['There is no file with that ID.']);
		}
		
		// delete the file
		$this->db->table('files')->where('id', '=', $file->id)->delete();

		return $file;
	}
	


// ----------------------------------------------------------------------------------------------
// GETTERS
// ----------------------------------------------------------------------------------------------
// 
// 
// 

	/**
	 * Get file by ID
	 * 
	 * @param int $id - ID of file to be fetched
	 * 
	 * @return array
	 */
	protected function _fetch($id)
	{
		$file = $this->db->table('files')->find($id);
		
		if( ! $file )
		{
			throw new NotFoundException(['There is no file with that ID.']);
		}

		$file->type = 'file';
		
		return $file;
	}

	/**
	 * Get files
	 * 
	 * @return array
	 */
	protected function _get($filter = [], $offset = 0, $limit = 0, $sort = [])
	{
		$query = $this->db->table('files');

		$query = $this->parseFilters($query, $filter);

		$query = $this->parsePaging($query, $offset, $limit);

		$query = $this->parseSorting($query, $sort);
		
		$files = $query->get();

		if( ! $files )
		{
			return [];	
		}
		
		foreach ($files as &$file) {
			$file->type = 'file';
		}
		
		return $files;
	}

}