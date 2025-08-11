<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            // Telkomsel Group
            "TSEL" => "Telkomsel",
            "SIMP" => "Simpati",
            "AS"   => "Kartu As",
            "LOOP" => "Loop",

            // Indosat Ooredoo Hutchison
            "ISAT" => "Indosat Ooredoo",
            "IM3"  => "IM3 Ooredoo",
            "MTRI" => "Tri (3)",
            "TRI"  => "Tri (3)",

            // XL Axiata Group
            "XL"   => "XL Axiata",
            "AXIS" => "AXIS",

            // Smartfren
            "SF"   => "Smartfren",

            // By.U (Telkomsel Digital)
            "BYU"  => "by.U",

            // Live.On (XL Digital)
            "LIVEON" => "Live.On",

            // Net1 (jarang dipakai)
            "NET1" => "Net1 Indonesia"
        ];


        foreach ($banks as $code => $name) {
            DB::table('providers')->insert([
                'name' => $name,
                'code' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
