<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\AllowedFilterable;
use App\Models\Concerns\Filterable;
use App\Models\Concerns\ForceUseIndexable;
use App\Models\Concerns\Observable;
use App\Models\Concerns\Pipeable;
use App\Models\Concerns\SerializeDate;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Arr;
use Watson\Validating\ValidatingTrait;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use AllowedFilterable;
    use Filterable;
    use ForceUseIndexable;
    use Observable;
    use Pipeable;
    use SerializeDate;
    use Sortable;
    use ValidatingTrait;
    // use HasUlids;
    // use HasUuids;
    // use MassPrunable;
    // use Prunable;

    protected $rules = [];

    public function toDotArray(): array
    {
        return Arr::dot($this->toArray());
    }
}
