# Components

This is general documentation of pieces used in Edde; unlike the others, Edde aims
to be consistent, small and simple framework, thus I see as an anti-pattern using
packages for a framework.

Sections of describes behavior and ideas of individual parts of framework and reasons
why part exists or not. 

## What we have here {docsify-ignore}

* [Configurators](/components/configurators): Quite unique and nice feature of Edde - simple way how
to setup services in deferred way.

* [Container](/components/container): Core feature of (basically any) framework; it provides
quite strong and complex dependency container built for real-world usage (do not search for
strange features).

* [Controllers](/components/controllers): The way how to handle incoming requests from an user to
an application request; quite simple stuff with one incredibly annoying exception.

* [Exceptions](/components/exceptions): Edde is using simple exception model; in general no body cares
about exceptions, but it could make development a bit easier.

* [Logging](/components/logging): Every application needs to keep logs. Not on filesystem though ;).

* [ORM](/components/orm): A little surprise here - story about the question if ORM is useful or not.

* [Schema](/components/schema): Another nice piece of code useful for data format description to keep
entities and data format separated.

* [Storage](/components/storage): That interesting part for everybody; how to save and retrieve you 
valuable data!

* [Upgrades](/components/upgrades): Migrations made easy. One simple class rule them all!
