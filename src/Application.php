<?php

namespace Deniaz\Splendid;

use Deniaz\Splendid\Provider\RouteProvider;
use Deniaz\Terrific\Twig\Extension\TerrificExtension;
use Silex\Application as SilexApp;
use Silex\Provider\TwigServiceProvider;

/**
 * Extends Silex Application with some must-have configurations.
 *
 * @package Deniaz\Splendid
 * @author Robert Vogt <robert.vogt@namics.com>
 */
class Application extends SilexApp
{
    /**
     * @var string Splendid Root Directory
     */
    private $rootDir;

    /**
     * Instantiate a new Application and configures Terrific-style routes and Twig.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param string $rootDir Splendid PHP Root Directory.
     * @param array  $values The parameters or objects.
     */
    public function __construct($rootDir, array $values = [])
    {
        parent::__construct($values);

        $this['root_dir'] = $rootDir . '/';

        $this['debug'] = true;
        $this['tc.config'] = (is_readable($this->rootDir . 'config.json'))
            ? json_decode(file_get_contents($this->rootDir . 'config.json'))
            : null
        ;

        $this->register(
            new TwigServiceProvider(),
            [
                'twig.path' => $this->rootDir . $this['tc.config']->micro->view_directory
            ]
        );

        $app['twig'] = $this->share($this->extend('twig', function($twig, $app) {
            $paths = [
                $app['root_dir'] . $app['tc.config']->micro->view_partial_directory
            ];
            foreach ($app['tc.config']->micro->components as $component) {
                $paths[] = $app['root_dir'] . $component->path;
            }

            $twig->addExtension(new TerrificExtension($paths, $app['tc.config']->micro->view_file_extension));
            return $twig;
        }));

        $this->mount(
            '/',
            new RouteProvider(
                json_decode(
                    file_get_contents($this->rootDir . 'project/content.json'),
                    true
                )
            )
        );
    }

    /**
     * @return string Splendid Root Directory
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }
}