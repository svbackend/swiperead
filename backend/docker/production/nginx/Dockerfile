FROM nginx:1.17-alpine

ENV BUILD_NO=11111

COPY ./docker/common/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY ./public ./public
