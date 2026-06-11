# STYLEUS CRM - Development Workflow Guide

## 🔄 Синхронизация локальной и production среды

---

## Рекомендуемый подход: Git + GitHub/GitLab

### Преимущества:
- ✅ Версионный контроль
- ✅ История изменений
- ✅ Откат к предыдущим версиям
- ✅ Командная работа
- ✅ Автоматический deployment

---

## 📋 Вариант 1: Git Workflow (РЕКОМЕНДУЕТСЯ)

### Шаг 1: Инициализация Git репозитория

```bash
# На вашем Mac в директории проекта
cd "/Users/sergeirybakov/StyleUS-Tools/STYLEUS_CRM/Krayin CRM/styleuscrm"

# Инициализируйте Git (если еще не сделано)
git init

# Добавьте .gitignore
cat > .gitignore << 'EOF'
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
EOF

# Добавьте все файлы
git add .
git commit -m "Initial commit - STYLEUS CRM"
```

### Шаг 2: Создайте GitHub/GitLab репозиторий

**GitHub (рекомендую):**
1. Перейдите на https://github.com
2. Нажмите "New repository"
3. Название: `styleus-crm`
4. Выберите: Private (для безопасности)
5. Нажмите "Create repository"

```bash
# Подключите удаленный репозиторий
git remote add origin https://github.com/YOUR_USERNAME/styleus-crm.git
git branch -M main
git push -u origin main
```

### Шаг 3: Настройте deployment на сервере

**На сервере создайте deploy ключ:**

```bash
# Подключитесь к серверу
ssh root@45.55.62.115

# Создайте SSH ключ для GitHub
ssh-keygen -t ed25519 -C "deploy@styleus.us" -f ~/.ssh/github_deploy
cat ~/.ssh/github_deploy.pub
# Скопируйте вывод
```

**Добавьте Deploy Key в GitHub:**
1. GitHub Repository → Settings → Deploy keys
2. Add deploy key
3. Title: "Production Server"
4. Key: вставьте скопированный ключ
5. ✅ Allow write access (если нужно)
6. Add key

**Настройте Git на сервере:**

```bash
# На сервере
cd /var/www/styleus

# Инициализируйте Git
git init
git remote add origin git@github.com:YOUR_USERNAME/styleus-crm.git

# Настройте SSH
cat > ~/.ssh/config << 'EOF'
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/github_deploy
EOF

chmod 600 ~/.ssh/config

# Первый pull
git fetch origin main
git reset --hard origin/main
```

### Шаг 4: Workflow для разработки

#### Локальная разработка:

```bash
# 1. Внесите изменения в код
# 2. Протестируйте локально
php artisan serve

# 3. Закоммитьте изменения
git add .
git commit -m "Описание изменений"

# 4. Отправьте на GitHub
git push origin main
```

#### Обновление production:

```bash
# Подключитесь к серверу
ssh root@45.55.62.115

# Перейдите в директорию
cd /var/www/styleus

# Включите maintenance mode
php artisan down

# Получите последние изменения
git pull origin main

# Обновите зависимости (если нужно)
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# Выполните миграции (если есть новые)
php artisan migrate --force

# Очистите кеш
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Перезапустите очереди
sudo supervisorctl restart styleuscrm-worker:*

# Выключите maintenance mode
php artisan up
```

---

## 📋 Вариант 2: Автоматический Deployment (GitHub Actions)

Создайте файл `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: 45.55.62.115
        username: root
        key: ${{ secrets.SSH_PRIVATE_KEY }}
        script: |
          cd /var/www/styleus
          php artisan down
          git pull origin main
          composer install --no-dev --optimize-autoloader
          npm ci --production
          npm run build
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo supervisorctl restart styleuscrm-worker:*
          php artisan up
```

**Настройка GitHub Secrets:**
1. Repository → Settings → Secrets and variables → Actions
2. New repository secret
3. Name: `SSH_PRIVATE_KEY`
4. Value: содержимое вашего приватного SSH ключа

**Теперь при каждом `git push` код автоматически развернется на сервере!**

---

## 📋 Вариант 3: Rsync (Быстрая синхронизация)

Для быстрой синхронизации без Git:

