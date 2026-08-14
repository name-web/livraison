<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backend\Hub;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hubs = [
            [
                'name'            =>'Abobo',
                'phone'           =>'+225 07 00 00 00 01',
                'address'         =>'Abidjan, Abobo',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Adjamé',
                'phone'           =>'+225 07 00 00 00 02',
                'address'         =>'Abidjan, Adjamé',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Anyama',
                'phone'           =>'+225 07 00 00 00 03',
                'address'         =>'Abidjan, Anyama',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Attécoubé',
                'phone'           =>'+225 07 00 00 00 04',
                'address'         =>'Abidjan, Attécoubé',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Bingerville',
                'phone'           =>'+225 07 00 00 00 05',
                'address'         =>'Abidjan, Bingerville',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Cocody',
                'phone'           =>'+225 07 00 00 00 06',
                'address'         =>'Abidjan, Cocody',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Koumassi',
                'phone'           =>'+225 07 00 00 00 07',
                'address'         =>'Abidjan, Koumassi',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Marcory',
                'phone'           =>'+225 07 00 00 00 08',
                'address'         =>'Abidjan, Marcory',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Plateau',
                'phone'           =>'+225 07 00 00 00 09',
                'address'         =>'Abidjan, Plateau',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Port-Bouët',
                'phone'           =>'+225 07 00 00 00 10',
                'address'         =>'Abidjan, Port-Bouët',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Songon',
                'phone'           =>'+225 07 00 00 00 11',
                'address'         =>'Abidjan, Songon',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Treichville',
                'phone'           =>'+225 07 00 00 00 12',
                'address'         =>'Abidjan, Treichville',
                'current_balance' => '00'
            ],
            [
                'name'            =>'Yopougon',
                'phone'           =>'+225 07 00 00 00 13',
                'address'         =>'Abidjan, Yopougon',
                'current_balance' => '00'
            ],
        ];

        for($n = 0; $n < sizeof($hubs); $n++)
        {
            $hub                  = new Hub();
            $hub->name            = $hubs[$n]['name'];
            $hub->phone           = $hubs[$n]['phone'];
            $hub->address         = $hubs[$n]['address'];
            $hub->current_balance = $hubs[$n]['current_balance'];
            $hub->save();
        }
    }
}
