FROM node:15-alpine as builder

RUN npm install -g @angular/cli@11

WORKDIR /app

COPY ./package.json ./package-lock.json ./
RUN npm install

COPY ./ ./
RUN ng build --prod --localize

FROM nginx:1.17-alpine

COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY --from=builder /app/dist/adspons ./public
COPY --from=builder /app/dist/adspons/uk ./public
