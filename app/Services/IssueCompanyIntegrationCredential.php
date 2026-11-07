<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyIntegrationCredential;

class IssueCompanyIntegrationCredential
{
    /** @return array{credential:CompanyIntegrationCredential,token:string} */
    public function handle(Company $company, string $name, bool $simulation = false): array
    {
        $secret = bin2hex(random_bytes(32));
        $credential = CompanyIntegrationCredential::create(['company_id' => $company->id, 'name' => $name, 'abilities' => ['claims:create'], 'token_hash' => hash('sha256', $secret), 'is_simulation' => $simulation]);
        $token = $credential->id.'|'.$secret;
        if ($simulation) {
            $credential->update(['simulator_token' => $token]);
        }

        return ['credential' => $credential, 'token' => $token];
    }
}
