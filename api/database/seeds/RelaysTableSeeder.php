<?php

use Illuminate\Database\Seeder;

class RelaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i=0; $i<=16; $i++){
            DB::table('relays')->insert([
                'number' => $i,
                'name' => 'Relay '. $i, 
                'is_output' => 1,
                'mode' => 'auto'
            ]);
        }
    }
}
