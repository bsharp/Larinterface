<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PHP Classes files that need to have a interface generated.
    |--------------------------------------------------------------------------
    |
    | All class will have an interface generated in the specified directory
    | (the array key). If the key is not set, the Interface will be created in
    | the class directory.
    |
    */

    'classes' => [
        App\MyClass::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Directories containing PHP classes files
    |--------------------------------------------------------------------------
    |
    | All directories will be opened and a proper interface will be
    | generated for each class contained in the directory, the output
    | interface is generated in the specified directory (the array key).
    |
    */

    'directories' => [
        'app/Custom/Directory/Contracts' => 'app/Models'
    ],

    /*
    |--------------------------------------------------------------------------
    | List of PHP class to ignore
    |--------------------------------------------------------------------------
    |
    | This list act as a blacklist of class that should not have an
    | Interface generated but are included in the directories config array.
    | Such as an Abstract class or config files.
    |
    */

    'ignore' => [
        App\MyClassAbstract::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Path to the Larinterface stubFile
    |--------------------------------------------------------------------------
    |
    | You can copy the Larinterface stubFile to customize how the package
    | generate Interfaces for your classes. Use any of the parameters
    | listed below and feel free to modify anything you want in the file.
    |
    | StubFile path: vendor/bsharp/larinterface/src/stubs/SampleInterface
    |
    | Parameters:
    |
    | %namespace%  : The Interface namespace
    | %className%  : The Interface ClassName
    | %properties% : The Interface properties
    | %methods%    : The Interface methods
    |
    */

    'stubFile' => base_path('vendor/bsharp/larinterface/src/stubs/SampleInterface'),

    /*
    |--------------------------------------------------------------------------
    | Choose the generated interface filename
    |--------------------------------------------------------------------------
    |
    | You can change the generated interface filename by choosing if
    | Larinterface add the "Interface" keyword a a prefix, a suffix or
    | if it should call the interface by the same name as the class.
    |
    | Possible values:
    |
    | before : InterfaceMyClass
    | after  : MyClassInterface
    | none   : MyClass
    |
    |
    */

    'declaration' => 'after',
];
