<?php

namespace Tests\Feature;

use App\Models\Claim;
use Database\Factories\ClaimFactory;
use Database\Seeders\UpdateClaimAccountsSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UpdateClaimAccountsSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_it_updates_accounts_without_creating_records_or_changing_other_fields(): void
    {
        $first = Claim::factory()->create(['insured_account' => null]);
        $second = Claim::factory()->create(['insured_account' => 'Relato anterior']);
        $before = [$first->fresh()->getRawOriginal(), $second->fresh()->getRawOriginal()];

        $this->seed(UpdateClaimAccountsSeeder::class);

        $this->assertDatabaseCount('claims', 2);
        foreach ([$first->fresh(), $second->fresh()] as $index => $claim) {
            $this->assertContains($claim->insured_account, ClaimFactory::insuredAccounts());
            $after = $claim->getRawOriginal();
            unset($after['insured_account'], $before[$index]['insured_account']);
            $this->assertSame($before[$index], $after);
        }
    }
}
