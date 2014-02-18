<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentnotifiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commentnotifies', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('post_id');
			$table->integer('comment_id');
			$table->boolean('is_read');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('commentnotifies');
	}

}
