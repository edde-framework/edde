# Distribution {docsify-ignore-all}

This question could became brutal flame, but I'll take some general topics related to
Docker as the starting point which distribution could be useful as a Docker host.

**Some key requirements are**:
* **lightweight**: basically no configuration after (during) installation as it's not
desirable to make any changes in the target system 
* **modern**: because Docker sometimes could cause quite **big** pain in the ass, it's
good to have access to hotfixes and new versions; good distribution should provide at least
current stable version of Docker
* **small footprint**: this is related also to security - running system should not take
basically no resources (up to 100-200MB of ram is too much); with no running services
you don't need attach surface is much smaller
* **instant reinstallation**: when there is a failure you should be able to start a fresh
instance of the system in minutes up to fully running state
* ...and some others

## Ubuntu

**Big nope**: Ubuntu is a good distribution for oldschool approach of server setup as
there are a lot of stuff solved on ServerFault and on other sites. But we want to run just
Docker and nothing else; also bigger footprint and a lot of stuff by running by default is
not desirable.

!> As Ubuntu is quite good distro which can access new features, messing with setup
after installation is pushing it out of the list.

## Debian

**Another nope**: Debian is very strict and secure distribution based on stable releases
of packages. This makes o lot of pain where it comes to new features; it has similar problems
as Ubuntu but on a bit higher level as new versions are rolling out in very slow fashion.

!> Host should be as stable as possible, but this depends on luck related to Docker version
available: if there is some bug fixed in a bit newer version, you have to wait years before
Debian will adapt it. Nope.

## Fedora

**Small nope**: Fedora is really good distribution and it's simply possible to run Docker
on it; some doesn't like UWF, some doesn't like SELinux, both could be simply useful with
some skill: but it leads to the basic failure: no configuration of magical problems appearing
after installation of Docker.   

!> If you are a bit more conservative about new and "hi-tech" distros, you could take
Fedora se a good host (if you're masochist, there is also Atomic Host optimized for Docker).
But it's still recommended to go around Fedora as it could be much harder to maintain Docker
than on other distros.

## Alpine Linux

**Yep**: Simple, small, secure - nothing more, no pains included. What could complicate things
for oldschool admins are `BusyBox` and default `Ash` instead of `Bash`. This distribution is
incredibly lightweight able to take from `25-100MB` of ram when running (yes, without any enabled
services by default as it's intended).

?> Yes, this distribution is still close to the others by terms of usage (package system and quite
standard environment), but it does not have it's drawbacks; also upgrades are simple and edge
runners has ability to simply switch to latest branches. That means Docker is getting updates
too.    

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

## Conclusion

At the end doesn't matter which distro you'll choose as setup described in this guide is build
on system and data separation so it's incredibly simple to switch between installation to suit
your needs.

If you're a bit more brave, use RancherOS, if a bit less, go Alpine Linux way.
