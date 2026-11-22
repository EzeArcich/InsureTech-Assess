<?php

namespace Database\Seeders;

use Database\Factories\ClaimFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class UpdateClaimAccountsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            DB::table('claims')->select('id')->orderBy('id')->chunkById(100, function ($claims): void {
                foreach ($claims as $claim) {
                    DB::table('claims')->where('id', $claim->id)->update([
                        'insured_account' => Arr::random(ClaimFactory::insuredAccounts()),
                    ]);
                }
            });
        });
    }
}
