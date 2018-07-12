FROM docker.x32.cz/tools/turtle:v1.0.0

COPY .docker/rootfs /

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
