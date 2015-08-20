<?php

use Bsharp\Larinterface\Larinterface;
use Illuminate\Filesystem\Filesystem;

/**
 * Class LarinterfaceTest
 */
class LarinterfaceTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('larinterface', require __DIR__ . '/../../publish/config/larinterface.php');

        // Create test Larinterface configuration
        $app['config']->set('larinterface.classes', [
            // @TODO: test
        ]);

        $app['config']->set('larinterface.directories', [
            'app/Larinterface/Test/Interfaces' => 'app/Larinterface/Test/Classes'
        ]);

        $app['config']->set('larinterface.ignore', [
            \App\Larinterface\Test\Classes\AbstractClass::class
        ]);

        $app['config']->set('larinterface.stubFile', base_path('vendor/SampleInterface'));


        /** @var Filesystem $filesystem */
        $filesystem = app()->make(Filesystem::class);

        // Reset Orchestra fixture app and vendor directory
        $filesystem->deleteDirectory(app_path('larinterface'));
        $filesystem->delete(base_path('vendor/SampleInterface'));

        // Copy class dir to Orchestra fake app dir and stubfile to vendor dir
        $filesystem->copyDirectory(__DIR__ . '/samples', app_path());
        $filesystem->copyDirectory(__DIR__ . '/../../src/stubs', base_path('vendor'));
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            Bsharp\Larinterface\LarinterfaceServiceProvider::class
        ];
    }

    /**
     * Verify if the class as the proper configuration parameters.
     */
    public function testInitialisation()
    {
        $instance = app()->make(Larinterface::class);

        $this->assertEquals(0, $instance::SUCCESS);
        $this->assertEquals(1, $instance::NOT_CLASS);
        $this->assertEquals(2, $instance::EMPTY_CLASS);
        $this->assertEquals(3, $instance::FAIL_WRITING);
        $this->assertEquals(4, $instance::NO_MODIFICATION);
        $this->assertEquals(5, $instance::PARSE_ERROR);
    }

    /**
     * Test the get classes method (class input/output and interface info generator)
     */
    public function testGetClasses()
    {
        /** @var Larinterface $instance */
        $instance = app()->make(Larinterface::class);

        // Get classes info
        $classes = $instance->getClasses();

        // We should have 3 elements because AbstractClass is ignored
        $this->assertEquals(3, count($classes));

        // Validate FirstClass

        foreach ($classes as $key => $class) {
            $className = class_basename($key);

            $output = str_replace(app_path(), '', $class['output']);
            $output_file = str_replace(app_path(), '', $class['output_file']);
            $input_file = str_replace(app_path(), '', $class['input_file']);

            $this->assertEquals('/Larinterface/Test/Interfaces', $output);
            $this->assertEquals('/Larinterface/Test/Interfaces/' . $className . 'Interface.php', $output_file);
            $this->assertEquals('/Larinterface/Test/Classes/' . $className . '.php', $input_file);
            $this->assertEquals('App\Larinterface\Test\Interfaces', $class['namespace']);
            $this->assertEquals($className . 'Interface', $class['name']);
        }
    }

    /**
     * Test the Larinterface generate method.
     *
     * @runTestsInSeparateProcesses
     */
    public function testGenerate()
    {
        /** @var Larinterface $instance */
        $instance = app()->make(Larinterface::class);

        // Create classes array
        $classes = [
            'App\Larinterface\Test\Classes\FirstClass' => [
                'output' => app_path('Larinterface/Test/Interfaces'),
                'output_file' => app_path('Larinterface/Test/Interfaces/FirstClassInterface.php'),
                'input_file' => app_path('Larinterface/Test/Classes/FirstClass.php'),
                'namespace' => 'App\Larinterface\Test\Interfaces',
                'name' => 'FirstClassInterface',
            ],
            'App\Larinterface\Test\Classes\SecondClass' => [
                'output' => app_path('Larinterface/Test/Interfaces'),
                'output_file' => app_path('Larinterface/Test/Interfaces/SecondClassInterface.php'),
                'input_file' => app_path('Larinterface/Test/Classes/SecondClass.php'),
                'namespace' => 'App\Larinterface\Test\Interfaces',
                'name' => 'SecondClassInterface',
            ],
            'App\Larinterface\Test\Classes\ThirdClass' => [
                'output' => app_path('Larinterface/Test/Interfaces'),
                'output_file' => app_path('Larinterface/Test/Interfaces/ThirdClassInterface.php'),
                'input_file' => app_path('Larinterface/Test/Classes/ThirdClass.php'),
                'namespace' => 'App\Larinterface\Test\Interfaces',
                'name' => 'ThirdClassInterface',
            ],
        ];

        // @TODO: nothing work to check that ? :(
    }
}
