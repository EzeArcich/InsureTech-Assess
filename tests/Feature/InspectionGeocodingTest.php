<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InspectionGeocodingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_address_lookup_is_cached_and_confirming_a_result_creates_a_map_point(): void
    {
        $this->withoutVite();
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'presencial', 'inspection_date' => '2026-11-09']);
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/search*' => Http::response([
            ['lat' => '-34.6037', 'lon' => '-58.3816', 'display_name' => 'Corrientes 1000, Buenos Aires', 'address' => ['house_number' => '1000']],
        ])]);
        $url = '/route-sheets/'.$claim->id.'/geocode';
        $this->actingAs($inspector->user)->postJson($url, ['address' => 'Corrientes 1000, Buenos Aires'])->assertOk()->assertJsonPath('results.0.latitude', -34.6037);
        $this->postJson($url, ['address' => 'Corrientes 1000, Buenos Aires'])->assertOk();
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => $request['q'] === 'Corrientes 1000, Buenos Aires' && $request['countrycodes'] === 'ar'
            && str_contains($request->header('User-Agent')[0], 'InsureTechAssess') && ! isset($request['claim_id']));
        $this->assertNull($claim->fresh()->inspection_latitude);
        $this->put('/route-sheets/'.$claim->id.'/location', ['inspection_address' => 'Corrientes 1000, Buenos Aires', 'inspection_latitude' => '-34.6037', 'inspection_longitude' => '-58.3816'])->assertRedirect();
        $this->get('/route-sheets?date=2026-11-09')->assertOk()->assertViewHas('points', fn ($points): bool => $points->pluck('id')->all() === [$claim->id]);
    }

    public function test_foreign_and_remote_claims_never_trigger_external_address_searches(): void
    {
        $inspector = Inspector::factory()->create();
        $foreign = Claim::factory()->create(['status' => 'Peritando']);
        $remote = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'foto']);
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/search*' => Http::response([])]);
        $this->actingAs($inspector->user)->postJson('/route-sheets/'.$foreign->id.'/geocode', ['address' => 'Corrientes 1000'])->assertNotFound();
        $this->postJson('/route-sheets/'.$remote->id.'/geocode', ['address' => 'Corrientes 1000'])->assertUnprocessable();
        $this->actingAs(User::factory()->create())->postJson('/route-sheets/'.$foreign->id.'/geocode', ['address' => 'Corrientes 1000'])->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_global_rate_limit_applies_across_users_and_cache_misses(): void
    {
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'presencial']);
        Cache::put('inspection-geocode:last-request', microtime(true), 60);
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/search*' => Http::response([])]);
        $this->actingAs($inspector->user)->postJson('/route-sheets/'.$claim->id.'/geocode', ['address' => 'Corrientes 1000, Buenos Aires'])->assertStatus(429);
        Http::assertNothingSent();
    }

    public function test_no_results_or_provider_failure_do_not_save_invented_coordinates(): void
    {
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'presencial']);
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/search*' => Http::sequence()->push([])->push([], 503)]);
        $url = '/route-sheets/'.$claim->id.'/geocode';
        $this->actingAs($inspector->user)->postJson($url, ['address' => 'Domicilio inexistente'])->assertOk()->assertExactJson(['results' => []]);
        Cache::forget('inspection-geocode:last-request');
        $this->postJson($url, ['address' => 'Otro domicilio diferente'])->assertStatus(503);
        $this->assertNull($claim->fresh()->inspection_latitude);
        $this->assertNull($claim->fresh()->inspection_longitude);
    }
    public function test_connection_failure_leaves_the_saved_location_unchanged(): void
    {
        $inspector = Inspector::factory()->create();
        $claim = Claim::factory()->for($inspector)->create(['status' => 'Peritando', 'inspection_mode' => 'presencial']);
        Http::preventStrayRequests();
        Http::fake(['nominatim.openstreetmap.org/search*' => Http::failedConnection()]);
        $url = '/route-sheets/'.$claim->id.'/geocode';
        $this->actingAs($inspector->user)->postJson($url, ['address' => 'Corrientes 1000, Buenos Aires'])->assertStatus(503);
        Http::assertSentCount(1);
        $this->assertNull($claim->fresh()->inspection_latitude);
    }

}
