FROM eddeframework/elephant:latest

ENV \
	BACKEND_ROOT=/edde \
	BACKEND_PUBLIC_ROOT=/edde \
	ROOT_PASSWORD=root:4321 \
	USE_SSHD=true

COPY .docker/rootfs /

WORKDIR /edde
COPY . .
RUN chmod +x test.sh

EXPOSE 80 22
