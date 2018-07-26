<?php
/*
 * This file is part of the cookbook/filesystem package.
 *
 * (c) Nikola Plavšić <nikolaplavsic@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * CreateFileTables migration
 *
 * Creates tables for files in database needed for this package
 *
 * @uses   		Illuminate\Database\Schema\Blueprint
 * @uses   		Illuminate\Database\Migrations\Migration
 *
 * @author  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @copyright  	Nikola Plavšić <nikolaplavsic@gmail.com>
 * @package 	cookbook/filesystem
 * @since 		0.1.0-alpha
 * @version  	0.1.0-alpha
 */
class CreateFileTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1.0 Create wp_bicc_images table
        // ---------------------------------------------------

        Schema::create('wp_bicc_images', function ($table) {

            // primary key, autoincrement
            $table->increments('id');

            // unique relative url to file
            $table->string('url', 1000);

            // filename
            $table->string('filename', 500);

            // file extension
            $table->string('extension', 50);

            // file mime type
            $table->string('type', 50);

            // private or public status
            $table->string('status', 50);

            // file size
            $table->integer('size');

            // user owner id
            $table->integer('user_id');

            // how many times has been this image used
            $table->integer('used');
            
            // created_at and updated_at timestamps
            $table->timestamp('date_created');
            $table->timestamp('date_modified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        // 1.0 Drop table wp_bicc_images

        Schema::drop('wp_bicc_images');
    }
}
