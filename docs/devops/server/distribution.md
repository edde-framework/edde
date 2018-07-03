# Distribution {docsify-ignore-all}

This question could became brutal flame, but I'll take some general stuff related to
Docker as the starting point, which distribution could be useful for Docker host.

**Some key requirements are**:
* **lightweight**: basically no configuration after (during) installation as it's not
desirable to make some changes in the target system 
* **modern**: because Docker sometimes could cause quite big pain in the ass, it's good
to have access to hotfixes and new versions; good distribution should provide at least
current stable version of Docker.
* **small footprint**: this is related also to security - running system should not take
basically no resources (up to 100-200MB of ram is too much); when there are no running
services, attach surface is also small
* ...and some others

## Ubuntu

**Big nope**: Ubuntu is a good distribution for oldschool approach of server setup as
there are a lot of stuff solved on ServerFault and on other sites. But we want to run just
Docker and nothing else; also footprint and a lot of stuff by default installed is
not desirable.

!> As Ubuntu is quite good distro, which can access new features, messing with setup
after installation is pushing it out of the list.

## Debian

**Nope, less than Ubuntu**: Debian is very strict and very secure distribution based on
stable releases of packages. This makes o lot of pain where it cames to new features;
it has similar problems as Ubuntu, but it's not usable at all.

!> Because of sticking with stable packages, releasing new versions of required packages
takes a lot of time; there is still original problem with configuration after installation.

## Fedora

**Small nope**: Fedora is really good distribution and it's simply possible to run Docker
on it; some doesn't like UWF, some doesn't like SELinux, both could be simply useful with
some skill: but it leads to general problem - no system configuration.  

!> If you are a bit more conservative about new and "hi-tech" distros, you could take
Fedora se a good host (if you're masochist, there is also Atomic Host optimized for Docker).
General recommendation is not for Fedora; Docker installation is also doing some strange
things with filesystem making maintenance much harder then for others.

## Alpine Linux

**Yep**: Simple, small, secure - nothing more, no pains included. Only things which could
complicate things for oldschool admins are `BusyBox` and default `Ash` instead of Bash. This
distribution is incredibly lightweight able to take from 25MB - ~100MB of ram when running
(yes, without any services as it's intended).

?> Yes, this distribution is still close to the others by terms of usage (package system),
but it does not have it's drawbacks; also upgrades are simple and edge runners has ability
to simply switch to latest branches.   

## RancherOS

**Dude, gimme that!**: If you want true Docker experience and you want to get some more
experience setting things up, this could be a great choice. Throw away any experience from
the original DevOps world as this is quite different system. The original idea is to have
the system configuration less in runtime. There is one file called
[cloud-config](https://rancher.com/docs/os/v1.2/en/configuration/) responsible for all the
magic you need: IP address, ssh keys. Some other stuff too.

So when you reinstall the system, it's enough to keep your data save, take this config file
and you have won.

You can choose different [consoles](https://rancher.com/docs/os/v1.2/en/configuration/switching-consoles/)
(aka distro) when running, so you can even have one of previously mentioned system used.

?> Using this distribution on production system could be considered a bit experimental in
terms of experience you may need to take control over this system. In general it's recommended.

**Previous**: [Index](/devops/server/index) | **Next**: [Docker Swarm](/devops/server/docker-swarm)
