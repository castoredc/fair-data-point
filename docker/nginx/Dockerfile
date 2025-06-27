FROM nginx:1.29-alpine@sha256:b2e814d28359e77bd0aa5fed1939620075e4ffa0eb20423cc557b375bd5c14ad as dev

WORKDIR /home/castor/fdp

COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/fdp.castoredc.local.crt /etc/nginx/fdp.castoredc.local.crt
COPY docker/nginx/fdp.castoredc.local.key /etc/nginx/fdp.castoredc.local.key

FROM dev
