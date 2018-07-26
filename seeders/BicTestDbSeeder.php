<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cookbook\Filesystem\Seeders;

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
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class BicTestDbSeeder extends Seeder
{
    public function run()
    {
        DB::table('wp_bicc_images')->truncate();

        DB::table('wp_bicc_images')->insert([
            [
                'url' => 'files/1.jpg',
                'filename' => '1.jpg',
                'extension' => 'jpg',
                'type' => 'image',
                'size' => 6,
                'user_id' => 8,
                'used' => 0,
                'status' => 'private',
                'date_created' => date("Y-m-d H:i:s"),
                'date_modified' => date("Y-m-d H:i:s")
            ]
        ]);
    }
}
