FROM alpine:edge

WORKDIR /opt/app

# system configuration and setup

RUN apk update && apk add \
	nginx git curl ca-certificates bash \
	nodejs yarn

COPY doc/.docker/nginx.conf /etc/nginx/nginx.conf

# end of a mandatory system configuration

COPY favicon.ico public

COPY doc/.docker/start.sh /start.sh
RUN chmod +x /start.sh

ENTRYPOINT ["/start.sh"]

EXPOSE 80
