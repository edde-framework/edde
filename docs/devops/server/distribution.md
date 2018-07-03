# Distribution

This question could became brutal flame, but I'll take some general stuff related to
Docker as the starting point, which distribution could be useful for Docker host.

**Some requirments are**:
* **lightweight**: basically configuration less as it's not desirable to make some
changes in a target system 
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

## Fedora
