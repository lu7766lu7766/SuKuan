# 使用 php:7.3-apache 作為基礎鏡像
FROM php:7.3-apache

# 安裝必要的依賴
RUN apt-get update && apt-get install -y \
  gnupg2 \
  apt-transport-https \
  && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
  && curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list \
  && apt-get update \
  && ACCEPT_EULA=Y apt-get install -y msodbcsql17 unixodbc-dev

# 安裝 sqlsrv 和 pdo_sqlsrv 擴展
RUN pecl install sqlsrv-5.9.0 \
  && pecl install pdo_sqlsrv-5.9.0 \
  && docker-php-ext-enable sqlsrv pdo_sqlsrv

# 啟用 Apache 的 rewrite 模組（可選，根據專案需求）
RUN a2enmod rewrite

# 設置工作目錄
WORKDIR /var/www/html

# 複製專案文件（可選，根據需求）
# COPY ./ /var/www/html

# 設置權限
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html