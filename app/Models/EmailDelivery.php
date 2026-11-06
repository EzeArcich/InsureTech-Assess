<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDelivery extends Model
{
    protected $fillable = ['request_id', 'claim_id', 'email_template_version_id', 'sent_by', 'recipient', 'cc', 'subject', 'body', 'status', 'sent_at'];

    protected $casts = ['cc' => 'array', 'sent_at' => 'datetime'];
}
