# Use a imagem base PHP 8.2
FROM php:8.2-fpm

# Instale as dependências do sistema e extensões PHP necessárias
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
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd tokenizer ctype json xml openssl \
    && rm -rf /var/lib/apt/lists/*

# Copie os arquivos do Oracle Instant Client e do SDK para o contêiner
COPY instantclient-basiclite-linux.x64-12.2.0.1.0.zip /opt/oracle/
COPY instantclient-sdk-linux.x64-12.2.0.1.0.zip /opt/oracle/

# Instale o Oracle Instant Client e o SDK
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

# Instale o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto Laravel
COPY . .

# Configurar arquivo .env
COPY .env.example .env

# Limite de memória para o Composer e instalação com cache limpo
RUN composer clear-cache && COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-interaction --verbose

# Ajusta permissões para o Laravel
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Define o comando de inicialização do Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
