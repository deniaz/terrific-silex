<?php

namespace Deniaz\Test\Splendid\Provider;

use Deniaz\Splendid\Provider\RouteProvider;
use \PHPUnit_Framework_TestCase;
use Silex\Application;

class RouteProviderTest extends PHPUnit_Framework_TestCase
{
    public function testControllerCollection()
    {
        $provider = new RouteProvider();

        $controllers = $provider->connect(new Application());
        $routes = $controllers->flush();
        $this->assertEquals(2, count($routes));

        $expected = ['/', '/{view}'];
        $i = 0;
        foreach ($routes as $route) {
            $this->assertTrue($route->getPath() === $expected[$i++]);
        }
    }
}