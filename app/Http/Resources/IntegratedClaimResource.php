<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegratedClaimResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return ['id' => $this->resource['id'], 'external_id' => $this->resource['external_id'], 'claim_number' => $this->resource['claim_number'], 'company_id' => $this->resource['company_id'], 'status' => $this->resource['status'], 'created_at' => $this->resource['created_at']];
    }
}
