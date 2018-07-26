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
use Cookbook\Core\Facades\Trunk;
use Cookbook\Core\Repositories\AbstractRepository;
use Cookbook\Core\Repositories\Collection;
use Cookbook\Core\Repositories\Model;
use Cookbook\Core\Repositories\UsesCache;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

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
class FileRepository extends AbstractRepository implements FileRepositoryContract //, UsesCache
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

        $this->table = 'wp_bicc_images';

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
        $model['date_created'] = $model['date_modified'] = Carbon::now('UTC')->toDateTimeString();

        // insert file in database
        $fileId = $this->db->table($this->table)->insertGetId($model);

        // get file
        $file = $this->fetch($fileId);

        if (!$file) {
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

        if (! $file) {
            throw new NotFoundException(['There is no file with that ID.']);
        }

        $model['date_modified'] = Carbon::now('UTC')->toDateTimeString();

        $this->db->table($this->table)->where('id', '=', $id)->update($model);

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
     * @throws Cookbook\Core\Exceptions\NotFoundException
     */
    protected function _delete($id)
    {
        // get the file
        $file = $this->fetch($id);
        if (!$file) {
            throw new NotFoundException(['There is no file with that ID.']);
        }
        
        // delete the file
        $this->db->table($this->table)->where('id', '=', $file->id)->delete();
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
        
        if (Trunk::has($params, 'file')) {
            $file = Trunk::get($id, 'file');
            $file->clearIncluded();
            $file->load($include);
            $meta = ['id' => $id, 'include' => $include];
            $file->setMeta($meta);
            return $file;
        }

        $file = $this->db->table($this->table)->find($id);
        
        if (! $file) {
            throw new NotFoundException(['There is no file with that ID.']);
        }

        $file->type = 'file';

        $timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
        $file->date_created = Carbon::parse($file->date_created)->tz($timezone);
        $file->date_modified = Carbon::parse($file->date_modified)->tz($timezone);

        $result = new Model($file);
        
        $result->setParams($params);
        $meta = ['id' => $id, 'include' => $include];
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

        if (Trunk::has($params, 'file')) {
            $files = Trunk::get($params, 'file');
            $files->clearIncluded();
            $files->load($include);
            $meta = [
                'include' => $include
            ];
            $files->setMeta($meta);
            return $files;
        }

        $query = $this->db->table($this->table);

        $query = $this->parseFilters($query, $filter);

        $total = $query->count();

        $query = $this->parsePaging($query, $offset, $limit);

        $query = $this->parseSorting($query, $sort);
        
        $files = $query->get();

        if (! $files) {
            $files = [];
        }
        
        foreach ($files as &$file) {
            $file->type = 'file';
            $timezone = (Config::get('app.timezone'))?Config::get('app.timezone'):'UTC';
            $file->date_created = Carbon::parse($file->date_created)->tz($timezone);
            $file->date_modified = Carbon::parse($file->date_modified)->tz($timezone);
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
