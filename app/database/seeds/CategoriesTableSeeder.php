<?php

class CategoriesTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		// DB::table('categories')->truncate();

		$categories = array(
			['category_name' => 'Ruby',
			 'category_description' => 'Ruby is a programming language created by.'],
        	['category_name' => 'Python',
        	'category_description' => 'Python is a programming language created by.'],
        	['category_name' => 'Rails',
        	'category_description' => 'Rails is a programming language framework created by.'],
        	['category_name' => 'Django',
        	'category_description' => 'Django is a programming language framework created by.']
		);

		// Uncomment the below to run the seeder
		 DB::table('categories')->insert($categories);
	}

}
