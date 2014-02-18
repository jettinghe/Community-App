<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddParentCategoryUriToParentcategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('parentcategories', function(Blueprint $table) {
			$table->string('parent_category_uri');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('parentcategories', function(Blueprint $table) {
			$table->dropColumn('parent_category_uri');
		});
	}

}
