FROM docker.x32.cz/tools/turtle

COPY .docker/rootfs /

WORKDIR /edde
COPY . .
RUN chmod +x test.sh

EXPOSE 80 22
