FROM docker.x32.cz/edde-framework/elephant:latest

ENV \
	BACKEND_ROOT=/edde \
	BACKEND_PUBLIC_ROOT=/edde \
	ROOT_PASSWORD="root:1234" \
	USE_COMPOSER=true \
	USE_SSHD=true

COPY .docker/rootfs /

WORKDIR /edde
COPY . .
RUN chmod +x test.sh

EXPOSE 80 22
