<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CLAIM_COLUMNS = [
        'siniestro' => 'claim_number', 'patente' => 'license_plate',
        'fechaip' => 'inspection_date', 'estado' => 'status', 'cliente' => 'client_type',
        'modalidad' => 'inspection_mode', 'observaciones' => 'notes',
        'fechacierre' => 'closed_at', 'contacto' => 'contact', 'codigoinspeccion' => 'inspection_code',
    ];

    private const WORKSHOP_COLUMNS = [
        'vacaciones' => 'holidays', 'descfranq' => 'deductible_discount', 'granizo' => 'hail',
        'ponerep' => 'repair_availability', 'taller' => 'name', 'direccion' => 'address',
        'barrio' => 'neighborhood', 'localidad' => 'city', 'telefonos' => 'phone',
        'zona' => 'area', 'razon' => 'business_name', 'cuit' => 'tax_id',
        'grua' => 'towing', 'traslado' => 'transport',
    ];

    private const EXTRA_CLAIM_COLUMNS = [
        'link', 'image', 'short_number', 'reason', 'workshop_name', 'coordinator',
        'send_order', 'inspection_time', 'inspector_notes', 'location', 'third_party_name',
        'analyst', 'closure', 'quoted', 'negotiation', 'first_offer', 'second_offer',
        'template', 'estimate', 'inspector_email', 'inspector_phone', 'claim_url',
        'producer_name', 'producer_email', 'email', 'cc', 'cc2',
    ];

    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('prefix', 20)->unique();
            $table->timestamps();
        });

        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
            $table->unique(['id', 'company_id']);
        });

        Schema::create('inspectors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::rename('siniestros', 'claims');
        $this->renameColumns('claims', self::CLAIM_COLUMNS);
        Schema::table('claims', function (Blueprint $table): void {
            $table->foreignId('company_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('client_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->foreignId('inspector_id')->nullable()->constrained()->nullOnDelete();
            $table->foreign(['client_id', 'company_id'])->references(['id', 'company_id'])->on('clients');
            foreach (self::EXTRA_CLAIM_COLUMNS as $column) {
                $table->text($column)->nullable();
            }
        });

        // Preserve historical text and contact details before dropping the old columns.
        DB::table('claims')->orderBy('id')->chunkById(200, function ($claims): void {
            foreach ($claims as $claim) {
                $companyId = null;
                if ($claim->compania !== null && $claim->compania !== '') {
                    $companyId = DB::table('companies')->where('name', $claim->compania)->value('id');
                    if ($companyId === null) {
                        $companyId = DB::table('companies')->insertGetId([
                            'name' => $claim->compania, 'prefix' => 'LEG'.$claim->id,
                            'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                }
                $inspectorId = null;
                if ($claim->inspector !== null && $claim->inspector !== '') {
                    $inspectorId = DB::table('inspectors')->where('name', $claim->inspector)->value('id');
                    if ($inspectorId === null) {
                        $inspectorId = DB::table('inspectors')->insertGetId([
                            'name' => $claim->inspector, 'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                }
                $clientId = DB::table('clients')->insertGetId([
                    'company_id' => $companyId, 'phone' => $claim->telefono,
                    'address' => $claim->direccion, 'city' => $claim->localidad,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('claims')->where('id', $claim->id)->update([
                    'company_id' => $companyId, 'client_id' => $clientId, 'inspector_id' => $inspectorId,
                ]);
            }
        });
        Schema::table('claims', function (Blueprint $table): void {
            $table->dropColumn(['compania', 'inspector', 'telefono', 'direccion', 'localidad']);
            $table->index(['status', 'inspection_date']);
            $table->index(['company_id', 'claim_number']);
        });

        Schema::rename('_t_h', 'workshops');
        $this->renameColumns('workshops', self::WORKSHOP_COLUMNS);
        $this->renameColumns('blogs', ['titulo' => 'title', 'contenido' => 'content']);
        $this->renameColumns('users', ['telefono' => 'phone']);

        Schema::create('producers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
        Schema::create('files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('claim_id')->constrained()->cascadeOnDelete();
            $table->text('url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
        Schema::dropIfExists('producers');
        Schema::table('claims', function (Blueprint $table): void {
            foreach (['compania', 'inspector', 'telefono', 'direccion', 'localidad'] as $column) {
                $table->string($column)->nullable();
            }
        });
        DB::table('claims')->orderBy('id')->chunkById(200, function ($claims): void {
            foreach ($claims as $claim) {
                $client = DB::table('clients')->find($claim->client_id);
                DB::table('claims')->where('id', $claim->id)->update([
                    'compania' => DB::table('companies')->where('id', $claim->company_id)->value('name'),
                    'inspector' => DB::table('inspectors')->where('id', $claim->inspector_id)->value('name'),
                    'telefono' => $client?->phone, 'direccion' => $client?->address, 'localidad' => $client?->city,
                ]);
            }
        });
        Schema::table('claims', function (Blueprint $table): void {
            $table->dropForeign(['client_id', 'company_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['inspector_id']);
            $table->dropUnique(['client_id']);
            $table->dropIndex(['status', 'inspection_date']);
            $table->dropIndex(['company_id', 'claim_number']);
            $table->dropColumn(['company_id', 'client_id', 'inspector_id', ...self::EXTRA_CLAIM_COLUMNS]);
        });
        $this->renameColumns('claims', array_flip(self::CLAIM_COLUMNS));
        Schema::rename('claims', 'siniestros');
        $this->renameColumns('workshops', array_flip(self::WORKSHOP_COLUMNS));
        Schema::rename('workshops', '_t_h');
        $this->renameColumns('blogs', ['title' => 'titulo', 'content' => 'contenido']);
        $this->renameColumns('users', ['phone' => 'telefono']);
        Schema::dropIfExists('inspectors');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('companies');
    }

    /** @param array<string, string> $columns */
    private function renameColumns(string $tableName, array $columns): void
    {
        foreach ($columns as $from => $to) {
            Schema::table($tableName, function (Blueprint $table) use ($from, $to): void {
                $table->renameColumn($from, $to);
            });
        }
    }
};
