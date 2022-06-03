FROM php:7.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    locales \
    zip \
    vim \
    unzip \
    git \
    curl \
    apt-utils \
    libzip-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql \
&& docker-php-ext-enable pdo_mysql

RUN apt-get install -y zlib1g-dev libicu-dev g++ \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl \
&& docker-php-ext-configure pcntl \
&& docker-php-ext-install pcntl \
&& docker-php-ext-install zip

# Install Redis extension
RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/5.3.1.zip \
&& unzip /tmp/redis.tar.gz && rm -r /tmp/redis.tar.gz && mkdir -p /usr/src/php/ext \
&& mv phpredis-* /usr/src/php/ext/redis && docker-php-ext-install redis && docker-php-ext-enable redis

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Start as root
USER root
###########################################################################
# non-root user:
###########################################################################
# Add a non-root user to prevent files being created with root permissions on host machine.
ARG PUID=1001
ENV PUID ${PUID}
ARG PGID=1001
ENV PGID ${PGID}
ARG COMPOSE_PROJECT_NAME

RUN set -xe; \
groupadd -g ${PGID} ${COMPOSE_PROJECT_NAME} \
&& useradd -u ${PUID} -g ${COMPOSE_PROJECT_NAME} -m ${COMPOSE_PROJECT_NAME}\
&& usermod -p "*" ${COMPOSE_PROJECT_NAME} -s /bin/bash \
&& groupadd docker \
&& usermod -aG docker ${COMPOSE_PROJECT_NAME}

# Getting composer's executable file
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Set the timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo "${TIMEZONE}" > /etc/timezone

COPY ./aliases.sh /root/aliases.sh
COPY ./aliases.sh /home/www/aliases.sh

RUN sed -i 's/\r//' /root/aliases.sh && \
    sed -i 's/\r//' /home/www/aliases.sh && \
    chown www:www /home/www/aliases.sh && \
    echo "" >> ~/.bashrc && \
    echo "# Load Custom Aliases" >> ~/.bashrc && \
    echo "source ~/aliases.sh" >> ~/.bashrc && \
	echo "" >> ~/.bashrc

COPY ./php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY ./project/ /var/www/
COPY ./project/.env /var/www/.env

RUN chown -R www:www /var/www
RUN chmod +x /var/www/entrypoint.sh

USER www

RUN echo "" >> ~/.bashrc && \
    echo "# Load Custom Aliases" >> ~/.bashrc && \
    echo "source ~/aliases.sh" >> ~/.bashrc && \
	echo "" >> ~/.bashrc

EXPOSE 9000

ENTRYPOINT ["/var/www/entrypoint.sh"]
CMD ["run"]