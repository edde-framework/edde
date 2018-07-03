# Concepts

Edde is built around key set of concepts more than relying on repeating code found all over the internet.
This article explains some decisions made to keep life a bit easier despite it could be strange on the first
look. It itsn't :blush: !

> Important note about concepts: Edde is trying to use some pieces of design patterns, but also it makes some
choices between them based on good practices or just on experience. Despite it's quite high quality, Edde is
not **the best** framework ever as there are plenty of things continually evolving and improving.

## Read-first Code

Even sometimes there are some pieces of code which are a bit less readable (the kind of "hacky code"), codebase in
general aims to be clear for reading without any strange rules like `80` or `120` character limit as this involves
a lot of strange pieces of code making reading much harder.

> There are some arguments for code-reviews and other tools, but who cares in the age of wide-screens to cut source
code to some synthetic limit of A4 paper. In general you'll see code much longer than any diff or code review.

## Environments

There is nothing like "production", "development" and other shits complicating development of an application. Not
in a traditional way. Usually it's enough to use environment variables to provides values for you application during
build or startup of [Docker](/docker) as it's much simpler, than to fight with different files on different platforms and 
stages.

> **Recommendation**: If you are not familiar with [Docker](/docker), please read the piece of docs here or see
[official documentation](https://docs.docker.com/) as Docker is simplest way how to run any kind of application and
it will be used through this documentation. 

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
than injects by a method. This is prevention of constructor hell and also inject method could be simply moved
to a trait and than reused by one line of code.

> To keep things clear and simple, it's useful to create trait per service you use in you application which will contain
property and inject method; this has initial drawback as for one service it's necessary to create interface, implementation,
register it to `loader.php` and make a trait, but it will lead to much more clear code.

## Configurators

General article is available [here](/edde/configurators); Edde is using deferred creation of all services, which means also
deferred time of service configuration. That's reason for `Configurators` which are responsible for service configuration
on request.

> This mechanism is useful when you want to create one service, but it's not necessary to create and setup whole tree of
services: for example, you have service working with `Storage`, but creation of that service will not make a connection to
database before you actually "touch" storage. 

## Logger

`LogService` is done in a bit different way in Edde way incompatible with that strange :astonished: [PSR](https://github.com/php-fig/log)
thing out there. Main reason was to use one service through whole application and choose logger on fly. For example you can simply log to
`stderr` and if you decide you want to disable this logger, it's enough to disable individual logger handling this type of `tag`. Such you
can do logging to database, file or any kind of storage and simply enable/disable this logger in any environment without touching
code. 

> Even it could be similar to other products, simple log service through whole application based on tags is quite powerful
as it's not necessary to mess up with container to provide individual loggers to different parts of the application.

## Backend Only

There are plenty of [template](https://twig.symfony.com/) [engines](https://latte.nette.org/en/) around the world; it's not
necessary to create another one to build huge piece of crap on server side solving client related problems; in general
Edde is using concept far away from traditional way how to build oldschool application - you application should be just backend
for another piece of software making UI for it.

That means Edde will **never** implement any kind of Control or template or another piece of thing again (yes, there was in some older
generation) to provide server-side rendering.

Yo, no session support too.

> Hard decision made by experience in the field: frontend stuff does not belong to backend, a lot of things is much more easier
without messing with them on backend.

## No Schemas

Sometimes framework tend to force users to use their's internal Entities, schemas, whatever to implement some piece of functionality.
This was quite hard to solve, but Edde in general doesn't do this. Even it's really simple to create [Schema](/edde/schema) and extend it,
Edde does not provide any; better is good quality documentation than some strangely prepared schemas.  

> The original attempts to use Entities or Schemas defined by framework leaded to some edge situations forced user to copy-paste
original entity and made some modification breaking whole concept. That's reason for this decision - user should provide his own
implementation of data model. 

## No PSR Support

In general I think that whole [PSR Group](https://www.php-fig.org/psr/) is wrong in principle, because they're trying to solve
fragmentation of frameworks split into smaller packages, solve interoperability between them and all problems of the world in
general. There are a lot of things which are not done in good way or it's ended in the half way.

This opinion is maybe a bit outside of PHP world mainstream, but prefer to have clean API than some kind of pseudo compatibility
leading to some crappy code around.  

Concrete arguments could be found [here](/psr).

> Even it could be considered as a nice attempt to make some stuff around PHP and create some compatibility layer, dependency on
some kind of "authority" with some people defining theirs stuff based on different experience and requirements is not a good idea.
