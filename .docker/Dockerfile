FROM alpine:edge

WORKDIR /edde

RUN apk update && apk upgrade apk-tools && apk add \
	nginx git curl ca-certificates unzip bash \
	php7 php7-fpm php7-pdo_sqlite php7-xdebug php7-gd php7-pdo_pgsql \
	php7-redis php7-pdo_mysql php7-bcmath php7-xml php7-curl php7-intl \
	php7-mbstring php7-xml php7-zip php7-zlib php7-openssl php7-json php7-phar \
	php7-fileinfo php7-tokenizer php7-dom php7-xmlwriter php7-xmlreader php7-ctype \
	openssh

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN composer global require hirak/prestissimo -q --prefer-dist --no-progress
RUN composer config --global repo.pacagist composer https://packagist.org

RUN echo "PermitRootLogin yes" >> /etc/ssh/sshd_config
RUN echo "zend_extension = xdebug.so" > /etc/php7/conf.d/xdebug.ini
RUN echo "root:1234" | chpasswd

COPY .docker/nginx.conf /etc/nginx/nginx.conf
COPY .docker/php-fpm.conf /etc/php7/php-fpm.d/zz-www.conf

COPY .docker/start.sh /edde/.docker/start.sh
RUN chmod +x /edde/.docker/start.sh

COPY composer.json .
COPY composer.lock .
COPY loader.php .
COPY phpunit.xml .
ADD src src
ADD tests tests

RUN composer install --prefer-dist --no-progress

RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log
RUN ln -sf /dev/stdout /var/log/php-fpm-access.log && ln -sf /dev/stderr /var/log/php-fpm-error.log

ENTRYPOINT ["/edde/.docker/start.sh"]

EXPOSE 80 22
