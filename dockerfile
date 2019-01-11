FROM php:7.3.1-alpine

RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
    docker-php-ext-configure gd \
    --with-gd \
    --with-freetype-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ && \
    NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
    docker-php-ext-install -j${NPROC} gd && \
    docker-php-ext-install exif && \
    apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

WORKDIR /app

COPY . .

WORKDIR /app/example

EXPOSE 8088

CMD ["php", "-S", "0.0.0.0:8088"]