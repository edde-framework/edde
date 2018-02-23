FROM alpine:edge

WORKDIR /opt/app

# system configuration and setup

RUN apk update && apk upgrade apk-tools && apk add \
	nginx curl ca-certificates unzip bash

COPY .docker/doc/nginx.conf /etc/nginx/nginx.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

# end of a mandatory system configuration

ADD src src

COPY .docker/doc/start.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
