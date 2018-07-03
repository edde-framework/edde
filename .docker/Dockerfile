FROM alpine:3.7

ENV LANG="en_US.UTF-8" \
    LANGUAGE="en_US:en" \
    LC_ALL="en_US.UTF-8"

COPY .docker/rootfs /

RUN apk update && apk upgrade apk-tools && apk add \
	runit nginx git curl ca-certificates unzip bash openssh nodejs npm \
	php7 php7-fpm php7-pdo_sqlite php7-xdebug php7-gd php7-pdo_pgsql \
	php7-redis php7-pdo_mysql php7-bcmath php7-xml php7-curl php7-intl \
	php7-mbstring php7-xml php7-zip php7-zlib php7-openssl php7-json php7-phar \
	php7-fileinfo php7-tokenizer php7-dom php7-xmlwriter php7-xmlreader php7-ctype

# init scripts must be executable
RUN ["chmod", "0750", "-R", "/etc/service"]
RUN ["chmod", "0750", "-R", "/etc/bootstrap"]
# setup executable on boot script
RUN ["chmod", "+x", "/bootstrap.sh"]

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN composer global require hirak/prestissimo -q --prefer-dist --no-progress
RUN composer config --global repo.pacagist composer https://packagist.org

RUN echo "PermitRootLogin yes" >> /etc/ssh/sshd_config
RUN echo "root:1234" | chpasswd
RUN echo "zend_extension = xdebug.so" > /etc/php7/conf.d/xdebug.ini

WORKDIR /edde

COPY composer.json .
COPY composer.lock .
COPY loader.php .
COPY phpunit.xml .
ADD docs docs
ADD src src
ADD tests tests

COPY test.sh .
RUN chmod +x test.sh

RUN composer install --prefer-dist --no-progress

ENTRYPOINT ["/bootstrap.sh"]

EXPOSE 80 22