```bash
# Создайте скрипт sync.sh на Mac
cat > sync-to-production.sh << 'EOF'
#!/bin/bash

echo "🔄 Syncing to production server..."

rsync -avz --exclude 'node_modules' \
           --exclude 'vendor' \
           --exclude '.git' \
           --exclude 'storage/logs/*' \
           --exclude 'storage/framework/cache/*' \
           --exclude 'storage/framework/sessions/*' \
           --exclude 'storage/framework/views/*' \
           --exclude '.env' \
           . root@45.55.62.115:/var/www/styleus/

echo "✅ Sync complete!"
echo "🔄 Clearing cache on server..."

ssh root@45.55.62.115 << 'ENDSSH'
cd /var/www/styleus
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart styleuscrm-worker:*
ENDSSH

echo "✅ Done!"
EOF

chmod +x sync-to-production.sh
```

**Использование:**

```bash
./sync-to-production.sh
```

---

## 📋 Вариант 4: Laravel Forge (Платный, но простой)

**Laravel Forge** (https://forge.laravel.com) - $12/месяц

### Преимущества:
- ✅ Автоматический deployment при push в Git
- ✅ Управление через веб-интерфейс
- ✅ SSL сертификаты в один клик
- ✅ Мониторинг и логи
- ✅ Scheduled jobs и queue workers
- ✅ Резервное копирование

### Как использовать:
1. Зарегистрируйтесь на forge.laravel.com
2. Подключите ваш DigitalOcean аккаунт
3. Подключите GitHub репозиторий
4. Forge автоматически развернет и будет обновлять приложение

---

## 🗄️ Синхронизация базы данных

### Локальная → Production (ОСТОРОЖНО!)

```bash
# Экспорт локальной БД
mysqldump -u root laravel-crm > local_db.sql

# Загрузка на сервер
scp local_db.sql root@45.55.62.115:/tmp/

# Импорт на сервере
ssh root@45.55.62.115
mysql -u styleuscrm_user -p styleuscrm_prod < /tmp/local_db.sql
rm /tmp/local_db.sql
```

### Production → Локальная (для тестирования)

```bash
# На сервере создайте дамп
ssh root@45.55.62.115 "mysqldump -u styleuscrm_user -p styleuscrm_prod > /tmp/prod_db.sql"

# Скачайте на Mac
scp root@45.55.62.115:/tmp/prod_db.sql .

# Импортируйте локально
mysql -u root laravel-crm < prod_db.sql

# Очистите
ssh root@45.55.62.115 "rm /tmp/prod_db.sql"
rm prod_db.sql
```

---

## 📁 Синхронизация файлов (uploads, storage)

### Production → Локальная

```bash
# Скачать файлы с production
rsync -avz root@45.55.62.115:/var/www/styleus/storage/app/public/ \
           ./storage/app/public/
```

### Локальная → Production

```bash
# Загрузить файлы на production
rsync -avz ./storage/app/public/ \
           root@45.55.62.115:/var/www/styleus/storage/app/public/
```

---

## 🔐 Важные правила безопасности

### ⚠️ НИКОГДА не синхронизируйте:

- ❌ `.env` файлы (разные настройки для local и production)
- ❌ `vendor/` и `node_modules/` (устанавливаются через composer/npm)
- ❌ `storage/logs/` (логи разные)
- ❌ `storage/framework/cache/` (кеш разный)

### ✅ Всегда синхронизируйте:

- ✅ Исходный код (PHP, JS, CSS)
- ✅ Миграции базы данных
- ✅ Конфигурационные файлы
- ✅ Публичные assets

---

## 🎯 Рекомендуемый workflow для вас

**Для начала (простой):**
1. Используйте **Git + GitHub** для версионного контроля
2. Используйте **rsync скрипт** для быстрой синхронизации
3. Вручную запускайте команды на сервере после обновления

**Когда освоитесь (продвинутый):**
1. Настройте **GitHub Actions** для автоматического deployment
2. Или используйте **Laravel Forge** для полной автоматизации

---

## 📝 Быстрая шпаргалка

### Ежедневная разработка:

```bash
# 1. Локально: внесите изменения
# 2. Локально: тестируйте
php artisan serve

# 3. Локально: коммит
git add .
git commit -m "Feature: описание"
git push origin main

# 4. На сервере: обновите
ssh root@45.55.62.115
cd /var/www/styleus
php artisan down
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:cache
php artisan up
```

### Быстрая синхронизация (без Git):

```bash
./sync-to-production.sh
```

---

## 🆘 Откат изменений

Если что-то пошло не так:

```bash
# На сервере
cd /var/www/styleus
git log  # посмотрите историю коммитов
git reset --hard COMMIT_HASH  # откатитесь к нужному коммиту
php artisan config:cache
php artisan up
```

---

**Какой подход хотите использовать?** Рекомендую начать с Git + rsync, а потом перейти на автоматизацию.
