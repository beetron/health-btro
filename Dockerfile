# =====================================================
# Stage 1: Composer Dependency Installation
# =====================================================
FROM composer:2.8 AS vendor-builder
WORKDIR /app

COPY composer.json composer.lock ./

# Install production dependencies and optimize the autoloader
# Platform requirements are ignored here because they are handled in the final runtime stage
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs \
    --optimize-autoloader

# =====================================================
# Stage 2: Frontend Build Environment
# =====================================================
FROM node:22-bookworm-slim AS frontend-builder
WORKDIR /app

# Copy configuration files and source code required for asset building
COPY package.json package-lock.json* vite.config.js* tailwind.config.js* tsconfig.json* ./
COPY resources/ ./resources/
COPY public/ ./public/

# Copy only the Ziggy package from the vendor stage so TypeScript type-checking passes
COPY --from=vendor-builder /app/vendor/tightenco/ziggy/ ./vendor/tightenco/ziggy/

# Install dependencies and compile production assets via Vite
RUN npm ci && npm run build

# =====================================================
# Stage 3: Final Runtime Environment
# =====================================================
FROM php:8.4-fpm-bookworm AS runtime

WORKDIR /var/www/html

# Install Nginx, Supervisor, and critical system libraries for image manipulation and compression
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure and install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd bcmath zip pdo_mysql \
    && pecl install redis xhprof \
    && docker-php-ext-enable redis xhprof

# Copy application files from context
COPY --chown=www-data:www-data . .

# Copy compiled components from upstream build stages
COPY --from=vendor-builder --chown=www-data:www-data /app/vendor/ ./vendor/
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build/ ./public/build/

# Copy the decoupled nginx configuration from the repository root
COPY nginx.conf /etc/nginx/sites-available/default

# Write the Supervisor configuration to manage FPM and Nginx execution lifetimes
COPY <<EOF /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0
pidfile=/tmp/supervisord.pid

[program:php-fpm]
command=php-fpm
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true

[program:nginx]
command=nginx -g "daemon off;"
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
autorestart=true
EOF

# Ensure application storage and cache paths exist with appropriate execution rights
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Reconfigure Nginx workspace directories to allow read/write execution permissions for non-root users
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log \
    && mkdir -p /var/lib/nginx/body /var/lib/nginx/fastcgi /var/lib/nginx/proxy \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx /run

# Switch container execution context to standard application user
USER www-data

# Expose internal unprivileged service port
EXPOSE 8080

# Kubernetes container lifecycle health check integration targeting the custom endpoint
HEALTHCHECK --interval=15s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -f http://localhost:8080/healthz || exit 1

# Warm up production caches using the mounted Kubernetes environment variables before starting processes
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
