FROM nginx:1.17-alpine

ENV BUILD_NO=11111

COPY ./common/nginx/snippets /etc/nginx/snippets
COPY ./production/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app
