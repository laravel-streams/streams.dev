<?php

namespace Anomaly\TestAddon;

use Anomaly\Streams\Platform\Provider\ServiceProvider;

/**
 * Class AddonServiceProvider
 *
 * @link   http://pyrocms.com/
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class TestAddonServiceProvider extends ServiceProvider
{

    /**
     * The addon routes.
     *
     * @var array
     */
    public $routes = [
        'web' => [],
        'cp' => [],
    ];
}
