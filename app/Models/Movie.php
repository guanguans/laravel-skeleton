<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SerializeDate;
use Illuminate\Database\Schema\Blueprint;
use Orbit\Concerns\Orbital;
use Orbit\Concerns\SoftDeletes;

class Movie extends \Illuminate\Database\Eloquent\Model
{
    use Orbital;
    use SerializeDate;
    use SoftDeletes;

    public static string $driver = 'json';

    protected $fillable = [
        'name',
        'director',
    ];

    public static function schema(Blueprint $table): void
    {
        $table->string('name');
        $table->string('director');
        // $table->timestamps();
        $table->softDeletes();
    }

    public static function enableOrbit(): bool
    {
        return true;
    }

    public function getKeyName(): string
    {
        return 'name';
    }

    public function getIncrementing(): bool
    {
        return false;
    }
}
