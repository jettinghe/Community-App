<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
		$this->call('PostsTableSeeder');
		$this->call('UsersTableSeeder');
		$this->call('CategoriesTableSeeder');
		$this->call('CommentsTableSeeder');
		$this->call('CommentnotifiesTableSeeder');
		$this->call('PostvotenotifiesTableSeeder');
		$this->call('ParentcategoriesTableSeeder');
	}

}