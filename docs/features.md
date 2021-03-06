# Features

> Edde is simple and powerful framework able to deliver convenient experience on backend development without messing with
unnecessary features. 

## Prolog

Why to use another PHP framework, instead of another one with huge community, a lot of QA on Stack Overflow and
other benefits missing on this project?

Maybe that's the benefit: because of kind of one-man-show project has much more straightforward approach, it's
more consistent and much simpler. Everything is publicly available, including this doc as a part of the
[repository](https://github.com/edde-framework/edde/tree/master/docs). At least framework will stay alive.

See a [story](/story) how long Edde is here, it's quite long reading because for now it's few years.

## Docker aware

Docker is quite important today, and so framework aware of this cool technology simplifying all the stuff around to
make container builds much more easy. This could be done for example by making services integral part of
[container](/components/container), thus available everywhere, so you can for example execute upgrades from URL.

> If you don't know Docker, it's time to have a look [around](/getting-started/index) this site to get some idea
what's this technology about and also have a look on [official site](https://docs.docker.com/).   

## Concepts

Edde is not yet another implementation of existing parts of another libraries or frameworks; it has it's own ideas,
it's own concepts. And because of concepts whole framework is simple and clear; a lot of wrong decisions was
already made (for example no package splitting or no [ORM](/components/orm) implementation). Also formally code
has quite high grade based on different tools with code insight.

> For more talk about concepts framework is using, you can see [Concepts](/ideas) and [Principles](/principles).

## Iterations

Because there is basically no backward-compatibility contract, Edde is usually learning from it's conceptual
bugs and flaws and growing into much more stable and clear framework. There is no lock on "this feature is 
nice enough, don't touch it".

A little story in short: there was a new ORM for 5th generation of Edde which was... ripped of before release
as there was major conceptual flaw. More [here](/components/orm).

## No ORM

Common framework are trying to be extremely cool with theirs ORMs which are the best over the globe. But if you ever
built an application, there was usually just one database engine. So why not to use it's native language which
**everybody** knows? Do you know SQL? Yes? And what about Edde proprietary ORM? Nope. That's the reason, why 
there is just a thin layer over a database connection providing just [necessary features](/components/storage) to
get and save data.

## No Middleware

It's quite common pattern used by all that cool frameworks, or at least trying to adopt this concept. Edde will
**never** use this as there is no [middleware](https://en.wikipedia.org/wiki/Middleware) in the framework - this
is a common nonsense take in the field by some unknown entity with quite huge popularity.

This concept usually creates hard to setup, hard to debug, hard to develop environment where you have to search
some deep places of you application to see, who is setting up some middleware here and there and how to disable
them just here, but not there and so on.

One of the main flaws of this concept is kind of onion approach where not every language is happy of. Current PHP
is much faster but older versions was crying when lambda functions were used. Also developer using debugger was
crying as an application flow was not predictable as on different places could be basically any kind of "random"
call.

Edde keeps things clear: everything is in hands of a developer which could simply see, what a controller is doing
without any surprising side effects. Thus even testing is much more simple and transparent.  

## No Lifecycle

When you're developing an application, you want to have a full control over the code. So when there is some piece
of code from the framework telling you how you should do this and that without an option to simply make exceptions,
it could be quite frustrating.

Edde is not forcing you any lifecycle. If you have a controller where is no response, your application will be silent.
If you throw and exception, an application will die in hard way. No gotchas. Everything is clear and transparent.
This decision was made mainly because you could opt-in for some lifecycle and create some base controller for that.
When you need, it's simple to bypass it.

This is also related to missing support for middleware. It's up to you to create whatever you need as usually requests
to your application are thin (shallow call stack). 
