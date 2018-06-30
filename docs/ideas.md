# Concepts

Edde is built around key set of concepts more than relying on repeating code found all over the internet.
This article explains some decisions made to keep life a bit easier despite it could be strange on the first
look. It itsn't :)!

## Environments

There is nothing like "production", "development" and other shits complicating development of an application. Not
in a traditional way. Usually it's enough to use environment variables to provides values for you application during
build or startup of [Docker](/docker) as it's much simpler, than to fight with different files on different platforms and 
stages.

## Configuration

### loader.php as the only God

This file is intended to provide full configuration of you application; it's not necessary to move code configuration
to any yaml, neon, json, whatever format: it's PHP, let's use PHP with all it's powers. 

This file contains interface to implementation bindings (if necessary) and [configurators](/edde/configurators) for some services.

### config.ini(.template)

Development, production, doesn't matter as this file should contain just environment variables expanded during 
container build/startup. Config should provide just basic scalar values in sections (usually for database), nothing more. 

## Dependencies

A more can be found in article about [Container](/edde/container) but in short Edde does **not** support other
than inject by a method. This is prevention of constructor hell and also inject method could be simply moved
to a trait and than reused by one line of code.

## Configurators

General article is available [here](/edde/configurators); Edde is using deferred creation of all services, which means also
deferred time of service configuration. That's reason for `Configurators` which are responsible for service configuration
on request.

## Logger

`LogService` is done in a bit different way in Edde way incompatible with that strange [PSR](https://github.com/php-fig/log) thing out there.
Main reason was to use one service through whole application and choose logger on fly. For example you can simply log to `stderr` and if
you decide you want to disable this logger, it's enough to disable individual logger handling this type of `tag`. Such you
can do logging to database, file or any kind of storage and simply enable/disable this logger in any environment without touching
code. 

## Backend Only

There are plenty of [template](https://twig.symfony.com/) [engines](https://latte.nette.org/en/) around the world; it's not
necessary to create another one to build huge piece of crap on server side solving client related problems; in general
Edde is using concept far away from traditional way how to build oldschool application - you application should be just backend
for another piece of software making UI for it.

That means Edde will **never** implement any kind of Control or template or another piece of thing again (yes, there was in some older
generation) to provide server-side rendering.

Yo, no session support too.
