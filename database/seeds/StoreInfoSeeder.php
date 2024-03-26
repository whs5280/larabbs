<?php

use Illuminate\Database\Seeder;

/**
 * `php artisan db:seed --class=StoreInfoSeeder`
 */
class StoreInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        \App\Models\StoreInfo::factory()->count(50)->create([
            'name'     => sprintf('%s(%s)', $faker->title, $faker->city),
            'address'  => $faker->address,
            'location' => sprintf('%s,%s', $faker->latitude, $faker->longitude),
        ]);
    }
}
