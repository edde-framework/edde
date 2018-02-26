FROM alpine:edge

WORKDIR /opt/app

# system configuration and setup

RUN apk update && apk add \
	nginx git curl ca-certificates bash gettext util-linux \
	nodejs yarn

COPY doc/.docker/nginx.conf /etc/nginx/nginx.conf

# end of a mandatory system configuration

COPY doc/package.json .
COPY doc/yarn.lock .

RUN yarn install --production --silent --non-interactive

ADD doc/assets assets
ADD doc/src src

RUN yarn run build
COPY favicon.ico public

COPY doc/.docker/start.sh /start.sh
RUN chmod +x /start.sh

ENTRYPOINT ["/start.sh"]

EXPOSE 80
