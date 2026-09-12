FROM node:24-alpine

RUN apk add yarn

WORKDIR /var/www/html

CMD yarn install && \
    yarn run watch
