<?php

namespace App\Models;

use App\Models\Concerns\SerializeDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HttpLog extends Model
{
    use SerializeDate;
    use SoftDeletes;

    protected $table = 'http_log';

    protected $guarded = [];
}
