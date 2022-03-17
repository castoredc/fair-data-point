FROM nginx:1.17-alpine as dev

WORKDIR /home/castor/fdp

COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/fdp.castoredc.local.crt /etc/nginx/fdp.castoredc.local.crt
COPY docker/nginx/fdp.castoredc.local.key /etc/nginx/fdp.castoredc.local.key

FROM dev