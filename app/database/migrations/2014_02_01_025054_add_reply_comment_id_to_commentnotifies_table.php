<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddReplyCommentIdToCommentnotifiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commentnotifies', function(Blueprint $table) {
			$table->integer('reply_comment_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('commentnotifies', function(Blueprint $table) {
			$table->dropColumn('reply_comment_id');
		});
	}

}
