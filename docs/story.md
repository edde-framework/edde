# Story

## Prolog

Edde was just a simple library build around Nette Framework to fill empty gaps and provide quite powerful
ORM layer. There was plenty code able to generate another code and provide some nice features to build a
database just from simple configuration file.

That was around 2011, it was the very first iteration trying to deal with database and relatively simple
query builder.

But there was some features and things in Nette making features from Edde incredibly hard to use and how
Nette was heavy (mainly with it's `RobotLoader`) and hard to use (dynamic classes registered in `Container`),
there was also some other conceptual problems even in Edde itself.

## The Idea

Create your very own framework isn't easy task - a lot of people was trying but also failing. There was
a list of required components and it wasn't nice look. So that was second iteration - heavily inspired by
Nette, but without the mess inside - a lot of things was much more easier, but a lot of things was also quite
strange or done in wrong way.

Iteration by iteration Edde was much more standalone and much less like Nette by concepts used. As it was
growing, some features was dropped as when there are [concepts](/ideas), it's not necessary to implement
all that bloatware.

## The Reason

Being bound to a parent framework was quite hard to maintain. Also initial set of components necessary to
make Edde standalone framework was a reason. But the most important is selection of concepts and decisions
made to make Edde really unique. It shares some common stuff, but those decisions are simplifying the code
to small and useful piece of software.

Yet another reason to make Edde standalone was to take something interesting into Open Source community. Even
there is no heavy marketing, webinars, trainings and other stuff, it has it's own unique place to exists:
Edde has small user-base which enables to iterate in incredible speed, because there is no need for backward
compatibility.

So here it is: free of charge, simple to use. Some other frameworks are quite overcomplicated, so it's not
easy to use them without expensive trainings, you could get a lot of certificates telling nothing and still
you cannot use framework of you dreams on it's full potential.

Edde is not like that. If there is something missing in this docs, ask for that and it will became available.

## The Motivation

A lot of PHP stuff is nice and going to be even better with new versions of PHP. That's nice. But there is
still quite big hole to fill - some fast and simple framework. But not simple and not usable for some big
scale application. Simple to provide foundation for the application and than cover your back during development
basically without being noticed it's there.

The motivation is mainly based on overcomplexity of the available frameworks and believe the application
runtime could be simple to use. Precisely selected concepts and philosophy of Edde is also pushing it
forward as it's not just clone of another framework (like others do), but it has ideas able to reason about
not taken from depths of unknown, but based on commonly used patterns everybody knows. Plus some other
ideas.  

## The Jump

The forth and fifth generation of Edde was a huge milestone bringing fresh air into the codebase and simplifying
the whole framework. New concepts has been discovered and a lot of bad practices eliminated allowed Edde to
take it's place on the market.

If you feel it's missing some "mandatory" features like `forms`, `templates` or even `cache`, it has some reason
why. We are in a modern age where Single Page Applications are much more common, thus it's not necessary to
keep those oldschool concepts in the framework. Development of an oldschool application could be done by
using some of the other frameworks. This is Edde. There is just backend for you magic.
