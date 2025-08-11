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
            [
                'code' => 'TSEL',
                'name' => 'Telkomsel',
                'prefixes' => ['0811', '0812', '0813', '0821', '0822', '0823', '0851', '0852', '0853'],
            ],
            [
                'code' => 'ISAT',
                'name' => 'Indosat Ooredoo Hutchison',
                'prefixes' => ['0814', '0815', '0816', '0855', '0856', '0857', '0858'],
            ],
            [
                'code' => 'XL',
                'name' => 'XL Axiata',
                'prefixes' => ['0817', '0818', '0819', '0859', '0877', '0878'],
            ],
            [
                'code' => 'AXIS',
                'name' => 'AXIS',
                'prefixes' => ['0831', '0832', '0838'],
            ],
            [
                'code' => 'TRI',
                'name' => 'Tri (3)',
                'prefixes' => ['0895', '0896', '0897', '0898', '0899'],
            ],
            [
                'code' => 'SF',
                'name' => 'Smartfren',
                'prefixes' => ['0881', '0882', '0888', '0889'],
            ],
        ];

        foreach ($banks as $bank) {
            DB::table('providers')->insert([
                'name' => $bank['name'],
                'code' => $bank['code'],
                'prefixes' => json_encode($bank['prefixes']), // simpan prefix sebagai JSON string
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
