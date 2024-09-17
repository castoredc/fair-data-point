FROM nginx:1.17-alpine@sha256:763e7f0188e378fef0c761854552c70bbd817555dc4de029681a2e972e25e30e as dev

WORKDIR /home/castor/fdp

COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/fdp.castoredc.local.crt /etc/nginx/fdp.castoredc.local.crt
COPY docker/nginx/fdp.castoredc.local.key /etc/nginx/fdp.castoredc.local.key

FROM dev
