<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Game;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $count = User::count();

        foreach (range(0,10) as $number) {

            $first_id = $faker->numberBetween(1, $count);
            $second_id = $faker->numberBetween(1, $count);

            while($first_id === $second_id) {
                $second_id = $faker->numberBetween(1, $count);
            }

            $game = new Game();
            $game->first_user_id = $first_id;
            $game->second_user_id = $second_id;
            $game->date_started = $faker->time('Y-m-d H:i:s');
            $game->status = $faker->numberBetween(0, 2);
            $game->save();
        }
    }
}
