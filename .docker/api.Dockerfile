FROM alpine:edge

WORKDIR /opt/app

# system configuration and setup

RUN apk update && apk upgrade apk-tools && apk add \
	nginx curl ca-certificates bash \
	php7 php7-session php7-opcache \
	php7-bcmath php7-xml php7-curl php7-intl \
	php7-mbstring php7-xml php7-zip php7-zlib php7-openssl php7-json php7-phar \
	php7-fileinfo php7-tokenizer php7-dom php7-xmlwriter php7-xmlreader php7-ctype \
	php7-simplexml

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN composer global require hirak/prestissimo -q --prefer-dist --no-progress

COPY .docker/api.nginx.conf /etc/nginx/nginx.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

# end of a mandatory system configuration

COPY composer.json .
COPY composer.lock .
COPY sami.conf.php .

ADD src src

RUN composer install --prefer-dist -q --no-progress
RUN lib/bin/sami.php update sami.conf.php

COPY .docker/api.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
