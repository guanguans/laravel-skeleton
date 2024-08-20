<?php

declare(strict_types=1);

arch('will not use debugging functions')
    ->expect([
        'dd',
        'die',
        'dump',
        'echo',
        'exit',
        'print',
        'print_r',
        'printf',
        'ray',
        'trap',
        'var_dump',
        'var_export',
        'vprintf',
    ])
    ->each->not->toBeUsed();
