<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Client;
use App\Models\Company;
use App\Models\Inspector;
use App\Models\Producer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClaimSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call([
                CompanySeeder::class, WorkshopSeeder::class, ProducerSeeder::class,
                InspectorSeeder::class, ClientSeeder::class,
            ]);

            $inspectors = Inspector::orderBy('id')->get();
            $producers = Producer::orderBy('id')->get();
            $remaining = max(0, 200 - Claim::count());
            $index = 0;

            $clients = Client::whereNotNull('company_id')->whereDoesntHave('claim')
                ->orderBy('company_id')->orderBy('id')->limit($remaining)->get();
            for ($offset = 0; $offset < $remaining; $offset++) {
                $client = $clients->get($offset) ?? Client::factory()->for(Company::orderBy('id')->firstOrFail())->create();
                $producer = $producers[$index % $producers->count()];
                Claim::factory()->for($client)->for($producer)->for($inspectors[$index % $inspectors->count()])->create([
                    'status' => ['Pendiente', 'Ausente', 'Coordinado', 'Peritando', 'IPgestion'][$index % 5],
                    'producer_name' => $producer->name,
                    'producer_email' => $producer->email,
                ]);
                $index++;
            }

            Claim::whereNull('producer_id')->orderBy('id')->chunkById(100, function ($claims) use ($producers): void {
                foreach ($claims as $claim) {
                    /** Preserve historical producer details when no matching producer exists. */
                    if ($claim->producer_name || $claim->producer_email) {
                        $producer = Producer::query()
                            ->when($claim->producer_email,
                                fn ($query) => $query->where('email', $claim->producer_email),
                                fn ($query) => $query->where('name', $claim->producer_name))
                            ->first();
                        if ($producer === null) {
                            continue;
                        }
                    } else {
                        $producer = $producers[($claim->id - 1) % $producers->count()];
                    }
                    $claim->update(['producer_id' => $producer->id, 'producer_name' => $producer->name, 'producer_email' => $producer->email]);
                }
            });
        });
    }
}
