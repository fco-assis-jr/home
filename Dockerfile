# Use a imagem base PHP 8.2
FROM php:8.2-fpm

# Instale dependências de sistema e extensões PHP para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    wget \
    libaio1 \
    git \
    nano \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Copie e instale o Oracle Instant Client e o SDK
COPY instantclient-basiclite-linux.x64-12.2.0.1.0.zip /opt/oracle/
COPY instantclient-sdk-linux.x64-12.2.0.1.0.zip /opt/oracle/
RUN cd /opt/oracle && \
    unzip instantclient-basiclite-linux.x64-12.2.0.1.0.zip && \
    unzip instantclient-sdk-linux.x64-12.2.0.1.0.zip && \
    rm instantclient-basiclite-linux.x64-12.2.0.1.0.zip instantclient-sdk-linux.x64-12.2.0.1.0.zip && \
    cd instantclient_12_2 && \
    ln -s libclntsh.so.12.1 libclntsh.so && \
    echo "/opt/oracle/instantclient_12_2" > /etc/ld.so.conf.d/oracle-instantclient.conf && \
    ldconfig

# Instale a extensão oci8 com o Instant Client
RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/opt/oracle/instantclient_12_2 && \
    docker-php-ext-install oci8

# Instale o Node.js 18.x e npm para Vite
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Instale o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Defina o diretório de trabalho e copie o projeto Laravel
WORKDIR /var/www
COPY . .

# Instale as dependências do Laravel e do Vite
RUN composer install && npm install && npm run build

# Ajuste permissões para o Laravel
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage

# Exponha a porta para o Laravel
EXPOSE 8000

# Defina o comando de inicialização do Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
