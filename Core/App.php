<?php

namespace Core;

use Core\Routing\AppRouter;
use Core\Service\AppContainer;

class App
{
    /**
     * @var AppContainer
     */
    private $container;

    /**
     * @var AppRouter
     */
    private $router;

    public function __construct()
    {
        $this->init();
        $this->run();
    }

    private function init()
    {
        $this->container = AppContainer::getInstance();
    }

    private function run()
    {
        $this->container->getRouter()->initRoutes()->checkRoutes();
    }
}