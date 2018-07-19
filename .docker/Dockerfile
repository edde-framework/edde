FROM docker.x32.cz/tools/turtle

COPY .docker/rootfs /

RUN echo "PermitRootLogin yes" >> /etc/ssh/sshd_config
RUN echo "root:1234" | chpasswd
RUN echo "zend_extension = xdebug.so" > /etc/php7/conf.d/xdebug.ini

WORKDIR /edde

COPY . .

RUN chmod +x test.sh

RUN composer install --prefer-dist --no-progress

EXPOSE 80 22
