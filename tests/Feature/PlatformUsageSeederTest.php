<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\InspectionPayment;
use App\Models\Inspector;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PlatformUsageSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PlatformUsageSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_demo_populates_tomorrow_routes_repairs_and_unpaid_inspections(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 9)->startOfDay());
        $this->seed(DatabaseSeeder::class);
        $count = Claim::count();
        $this->seed(PlatformUsageSeeder::class);

        $this->assertSame($count, Claim::count());
        $this->assertSame(8, Claim::where('claim_number', 'like', 'DEMO-USO-COLA-%')->where('status', 'Coordinado')->whereDate('inspection_date', '2026-11-10')->count());
        foreach (Inspector::whereHas('claims', fn ($q) => $q->where('claim_number', 'like', 'DEMO-USO-%'))->get() as $inspector) {
            $this->assertSame(2, Claim::where('inspector_id', $inspector->id)->where('claim_number', 'like', 'DEMO-USO-%')->where('status', 'Peritando')->whereDate('inspection_date', '2026-11-10')->count());
        }
        $this->assertGreaterThan(0, InspectionPayment::whereNull('paid_at')->count());
        $this->assertGreaterThan(0, Claim::where('claim_number', 'like', 'DEMO-USO-%')->where('paint_panels', '>', 0)->whereHas('catalogParts')->count());
    }
}
