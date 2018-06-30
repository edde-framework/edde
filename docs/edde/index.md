# Components

This is general documentation of pieces used in Edde; unlike the others, Edde aims
to be consistent, small and simple framework, thus I see as an anti-pattern using
packages for a framework.

Sections of describes behavior and ideas of individual parts of framework and reasons
why part exists or not. 

## What we have here

* [Dependency Container](/edde/container): Core feature of (basically any) framework; it provides
quite strong and complex dependency container built for real-world usage (do not search for
strange features).

* [Controllers](/edde/controllers): The way how to handle incoming requests from an user to
an application request; quite simple stuff with one incredibly annoying exception.

* [Exceptions](/edde/exceptions): Edde is using simple exception model; in general no body cares
about exceptions, but it could make development a bit easier.
