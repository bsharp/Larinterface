<?php namespace Bsharp\Larinterface;

use Illuminate\Filesystem\ClassFinder;
use Illuminate\Filesystem\Filesystem;
use App;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class Larinterface
 * @package Bsharp\Larinterface
 */
class Larinterface
{
    const SUCCESS = 0;
    const NOT_CLASS = 1;
    const EMPTY_CLASS = 2;
    const FAIL_WRITING = 3;
    const NO_MODIFICATION = 4;

    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var ClassFinder
     */
    private $classFinder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Larinterface constructor.
     *
     * @param ClassFinder $classFinder
     * @param Filesystem $filesystem
     */
    public function __construct(ClassFinder $classFinder, Filesystem $filesystem)
    {
        $this->classFinder = $classFinder;
        $this->filesystem = $filesystem;
    }

    /**
     * Parse config file to get all classes to extract.
     *
     *
     * @return array
     */
    public function getClasses()
    {
        $conf_classes = config('larinterface.classes');
        $conf_directories = config('larinterface.directories');
        $conf_ignore = config('larinterface.ignore');

        // Forge classes
        foreach ($conf_classes as $output => $classes) {
            if (is_numeric($output)) {
                $output = 0;
            }

            if (is_string($classes)) {
                $classes = [$classes];
            }

            if (isset($this->classes[$output])) {
                $this->classes[$output] = array_merge($this->classes[$output], $classes);
            } else {
                $this->classes[$output] = $classes;
            }
        }

        // Forge directories
        foreach ($conf_directories as $output => $directories) {
            if (is_numeric($output)) {
                $output = 0;
            }

            if (is_string($directories)) {
                $directories = [$directories];
            }

            foreach ($directories as $directory) {
                $classes = $this->classFinder->findClasses($directory);

                if (isset($this->classes[$output])) {
                    $this->classes[$output] = array_merge($this->classes[$output], $classes);
                } else {
                    $this->classes[$output] = $classes;
                }
            }
        }

        // Clean forged class
        foreach ($this->classes as $key => $value) {

            // Ignore files
            foreach ($conf_ignore as $class) {
                $classKey = array_search($class, $value);

                if ($classKey !== false) {
                    unset($value[$classKey]);
                }
            }

            $this->classes[$key] = array_unique($value);
        }

        return $this->classes;
    }

    /**
     * @param $class
     * @param null $output
     *
     * @return null
     */
    public function generate($class, $output = null)
    {
        // StubFile Arguments
        $arguments = [];

        // Compose the Interface Class Name
        $arguments['className'] = class_basename($class);

        $classNameType = config('larinterface.declaration');

        if ($classNameType === 'before') {
            $arguments['className'] = 'Interface' . $arguments['className'];
        } elseif ($classNameType === 'after') {
            $arguments['className'] .= 'Interface';
        }

        // Parse Interface namespace and class output
        if ($output == null) {
            $arguments['namespace'] = substr($class, 0, strrpos($class, '\\'));
            $path = str_replace('\\', '/', str_replace(class_basename($class), '', $class));
            $output = substr($path, 0, strlen($path) - 1);
        } else {
            $arrayPath = explode('/', $output);

            foreach ($arrayPath as $key => $directory) {
                $arrayPath[$key] = ucfirst($directory);
            }

            $arguments['namespace'] = implode('\\', $arrayPath);
        }

        $output = app_path(substr($output, strpos($output, '/') + 1, strlen($output)));

        // Make output Interface path
        $interfacePath = $output . '/' . $arguments['className'] . '.php';

        $updateInterface = 0;

        /**
         * At this point the class should perfectly implements the interface to avoid a fatal error.
         *
         * Possible workaround:
         * - Empty the interface to avoid any possible missmatch (dirtiest)(actual)
         * - Find a way to get the class tokenized definition and analyse it to empty just the right interface method
         * - Find a way to use ReflectionClass on a class with a not compatible Interface
         */

        // if the interface already exist we empty it
        if (file_exists($interfacePath)) {
            $interfaceContent = file_get_contents($interfacePath);

            $tokenized = token_get_all($interfaceContent);
            $interfaceContentEmpty = '';

            foreach ($tokenized as $token) {
                if (is_string($token) && $token === '{') {
                    break;
                }

                if (is_array($token)) {
                    $interfaceContentEmpty .= $token[1];
                } else {
                    $interfaceContentEmpty .= $token;
                }
            }

            $interfaceContentEmpty .= '{}';

            // Get last modification date on class on interface
            $updateInterface = $this->filesystem->lastModified($interfacePath);

            // Empty Interface
            $this->filesystem->put($interfacePath, $interfaceContentEmpty);
        } else {
            // Create a empty namespaced interface
        }

        $reflectedClass = new ReflectionClass($class);

        // Check if it's not already an interface
        if ($reflectedClass->isInterface() || $reflectedClass->isTrait()) {
            return self::NOT_CLASS;
        }

        // Check Class and Interface last updated timestamp
        if (file_exists($interfacePath)) {
            $updateClass = $this->filesystem->lastModified($reflectedClass->getFileName());

            if ($updateClass < $updateInterface) {
                return self::NO_MODIFICATION;
            }
        }

        // Get methods and properties
        $methods = $reflectedClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $properties = [];
        //$properties = $reflectedClass->getProperties(ReflectionProperty::IS_PUBLIC);

        $missingCommentBlock = 0;
        $arguments['methods'] = '';
        $arguments['properties'] = '';

        if (count($methods) == 0 && count($properties) == 0) {
            return null;
        }

        $classFile = file($reflectedClass->getFileName());

        // Parse Class methods
        foreach ($methods as $key => $reflectionMethod) {

            // Remove inherited methods
            if ($reflectionMethod->class != $class) {
                continue;
            }

            $start = $reflectionMethod->getStartLine();
            $end = $reflectionMethod->getEndLine();

            $comment = $reflectionMethod->getDocComment();
            $method = implode('', array_slice($classFile, $start - 1, $end - $start + 1));

            $tokenized = token_get_all('<?php ' . $method);
            $methodDeclaration = '';

            foreach ($tokenized as $token) {
                if (is_string($token) && $token === '{') {
                    break;
                }

                if (is_array($token)) {
                    $methodDeclaration .= $token[1];
                } else {
                    $methodDeclaration .= $token;
                }
            }

            $methodDeclaration = str_replace('<?php ', '', $methodDeclaration);

            if (empty($comment)) {
                $missingCommentBlock++;
                $arguments['methods'] .= trim($methodDeclaration) . ";\n\n    ";
            } else {
                $arguments['methods'] .= $comment . "\n    " . trim($methodDeclaration) . ";\n\n    ";
            }
        }

        // Trim end of methods string
        $arguments['methods'] = rtrim($arguments['methods']);

        // @TODO: Parse properties

        // Create output directory if needed
        if (!file_exists($output)) {
            $this->filesystem->makeDirectory($output, 0755, true);
        }

        // Add arguments for stubFile
        $arguments['datetime'] =  date('Y-m-d H:i:s');

        // Fill stubFile in memory
        $stubFile = file_get_contents(config('larinterface.stubFile'));

        foreach ($arguments as $key => $argument) {
            $stubFile = str_replace('%' . $key . '%', $argument, $stubFile);
        }

        // Write Interface on disk using stubFile
        if ($this->filesystem->put($interfacePath, $stubFile) === false) {
            return [self::FAIL_WRITING, $interfacePath];
        }

        return [self::SUCCESS, $missingCommentBlock];
    }
}
