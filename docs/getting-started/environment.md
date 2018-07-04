# Environment

!> Be careful about **Let's Encrypt** as you can get certificates only for domains you own, thus it **deos not work** for a local
development! Also there are limitation of number of domains you can use, so issue a certificate just for production (testing, 
staging) domains, **not** for reviews.

## Storage

Please read about [storage](/examples/storage/index) concept; it's necessary to keep going with the following stuff.

## Dockerflow Proxy

### docker-composer.yml

It's time to configure [Dockerflow Proxy](https://proxy.dockerflow.com/) for production deployment. This service will watch containers
and make automatic adjustments to [HAPRoxy](http://www.haproxy.org/) configuration, so you'll never make manual webserver setup again. 
