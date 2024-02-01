<?php

namespace Config;

use AnserGateway\Filters\JsonResponseFilter;
use App\Filters\TestFilter;
use App\Filters\TestFilter2;
use App\Filters\GlobalFilter;

class Filters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        'jsonResponse' => JsonResponseFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = [
        'before' => [
            // 'global' => ['except' => 'api/*'],
        ],
        'after' => [
            'jsonResponse'
        ],
    ];

}
