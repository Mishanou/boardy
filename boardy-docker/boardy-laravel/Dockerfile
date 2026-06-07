# ИСПОЛЬЗУЕМ АКТУАЛЬНУЮ СТАБИЛЬНУЮ ВЕРСИЮ PHP (Замена 8.2 на 8.3)
FROM php:8.3-fpm

# Устанавливаем системные зависимости
# ВАЖНО: Добавлен libssl-dev для сборки redis через pecl
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libssl-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Устанавливаем встроенные расширения PHP
RUN docker-php-ext-install pdo_mysql mbstring zip opcache bcmath gd

# Устанавливаем расширение Redis через PECL и активируем его
RUN pecl install redis && docker-php-ext-enable redis

# Устанавливаем Composer (копируем официальный бинарник из официального образа)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаем рабочую директорию внутри контейнера
WORKDIR /var/www

# Создаем системного пользователя для безопасного запуска Laravel (не под root)
RUN useradd -G www-data,root -u 1000 -d /home/www www
RUN mkdir -p /home/www/.composer && chown -R www:www /home/www

# Копируем исходный код приложения в контейнер
COPY . /var/www

# Меняем владельца директории на созданного пользователя www
RUN chown -R www:www /var/www

# Переключаемся на пользователя www
USER www

# Открываем порт 9000 для связи с Nginx
EXPOSE 9000

# Запускаем PHP-FPM сервер
CMD ["php-fpm"]