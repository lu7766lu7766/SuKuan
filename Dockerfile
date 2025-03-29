FROM namoshek/php-mssql:7.3-fpm

# 安裝 GD 擴展所需的依賴
RUN apt-get update && apt-get install -y \
  libfreetype6-dev \
  libpng-dev \
  libjpeg62-turbo-dev \
  && docker-php-ext-configure gd \
  --with-freetype-dir=/usr/include/ \
  --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd

# 安裝其他依賴（根據你的需求）
RUN apt-get install -y \
  libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql

# 設置工作目錄
WORKDIR /var/www/html

# 複製專案文件
COPY ../ /var/www/html

# 設置權限
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html