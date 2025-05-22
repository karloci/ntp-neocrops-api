<?php

use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $modulesPath = dirname(__DIR__, 2) . "/src/";
    $modulesFinder = new Finder();

    $modulesFinder->directories()->in($modulesPath)->depth(0);

    foreach ($modulesFinder as $module) {
        $controllerPath = $module->getRealPath();
        if (is_dir($controllerPath)) {
            $routes->import($controllerPath, "attribute")
                ->namePrefix("api_")
                ->schemes(["http", "https"])
                ->format("json")
                ->stateless();
        }
    }

    $controllerPath = dirname(__DIR__, 2) . "/src/Core/Controller/";
    $routes->import($controllerPath, "attribute")
        ->namePrefix("api_")
        ->schemes(["http", "https"])
        ->format("json")
        ->stateless();
};
