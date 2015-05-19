[![Build Status](https://secure.travis-ci.org/mybuilder/conductor.svg?branch=master)](http://travis-ci.org/mybuilder/conductor)

Conductor
========

This tool allows you to manage isolated, internal [Composer](https://getcomposer.org/) packages within a single, monolithic repository.
Separating units of code based on directory structure, as opposed to at the repository level, maintains a single source of truth whilst providing the benefits of clearly defined component boundaries.

The Problem
-----------

Years back our code-base was stored in a single SVN repository, requiring much developer discipline to keep clean and decoupled.
After a large migration we thought it best to break the project into more manageable components, separated into Composer packages in individual Git repositories.
Unfortunately, it did not turn out like we envisioned, quickly becoming a huge overhead to maintain.
Now we are back to a single Git repository again, however, this time we have been able to bring back with us the clear separation and boundaries that Composer packages provided us.

Compatibility
-------------

- ✔ Mac OSX
- ✔ Unix-derived systems (CentOS, Debian etc.)
- ? Windows - Not tested at this time

Example Usage
-------------

This repository comes with a simple [todo example](examples/todo/), along with an accompanying [article]() which discusses how the tool can be used in its entirety.
If however, you wish to quickly get up and running, you can add 'conductor' to your root composer.json file and then configure/save the following YAML file to 'conductor.yml'.

``` yaml
conductors:
    artifacts_repository: conductor
    packages:
        - package/*
```

Once this has been completed you can include the following boilerplate (with paths corrected for your setup) to each internal packages composer.json file.

``` json
{
    "scripts": {
        "pre-install-cmd": [
            "../../bin/conductor update -c ../.."
        ],
        "pre-update-cmd": [
            "../../bin/conductor update -c ../.."
        ],
        "pre-autoload-dump": [
            "../../bin/conductor symlink -c ../.."
        ],
        "post-update-cmd": [
            "../../bin/conductor fix-composer-lock -c ../.."
        ]
    },
    "repositories": [
        {
            "type": "conductor",
            "url": "../../conductor"
        }
    ]
}
```

Online Material
---------------

- [UK Symfony Meetup - Composer in monolithic repositories](http://www.meetup.com/symfony/events/192889222/)
- [MyBuilder Tech - Using Conductor, by example]()

TODO
----

- [ ] Create a command similar to 'brew doctor', checking for compatibility and dependency management
- [ ] Create a command which 'prunes' out out-of-date and unused archived conductors
