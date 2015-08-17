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
    // Returned by the generate method in case of success.
    const SUCCESS = 0;

    // Returned by the generate method if the given class is a Trait or an Interface.
    const NOT_CLASS = 1;

    // Returned by the generate method if the class as no public method.
    const EMPTY_CLASS = 2;

    // Returned by the generate method if it can't write the Interface file.
    const FAIL_WRITING = 3;

    // Returned by the generate method if the Interface have a younger timestamp than the class.
    const NO_MODIFICATION = 4;

    // Code used in case of parse error.
    const PARSE_ERROR = 5;

    /**
     * @var array contain classes list with interface's to be generated path.
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
     * Parse config file and project to get all classes to extract.
     *
     *
     * @return array
     */
    public function getClasses()
    {
        $conf_classes = config('larinterface.classes');
        $conf_directories = config('larinterface.directories');
        $conf_ignore = config('larinterface.ignore');

        $classesArray = [];

        // Forge classes
        foreach ($conf_classes as $output => $classes) {
            if (is_numeric($output)) {
                $output = 0;
            }

            if (is_string($classes)) {
                $classes = [$classes];
            }

            if (isset($classesArray[$output])) {
                $classesArray[$output] = array_merge($classesArray[$output], $classes);
            } else {
                $classesArray[$output] = $classes;
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

                if (isset($classesArray[$output])) {
                    $classesArray[$output] = array_merge($classesArray[$output], $classes);
                } else {
                    $classesArray[$output] = $classes;
                }
            }
        }

        // Clean forged class
        foreach ($classesArray as $key => $value) {

            // Ignore files
            foreach ($conf_ignore as $class) {
                $classKey = array_search($class, $value);

                if ($classKey !== false) {
                    unset($value[$classKey]);
                }
            }

            $classesArray[$key] = array_unique($value);
        }

        return $this->classes = $this->extractInterfacePathFromClasses($classesArray);
    }

    /**
     * Transform the simple class map array by adding more info in it.
     *
     * @param array $classesArray
     *
     * @return array
     */
    protected function extractInterfacePathFromClasses(array $classesArray)
    {
        $extracted = [];

        foreach ($classesArray as $output => $classes) {
            foreach ($classes as $class) {

                // Namespace to file path
                $path = str_replace('\\', '/', str_replace(class_basename($class), '', $class));

                // Remove trailing "/"
                $input = substr($path, 0, strlen($path) - 1);

                // Forge output if it doesn't exist yet
                $output = is_null($output) ? $input : $output;

                // Use app_path to get the correct first dir (eg: app instead of App)
                $classOutput = app_path(substr($output, strpos($output, '/') + 1, strlen($output)));
                $input = app_path(substr($input, strpos($input, '/') + 1, strlen($input))) . '/' .
                    class_basename($class) . '.php';

                // Get namespace of the interface to generate
                $namespace = explode('/', $output);

                foreach ($namespace as &$chunk) {
                    $chunk = ucfirst($chunk);
                }

                $namespace = implode('\\', $namespace);

                // Get class name
                $classNameType = config('larinterface.declaration');
                $className = class_basename($class);

                if ($classNameType === 'before') {
                    $className = 'Interface' . $className;
                } elseif ($classNameType === 'after') {
                    $className .= 'Interface';
                }

                $extracted[$class] = [
                    'output' => $classOutput,
                    'output_file' => $classOutput . '/' . $className . '.php',
                    'input_file' => $input,
                    'namespace' => $namespace,
                    'name' => $className
                ];
            }
        }

        return $extracted;
    }

    /**
     * Store classes list in cache directory ot be used by gulp.
     */
    public function store()
    {
        $classes = [];

        foreach ($this->classes as $class) {
            $classes[] = $class['input_file'];
        }

        $json_classes = json_encode($classes);

        $this->filesystem->put(config('larinterface.cache_directory') . '/larinterface.json', $json_classes);
    }

    /**
     * Generate an interface for the given class and output.
     *
     * @param $class
     * @param null $output
     * @param $outputFile
     * @param $input_file
     * @param $namespace
     * @param $interfaceName
     *
     * @return null
     */
    public function generate($class, $output, $outputFile, $input_file, $namespace, $interfaceName)
    {

        // StubFile Arguments
        $arguments = [];

        // Compose the Interface Class Name
        $arguments['className'] = $interfaceName;
        $arguments['namespace'] = $namespace;

        // Store current datetime
        $arguments['datetime'] =  date('Y-m-d H:i:s');

        // Initialise methods and properties
        $arguments['methods'] = '';
        $arguments['properties'] = '';

        // Fill stubFile in memory
        $stubFile = file_get_contents(config('larinterface.stubFile'));

        /**
         * At this point the class should perfectly implements the interface to avoid a fatal error.
         *
         * Possible workaround:
         * - Empty the interface to avoid any possible missmatch (dirtiest)(actual)
         * - Find a way to get the class tokenized definition and analyse it to empty just the right interface method
         * - Find a way to use ReflectionClass on a class with a not compatible Interface
         */

        // if the interface already exist we empty it
        if (file_exists($outputFile)) {
            $interfaceContent = file_get_contents($outputFile);

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

            // Get last modification datetime of the class interface
            $updateInterface = $this->filesystem->lastModified($outputFile);
            // Get last modification datetime of the class
            $updateClass = $this->filesystem->lastModified($input_file);

            // Save old content
            $outputOldContent = $this->filesystem->get($outputFile);

            // Empty Interface
            $this->filesystem->put($outputFile, $interfaceContentEmpty);

            // Check if update is needed
            if ($updateClass < $updateInterface) {

                // if not Refill the interface
                if (isset($outputOldContent)) {
                    $this->filesystem->put($outputFile, $outputOldContent);
                }

                return self::NO_MODIFICATION;
            }
        } else {

            // Create output directory if needed
            if (!file_exists($output)) {
                $this->filesystem->makeDirectory($output, 0755, true);
            }

            $scratchStub = $stubFile;

            // Add arguments to the scratch stubFile
            foreach ($arguments as $key => $argument) {
                $scratchStub = str_replace('%' . $key . '%', $argument, $scratchStub);
            }

            // Write the Interface on disk
            if ($this->filesystem->put($outputFile, $scratchStub) === false) {
                return [self::FAIL_WRITING, $outputFile];
            }
        }

        /**
         * At this point we are sure that the class have a valid interface, an empty
         * one but it's just to use the reflection on the class without any error
         * concerning difference between the class and the interface that it implement.
         */

        $reflectedClass = new ReflectionClass($class);

        // Check if it's already an interface
        if ($reflectedClass->isInterface() || $reflectedClass->isTrait()) {
            return self::NOT_CLASS;
        }

        // Get methods and properties
        $methods = $reflectedClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $properties = [];
        //$properties = $reflectedClass->getProperties(ReflectionProperty::IS_PUBLIC);

        $missingCommentBlock = 0;

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
        foreach ($arguments as $key => $argument) {
            $stubFile = str_replace('%' . $key . '%', $argument, $stubFile);
        }

        // Write Interface on disk using stubFile
        if ($this->filesystem->put($outputFile, $stubFile) === false) {
            return [self::FAIL_WRITING, $outputFile];
        }

        return [self::SUCCESS, $missingCommentBlock];
    }
}
