<?php

namespace App\Models;

use App\Traits\AllowedFilter;
use App\Traits\Filterable;
use App\Traits\ForceUseIndex;
use App\Traits\Observable;
use App\Traits\QueryBuilderPipe;
use App\Traits\SerializeDate;
use App\Traits\Sortable;
use Watson\Validating\ValidatingTrait;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use AllowedFilter;
    use Filterable;
    use ForceUseIndex;
    use Observable;
    use QueryBuilderPipe;
    use SerializeDate;
    use Sortable;
    use ValidatingTrait;

    protected $rules = [];
}
