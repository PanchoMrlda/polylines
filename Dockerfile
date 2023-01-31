FROM php:8.0-fpm-alpine3.15

# Copy composer.lock and composer.json
COPY src/composer.json src/composer.loc? /polylines/

# Copy package.json and package-lock.json
COPY src/package.json package-lock.jso? /polylines/

# Set working directory
WORKDIR /polylines

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions mbstring pdo_mysql zip exif pcntl gd

# Install dependencies
RUN apk add --update --no-cache \
    build-base \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    musl-locales \
    zip \
    libzip-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    bash

# Install composer
RUN apk add composer

# Install npm
RUN apk add --update nodejs npm

# Add user for laravel application
RUN addgroup -g 1000 polylines_user && \
    adduser -D -u 1000 -G polylines_user polylines_user

# Copy existing application directory contents
COPY . /polylines

# Copy existing application directory permissions
COPY --chown=polylines_user:polylines_user . /polylines

# Change current user to polylines_user
USER polylines_user

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
