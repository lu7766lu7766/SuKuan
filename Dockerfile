FROM namoshek/php-mssql:7.3-fpm

# 安裝 GD 擴展所需的依賴
RUN apk add --no-cache \
  freetype-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd