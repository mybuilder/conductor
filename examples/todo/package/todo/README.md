To-do Package
=============

Contains the core domain logic required to represent and list tasks stored
within an implemented repository.

Service
-------

The supplied service provides functionality to add new tasks, along with a
projection of all present task titles.

Repository
----------

This package is not concerned with how the collection of tasks is stored within
the particular use-case.
All that is required is that their be an implementation abiding by the
'TaskRepository' interface when instantiating the service.
