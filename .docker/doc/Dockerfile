FROM alpine:edge

WORKDIR /opt/app

# system configuration and setup

RUN apk update && apk upgrade apk-tools && apk add \
	nginx git curl ca-certificates unzip bash nodejs nodejs-npm

RUN npm install -g spike

COPY .docker/doc/nginx.conf /etc/nginx/nginx.conf

# end of a mandatory system configuration

COPY .docker/doc/start.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
