<?php
/*
 * This file is part of the congraph/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Database\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
/**
 * TestDbSeeder
 * 
 * Populates DB with data for testing
 * 
 * @uses   		Illuminate\Database\Schema\Blueprint
 * @uses   		Illuminate\Database\Seeder
 * 
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	congraph/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class TestDbSeeder extends Seeder {

	public function run()
	{
		DB::table('files')->truncate();

		DB::table('files')->insert([
			[
				'url' => 'files/1.jpg',
				'name' => '1.jpg',
				'extension' => 'jpg',
				'mime_type' => 'image/jpeg',
				'size' => 6,
				'caption' => 'test image',
				'description' => 'test description',
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]
		]);

	
	}

}