<?php

namespace Core\Service;

use Core\Routing\AppRoute;

class RouteFactory
{
    public function getRoute($path, $name)
    {
        return new AppRoute($path, $name);
    }
}