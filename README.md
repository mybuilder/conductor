[![Build Status](https://secure.travis-ci.org/mybuilder/conductor.svg?branch=master)](http://travis-ci.org/mybuilder/conductor)

![Conductor](logo.png)

Conductor
=========

This tool allows you to manage isolated, internal [Composer](https://getcomposer.org/) packages within a single, monolithic repository.
Separating units of code based on directory structure, as opposed to at the repository level, maintains a single source of truth whilst providing the benefits of clearly defined component boundaries.

When would you use it?
----------------------

You would use this tool in a project setting where multiple separate applications co-exist (i.e. admin, frontend and mobile-api).
Within this context each application will share code, such as business logic, to provide the end solution.

An example project repository structure that we use in-kind is shown below:

```bash
├── app/
│   ├── admin
│   │   ├── src/
│   │   ├── tests/
│   │   └── composer.json
│   ├── frontend
│   │   ├── src/
│   │   ├── tests/
│   │   └── composer.json
│   └── mobile-api
│       ├── src/
│       ├── tests/
│       └── composer.json
├── artifact/
├── bin
│   └── conductor
├── package
│   ├── bar
│   │   ├── src/
│   │   ├── tests/
│   │   └── composer.json
│   └── foo
│       ├── src/
│       ├── tests/
│       └── composer.json
├── composer.json
└── conductor.yml

```

As you can see the root-level composer.json file is only used for uniform tooling - so no project specific code should be stored at this level.
The business logic is contained within each of the isolated packages, with the delivery supplied via the 'app' directory.

Compatibility
-------------

- ✔ Mac OSX
- ✔ Unix-derived systems (CentOS, Debian etc.)
- ? Windows - Not tested at this time

Examples
--------

At this time the project comes with a simple [todo example](examples/todo/) which illustrates how to use Conductor in it's entirety.

Further Reading
---------------

- [UK Symfony Meetup - Composer in monolithic repositories](http://www.meetup.com/symfony/events/192889222/)

---

Created by [MyBuilder](http://www.mybuilder.com/) - Check out our [blog](http://tech.mybuilder.com/) for more insight into the open-source projects we release
