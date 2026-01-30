<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/((?!api|_profiler|_wdt|assets|favicon).*)?(*:50)'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        50 => [
            [['_route' => 'index', 'reactRouting' => null, '_controller' => 'App\\Controller\\HomeController::index'], ['reactRouting'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
