# Proxy {docsify-ignore-all}

If you know nginx or apache with it's virtual hosts, there is another kind of product able to take incoming requests and 
forward them to appropriate containers - in this case [HAProxy](http://www.haproxy.org/). There is some kind of
[dark magic](https://proxy.dockerflow.com/) behind which we'll use.

?> This is probably easiest setup you can use; another option is to setup HAProxy manualy in a good old fashion way. You
don't want to do that.

There is also a [tool](http://proxy-letsencrypt.dockerflow.com/) able to take care about certificates for free (for a limited
number of domains), which means you can run services requiring valid SSL certificates without any pain. Send thanks to the
service of [Let's Encrypt](https://letsencrypt.org/) for this great product! 

!> Be careful about **Let's Encrypt** as you can get certificates only for domains you own, thus it **deos not work** for a local
development! Also there are limit of number of domains you can use, so issue a certificate just for domains you really need to 
be SSL enabled. Remember that certificates free to use, but not **for free**; there is some [cost](https://letsencrypt.org/donate/)
all the times. 

## docker-compose.yml

This is a stack file for Proxy stack with a recommended way of configuration. You can adjust a lot of interesting flags, just have
a look into [documentation](https://proxy.dockerflow.com/config/). 

> Path is based on a storage schema previously discussed. 

?> **/mnt/storage/common/stack/docker-compose.yml**

```yaml
service: '3.0'
```

**Previous**: [Storage](/devops/server/storage) | **Next**: [Proxy](/devops/server/proxy)
