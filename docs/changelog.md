# Changelog

!> Changelog will not display a little changes on API level (you can use `git` for that); main
purpose is to describe changes between generations of Edde and bigger conceptual changes.

## 5.0 (~2018)

Fresh restart with a lot of refactored stuff, mature Container, polished concepts and a version
in general available for public release. Also the very first version with full documentation.

Full support of Docker with [documentation](/getting-started/index) of a production usage.

Finally a version which could be considered polished enough for bigger spread.

## 4.0 - 4.5 (~2015-2017)

Generation of Edde which is basically starting to be mature enough for production use, still with
a lot of things which are suboptimal - mainly quite clever ORM, but with some flaws

First version with a specialization of pure backend development - this simplified a lot
of things in the framework and also brings default support for Docker so it's much more simple to
develop Edde itself or using Edde as a framework.

Main flaw of this generation was ORM itself as other components was not iterated too much.

## 3.0 (~2014)

This generation brings custom template engine and server side rendering support. A lot of interesting
stuff, but based on the wrong concepts. 

Some components are done in quite good way, but also there was some complications rendering framework
usable, but quite heavy.

Based on experience gained from this iteration an ongoing version was starting to have finally shape
of the production framework. 

As usual, there was some bugs, but Edde was stable enough to stand for some quite heavy projects, even
`component` related stuff complicated some parts of development.

## 2.0 (~2013)

A lot of improved stuff from the original library; not so much changes (as far as I remember). This was
kind of second generation of Edde, but also the very first iteration as a standalone framework with
all the features framework must have.

Still not mature enough - a lot of work is behind to take and a lot of components need it's iteration
to be production grade. 

## 1.0 (~2011)

The original release as library under [Nette Framework](https://nette.org/) with high specialization
on ORM level and way how to generate PHP classes and database structure from simple format
([neon](https://ne-on.org/)).

Very first iteration before the library started it's path to overgrow it's parent.
