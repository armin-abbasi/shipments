FROM nginx:1.18


RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

RUN touch /var/run/nginx.pid && \
  chown -R www:www /var/run/nginx.pid && \
  chown -R www:www /var/cache/nginx

#COPY ./conf.d/nginx.conf-prod /etc/nginx/nginx.conf
COPY ./conf.d/app.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www

USER www
