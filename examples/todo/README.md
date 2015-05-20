To-do Listing Example
=====================

Like many developers, I find that a good example is sometimes the best means of grasping a new concept or tool.
This example highlights a solution to the contrived example of displaying task title listings on the CLI.

Setup
-----

The initial step to solving this problem is to first gain access to the Conductor console application within our repository.
Below is the sample 'composer.json' file used to define the root repositories tool dependencies.

```json
{
    "name": "mybuilder/todo",
    "require": {
        "mybuilder/conductor": "*@dev"
    },
    "config": {
        "bin-dir": "bin"
    }
}
```

We are then required to specify within 'conductor.yml', the zipped archive and internal packages directories present.
You will notice by looking at the following YAML definition that we have decided to place our packages into a single directory called 'package'.
In the case where more fine-grained separation is required, simply adding further directories to this list will suffice.

```yaml
artifacts_repository: ./artifact
packages:
    - ./package/*
```

With these two files now in-place we are able to 'composer install' and download Conductor, along with all it's required dependencies.

The Package
-----------

The model in which we internally represent a task, along with the title listing projection service is core domain logic.
It is also agnostic and does not concern itself with the delivery and persistent methods it could be used in.
As a result of this, the task model, service and repository interface contract can all be packaged up in isolation without any external dependencies.

```bash
package
└── todo
    ├── src
    │   ├── Task.php
    │   ├── TaskRepository.php
    │   └── TaskService.php
    ├── tests
    │   ├── TaskServiceTest.php
    │   └── TaskTest.php
    ├── README.md
    ├── composer.json
    └── phpunit.xml.dist
```

As you will notice above, the package's structure follows a similar pattern found in most typical composer dependent packages.
As the package does not depend on anything from the outside world, no Conductor dependent additions are required to the 'composer.json' file.

```json
{
    "name": "mybuilder/todo-package",
    "version": "0.1",
    "autoload": {
        "psr-4": {
            "MyBuilder\\Package\\ToDo\\": "src/"
        }
    },
    "require-dev": {
        "phpunit/phpunit": "~4.0"
    },
    "config": {
        "bin-dir": "bin"
    }
}
```

The Application
---------------

With the core domain-logic now in place we are able to move on to implementing the CLI application.
Following a similar pattern to how the package is structured we will isolate the application into a single directory - allowing us to easily add other delivery mechanisms (such as 'web') at a later date.

```bash
app/
└── cli
    ├── src
    │   ├── App.php
    │   └── JsonFileTaskRepository.php
    ├── tests
    │   └── JsonFileTaskRepositoryTest.php
    ├── README.md
    ├── composer.json
    ├── phpunit.xml.dist
    └── todo
```

Looking at the file-based JSON repositories implementation you will notice that we depend upon several domain concepts.
This is the use-case for the Conductor tool.
Not only does it allow us to locally depend on other packages within the same repository, but also symbolically links the dependencies to the working directories current state.

```json
{
    "name": "mybuilder/todo-cli",
    "version": "0.1",
    "autoload": {
        "psr-4": {
            "MyBuilder\\App\\ToDo\\Cli\\": "src/"
        }
    },
    "require": {
        "mybuilder/todo-package": "*@dev"
    },
    "require-dev": {
        "phpunit/phpunit": "~4.0"
    },
    "config": {
        "bin-dir": "bin"
    },
    "scripts": {
        "pre-install-cmd": [
            "../../bin/conductor update -c ../.."
        ],
        "pre-update-cmd": [
            "../../bin/conductor update -c ../.."
        ],
        "post-update-cmd": [
            "../../bin/conductor fix-composer-lock -c ../.."
        ],
        "pre-autoload-dump": [
            "../../bin/conductor symlink -c ../.."
        ]
    },
    "repositories": [
        {
            "type": "artifact",
            "url": "../../artifact/"
        }
    ]
}
```

As you can see from the above composer definition, there is a little boilerplate that is required to correctly configure and invoke Conductor, highlighted within 'scripts'.
When an install or update composer action is invoked, all packages found within the defined directories are zipped and archived.
In the case of a successful update, we also address an issue in-regard to how Composer always stores absolute paths in lock files, even though relative paths have been supplied.
So as to allow for the lock files to be committed and developers freely specify where they wish to place the repository, we replace these absolute paths with their relative counterparts.
Finally, before dumping the autoloader we symbolically link the 'vendor' directories internal dependences used within each package to the current working directory version.
This is required as internally the archived directories do not contain any source code, only the directory paths to symbolically link.

With the command line application now having access to the 'todo-package', we are able to bootstrap the application and solve the problem laid out in its entirety.

```php
<?php

namespace MyBuilder\App\ToDo\Cli;

use MyBuilder\Package\ToDo\TaskService;

class App
{
    private $service;
    private $filePath;

    public function __construct($filePath)
    {
        $this->service = new TaskService(
            new JsonFileTaskRepository($this->filePath = $filePath)
        );
    }

    public function run()
    {
        $this->fillWithDummyTasks();

        foreach ($this->service->fetchAllTitles() as $title) {
            echo "* $title\n";
        }

        $this->removeAllTasks();
    }

    private function fillWithDummyTasks()
    {
        foreach (range(1, 3) as $n) {
            $this->service->addTask("Task $n", "Task $n Description");
        }
    }

    private function removeAllTasks()
    {
        unlink($this->filePath);
    }
}
```

You will notice above that we provide a dummy set of tasks for the service to consume.
This works in this use-case as we have not been tasked with adding the ability to persist user inputted items at this time.
Finally, you are able to 'composer install' this package, bringing in the symbolically linked internal dependencies, and execute the solution.

```php
#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new \MyBuilder\App\ToDo\Cli\App(__DIR__ . '/tasks.json');
$app->run();
```

```bash
$ ./todo
* Title 1
* Title 2
* Title 3
```
