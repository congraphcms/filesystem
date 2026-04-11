<?php

use Congraph\Contracts\Filesystem\FileRepositoryContract;

class FakeFileRepository implements FileRepositoryContract
{
	public array $calls = [];
	public mixed $createResult = null;
	public mixed $updateResult = null;
	public mixed $deleteResult = null;
	public mixed $fetchResult = null;
	public mixed $getResult = null;

	public function create($model)
	{
		$this->calls[] = ['create', [$model]];
		return $this->createResult;
	}

	public function update($id, $model)
	{
		$this->calls[] = ['update', [$id, $model]];
		return $this->updateResult;
	}

	public function delete($id)
	{
		$this->calls[] = ['delete', [$id]];
		return $this->deleteResult;
	}

	public function fetch($id, $include = [])
	{
		$this->calls[] = ['fetch', [$id, $include]];
		return $this->fetchResult;
	}

	public function get($filter = [], $offset = 0, $limit = 0, $sort = [], $include = [])
	{
		$this->calls[] = ['get', [$filter, $offset, $limit, $sort, $include]];
		return $this->getResult;
	}
}
