<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Congraph\Filesystem\Repositories;

use Congraph\Contracts\Filesystem\FileRepositoryContract;
use Congraph\Core\Exceptions\Exception;
use Congraph\Core\Exceptions\NotFoundException;
use Congraph\Core\Facades\Trunk;
use Congraph\Core\Repositories\AbstractRepository;
use Congraph\Core\Repositories\Collection;
use Congraph\Core\Repositories\Model;
use Congraph\Core\Repositories\UsesCache;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

/**
 * FileRepository class
 * 
 * Repository for file database queries
 * 
 * @uses   		Illuminate\Database\Connection
 * @uses   		Congraph\Core\Repository\AbstractRepository
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
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
		$model['created_at'] = $model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

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
	 * @throws Congraph\Core\Exceptions\NotFoundException
	 */
	protected function _update($id, $model)
	{

		// find file with that ID
		$file = $this->fetch($id);

		if( ! $file )
		{
			throw new NotFoundException(['There is no file with that ID.']);
		}

		$model['updated_at'] = Carbon::now('UTC')->toDateTimeString();

		$this->db->table('files')->where('id', '=', $id)->update($model);

		Trunk::forgetType('file');
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
	 * @throws Congraph\Core\Exceptions\NotFoundException
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
		Trunk::forgetType('file');
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
	protected function _fetch($id, $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;
		
		if(Trunk::has($params, 'file'))
		{
			$file = Trunk::get($id, 'file');
			$file->clearIncluded();
			$file->load($include);
			$meta = ['id' => $id, 'include' => $include];
			$file->setMeta($meta);
			return $file;
		}

		if(!is_numeric($id)) {
			return $this->fetchByUrl($id, $include);
		}

		$file = $this->db->table('files')->find($id);
		
		if( ! $file )
		{
			throw new NotFoundException(['There is no file with that ID.']);
		}

		$file->type = 'file';

		$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
		$file->created_at = Carbon::parse($file->created_at)->tz($timezone);
		$file->updated_at = Carbon::parse($file->updated_at)->tz($timezone);

		$result = new Model($file);

		$result->setParams($params);
		$meta = ['id' => $id, 'include' => $include];
		$result->setMeta($meta);
		$result->load($include);
		return $result;
	}

	/**
	 * Get file by URL
	 * 
	 * @param int $url - URL of file to be fetched
	 * 
	 * @return array
	 */
	protected function fetchByUrl( $url, $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;

		if(Trunk::has($params, 'file')) {
			$file = Trunk::get( $url, 'file');
			$file->clearIncluded();
			$file->load($include);
			$meta = ['url' => $url, 'include' => $include];
			$file->setMeta($meta);
			return $file;
		}

		if( substr( $url, 0, 1 ) === '/' ) {
            $url2 = substr($url, 1, strlen($url));
        } else {
            $url2 = '/' . $url;
        }

		$file = $this->db->table('files')->where('url', '=', $url)->first();

		if(!$file) {
            $file  = $this->db->table( 'files' )->where('url', '=', $url2)->first();
        }

		if(!$file) {
			throw new NotFoundException(['There is no file with that URL.']);
		}

		$file->type = 'file';
		// $file->api_url = route('BIC.file.serve', ['file' => trim($file->url, '/')]);

		// if ($file->filetype == 'image') {
		// 	$versions = Config::get('cb.files.image_versions');
		// 	if (count($versions) > 0) {
		// 		$file->versions = [];
		// 	}
		// 	foreach ($versions as $version => $handler) {
		// 		$file->versions[$version] = $file->api_url . '?v=' . $version;
		// 	}
        // }

		$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
		$file->created_at = Carbon::parse($file->created_at)->tz($timezone);
		$file->updated_at = Carbon::parse($file->updated_at)->tz($timezone);

		$result = new Model($file);
		
		$result->setParams($params);
		$meta = ['url' => $url, 'include' => $include];
		$result->setMeta($meta);
		$result->load($include);
		return $result;
	}

	/**
	 * Get files
	 * 
	 * @return array
	 */
	protected function _get($filter = [], $offset = 0, $limit = 0, $sort = [], $include = [])
	{
		$params = func_get_args();
		$params['function'] = __METHOD__;

		if(Trunk::has($params, 'file'))
		{
			$files = Trunk::get($params, 'file');
			$files->clearIncluded();
			$files->load($include);
			$meta = [
				'include' => $include
			];
			$files->setMeta($meta);
			return $files;
		}

		$query = $this->db->table('files');

		$query = $this->parseFilters($query, $filter);

		$total = $query->count();

		$query = $this->parsePaging($query, $offset, $limit);

		$query = $this->parseSorting($query, $sort);
		
		$files = $query->get();

		$files = $files->toArray();

		if( ! $files )
		{
			$files = [];
		}
		
		foreach ($files as &$file) {
			$file->type = 'file';
			$timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
			$file->created_at = Carbon::parse($file->created_at)->tz($timezone);
			$file->updated_at = Carbon::parse($file->updated_at)->tz($timezone);
		}

		$result = new Collection($files);
		
		$result->setParams($params);

		$meta = [
			'count' => count($files), 
			'offset' => $offset, 
			'limit' => $limit, 
			'total' => $total, 
			'filter' => $filter, 
			'sort' => $sort, 
			'include' => $include
		];
		$result->setMeta($meta);

		$result->load($include);
		
		return $result;
	}

}