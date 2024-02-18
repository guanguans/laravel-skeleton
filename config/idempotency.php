<?php

return [
    'idempotency_header' => env('IDEMPOTENCY_HEADER', 'Idempotency-Key'),
    'expiration_in_minutes' => env('IDEMPOTENCY_EXPIRATION', 1440), //24 hours
];
