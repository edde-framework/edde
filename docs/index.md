# About

[![pipeline status](https://git.x32.cz/edde-framework/edde/badges/master/pipeline.svg)](https://git.x32.cz/edde/edde/commits/master)
[![coverage report](https://git.x32.cz/edde-framework/edde/badges/master/coverage.svg)](https://git.x32.cz/edde/edde/commits/master)

## The Site

There is a lot of stuff not related to Edde or PHP at all; purpose of this site is to give a developer full guide how to build
an application from the very beginning. That means the [environment](/devops/server/index), [tools](/devops/index) being used
and even [workflow](/examples/workflow/index) of the project.

> So this documentation could help you even establish basic CI/CD pipeline, help setup server and run own instance of GitLab using
> modern concept of so called DevOps 2.0.

## The Framework

Edde itself is the littlest part of an application development: you will find a [lot of stuff](/getting-started/index) here how
to build an application from scratch using Docker and how to run very first stuff inside using Edde.

Basic idea is to help developer to create his application not to solve puzzles of the frameworks and spent more time on google,
forums and support sites. That's by the way just another reason why to use Edde; compared to your application it's basically
invisible.

!> Keep in mind that Edde is following different path than the others in many aspects of the framework; Edde is aware of your
code, so you can do things in the way you like without messing with any complex setups or heavy cli commands. It's just kind of
bigger library which covers your back.

## The Purpose

We're already in quite modern age with buzzwords like [SPA](https://en.wikipedia.org/wiki/Single-page_application) and other 
interesting technologies making a lot of current frameworks obsolete. There are some votes for server side rendering, but this
is still job of frontend stuff which does not belong to backend.

That means main purpose should be helping a developer create an application able to simply catch the request, simply respond in
very fast way and that's all. No template engines. No heavy ORMs. No sessions. The modern applications should be simple and
lightweight; but this is not talk about microservices.

!> So if you're searching for traditional framework, you'll be disappointed; Edde follows a lot of common stuff (yes, there are
controllers, ...), allows you to simply create an application, but it's not creating default vendor lock like the others do;
if you want to throw away Edde, you can - just after you'll see others are quite more simple in many basic things like Dependency
Container implementation.

## The Reason

Every time you start to write a new application there is at the beginning more stuff around the framework you're about to use than
things related to the application. Edde is trying to shorten this to the minimum - not by faking things behind cli commands making
stuff optically simple (yes, `artisan` I'm talking to you) or forcing you to use complex and unknown directory structure.
Just include `vendor/autoload.php` and you're happy.

!> General problem is Open Source: it costs a lot of money. Even Edde. But this framework is not built with complexity in mind to
provide thousands of different certificates proving your're super cool in that technology. Edde is intentionally simple. Because
it's not here because money but to make PHP world much better place.
