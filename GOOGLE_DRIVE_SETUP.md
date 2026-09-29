# STYLEUS CRM - Google Drive Integration Guide

## 📋 Полная инструкция по настройке Google Drive

---

## 🎯 Что вы получите:

- ✅ Автоматическое сохранение файлов в Google Drive
- ✅ Резервное копирование базы данных
- ✅ Доступ к файлам из любого места
- ✅ Неограниченное хранилище (Google Workspace)
- ✅ Интеграция с Gmail, Calendar, Sheets

---

## Шаг 1: Создание Google Cloud Project

### 1.1 Перейдите в Google Cloud Console

🔗 https://console.cloud.google.com

### 1.2 Создайте новый проект

```
1. Нажмите на выпадающий список проектов (вверху слева)
2. Нажмите "New Project"
3. Заполните:
   - Project name: STYLEUS CRM Integration
   - Organization: (ваша организация, если есть)
4. Нажмите "Create"
5. Подождите 10-20 секунд
```

### 1.3 Выберите созданный проект

```
Убедитесь, что в верхней панели выбран проект "STYLEUS CRM Integration"
```

---

## Шаг 2: Включение необходимых API

### 2.1 Перейдите в API Library

```
Меню (☰) → APIs & Services → Library
```

### 2.2 Включите следующие API:

Найдите и включите каждый API (нажмите "Enable"):

#### Обязательные:
- ✅ **Google Drive API**
- ✅ **Google Sheets API**
- ✅ **Gmail API**

#### Рекомендуемые:
- ✅ **Google Calendar API**
- ✅ **Google People API** (для контактов)
- ✅ **Google Apps Script API**

**Для каждого API:**
```
1. Найдите в поиске (например: "Google Drive API")
2. Нажмите на API
3. Нажмите "Enable"
4. Подождите активации
```

---

## Шаг 3: Создание OAuth 2.0 Client

### 3.1 Настройте OAuth Consent Screen

```
APIs & Services → OAuth consent screen

1. User Type: External (или Internal, если у вас Google Workspace)
2. Нажмите "Create"

3. Заполните форму:
   App name: STYLEUS CRM
   User support email: admin@styleus.us
   Developer contact: admin@styleus.us
   
4. Нажмите "Save and Continue"

5. Scopes - нажмите "Add or Remove Scopes":
   ✅ .../auth/drive
   ✅ .../auth/drive.file
   ✅ .../auth/spreadsheets
   ✅ .../auth/gmail.send
   
6. Нажмите "Save and Continue"

7. Test users (если External):
   Добавьте ваш email: admin@styleus.us
   
8. Нажмите "Save and Continue"
```

### 3.2 Создайте OAuth Client ID

```
APIs & Services → Credentials → "+ Create Credentials" → OAuth client ID

1. Application type: Web application
2. Name: STYLEUS CRM Web Client

3. Authorized JavaScript origins:
   https://crm.styleus.us
   
4. Authorized redirect URIs:
   https://crm.styleus.us/auth/google/callback
   https://crm.styleus.us/admin/google/callback
   
5. Нажмите "Create"

6. ВАЖНО: Скопируйте и сохраните:
   - Client ID
   - Client Secret
```

---

## Шаг 4: Создание Service Account

### 4.1 Создайте Service Account

```
APIs & Services → Credentials → "+ Create Credentials" → Service account

1. Service account details:
   Name: styleus-crm-service
   Description: Service account for STYLEUS CRM file operations
   
2. Нажмите "Create and Continue"

3. Grant this service account access to project:
   Role: Project → Editor
   
4. Нажмите "Continue"

5. Нажмите "Done"
```

### 4.2 Создайте ключ для Service Account

```
1. Найдите созданный Service Account в списке
2. Нажмите на него
3. Перейдите на вкладку "Keys"
4. "Add Key" → "Create new key"
5. Key type: JSON
6. Нажмите "Create"
7. Файл автоматически скачается
8. ВАЖНО: Сохраните этот файл безопасно!
```

---

## Шаг 5: Настройка Google Drive

### 5.1 Создайте папку в Google Drive

```
1. Перейдите на https://drive.google.com
2. Создайте папку: "STYLEUS CRM"
3. Внутри создайте структуру:
   
   STYLEUS CRM/
   ├── Leads/
   ├── Contacts/
   ├── Documents/
   ├── Quotes/
   ├── Invoices/
   └── Backups/
       ├── Database/
       └── Files/
```

### 5.2 Поделитесь папкой с Service Account

```
1. Откройте JSON файл Service Account
2. Найдите поле "client_email" (например: styleus-crm-service@...iam.gserviceaccount.com)
3. Скопируйте этот email

4. В Google Drive:
   - Правый клик на папку "STYLEUS CRM"
   - Share
   - Вставьте email Service Account
   - Права: Editor
   - Снимите галочку "Notify people"
   - Share
```

### 5.3 Получите ID папки

```
1. Откройте папку "STYLEUS CRM" в браузере
2. Скопируйте ID из URL:
   
   URL: https://drive.google.com/drive/folders/1a2b3c4d5e6f7g8h9i0j
                                              ^^^^^^^^^^^^^^^^^^^^
                                              Это ID папки
```

---

## Шаг 6: Установка пакетов на сервере

### 6.1 Подключитесь к серверу

```bash
ssh root@45.55.62.115
cd /var/www/styleus
```

### 6.2 Установите Google Drive пакет

```bash
composer require nao-pon/flysystem-google-drive
composer require masbug/flysystem-google-drive-ext
```

---

## Шаг 7: Конфигурация на сервере

### 7.1 Загрузите Service Account JSON

На вашем Mac:

```bash
scp /path/to/downloaded-service-account.json root@45.55.62.115:/var/www/styleus/storage/app/google-service-account.json
```

### 7.2 Обновите .env файл

На сервере:

```bash
nano /var/www/styleus/.env
```

Добавьте в конец файла:

```env
# Google Drive Configuration
FILESYSTEM_DISK=google
GOOGLE_DRIVE_CLIENT_ID=your_client_id_here
GOOGLE_DRIVE_CLIENT_SECRET=your_client_secret_here
GOOGLE_DRIVE_REFRESH_TOKEN=
GOOGLE_DRIVE_FOLDER_ID=your_folder_id_here

# Google Service Account
GOOGLE_APPLICATION_CREDENTIALS=/var/www/styleus/storage/app/google-service-account.json
```

Замените:
- `your_client_id_here` - Client ID из шага 3.2
- `your_client_secret_here` - Client Secret из шага 3.2
- `your_folder_id_here` - ID папки из шага 5.3

Сохраните: `Ctrl+O`, Enter, `Ctrl+X`

### 7.3 Обновите config/filesystems.php

```bash
nano /var/www/styleus/config/filesystems.php
```

Найдите секцию `'disks'` и добавьте:

```php
'google' => [
    'driver' => 'google',
    'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
    'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
    'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
    'folder' => env('GOOGLE_DRIVE_FOLDER_ID'),
    'teamDriveId' => env('GOOGLE_DRIVE_TEAM_DRIVE_ID'),
],
```

Сохраните файл.

---

## Шаг 8: Получение Refresh Token

### 8.1 Создайте временный скрипт

```bash
nano /var/www/styleus/get-google-token.php
```

Вставьте:

```php
<?php
require 'vendor/autoload.php';

$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$redirectUri = 'urn:ietf:wg:oauth:2.0:oob';

$client = new \Google\Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope(\Google\Service\Drive::DRIVE);
$client->setAccessType('offline');
$client->setPrompt('consent');

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    echo "Visit this URL:\n\n";
    echo $authUrl . "\n\n";
    echo "Enter the authorization code: ";
} else {
    $authCode = $_GET['code'];
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    
    if (isset($accessToken['refresh_token'])) {
        echo "\nRefresh Token:\n";
        echo $accessToken['refresh_token'] . "\n";
    } else {
        echo "Error: No refresh token received\n";
    }
}
?>
```

Замените `YOUR_CLIENT_ID` и `YOUR_CLIENT_SECRET`.

### 8.2 Запустите скрипт

```bash
php /var/www/styleus/get-google-token.php
```

Скопируйте URL, откройте в браузере, авторизуйтесь, скопируйте код.

```bash
php /var/www/styleus/get-google-token.php?code=PASTE_CODE_HERE
```

Скопируйте Refresh Token.

### 8.3 Добавьте Refresh Token в .env

```bash
nano /var/www/styleus/.env
```

Найдите `GOOGLE_DRIVE_REFRESH_TOKEN=` и вставьте токен.

### 8.4 Удалите временный скрипт

```bash
rm /var/www/styleus/get-google-token.php
```

---

## Шаг 9: Тестирование

### 9.1 Очистите кеш

```bash
cd /var/www/styleus
php artisan config:cache
```

### 9.2 Протестируйте загрузку

```bash
php artisan tinker
```

В tinker выполните:

```php
Storage::disk('google')->put('test.txt', 'Hello from STYLEUS CRM!');
Storage::disk('google')->exists('test.txt');
// Должно вернуть: true
exit
```

### 9.3 Проверьте в Google Drive

Откройте папку "STYLEUS CRM" - должен появиться файл `test.txt`.

---

## Шаг 10: Настройка автоматических бэкапов

### 10.1 Создайте команду бэкапа

```bash
nano /var/www/styleus/app/Console/Commands/BackupToGoogleDrive.php
```

Вставьте:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupToGoogleDrive extends Command
{
    protected $signature = 'backup:google-drive';
    protected $description = 'Backup database to Google Drive';

    public function handle()
    {
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql.gz';
        $localPath = storage_path('app/backups/' . $filename);
        
        // Create backup directory
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // Dump database
        $command = sprintf(
            'mysqldump -u %s -p%s %s | gzip > %s',
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DATABASE'),
            $localPath
        );
        
        exec($command);
        
        // Upload to Google Drive
        $contents = file_get_contents($localPath);
        Storage::disk('google')->put('Backups/Database/' . $filename, $contents);
        
        // Delete local backup
        unlink($localPath);
        
        $this->info('Backup uploaded to Google Drive: ' . $filename);
        
        return 0;
    }
}
?>
```

### 10.2 Зарегистрируйте команду

```bash
nano /var/www/styleus/app/Console/Kernel.php
```

Найдите метод `schedule()` и добавьте:

```php
protected function schedule(Schedule $schedule)
{
    // Daily backup at 2 AM
    $schedule->command('backup:google-drive')->dailyAt('02:00');
}
```

### 10.3 Протестируйте бэкап

```bash
php artisan backup:google-drive
```

Проверьте папку `STYLEUS CRM/Backups/Database/` в Google Drive.

---

## ✅ Проверочный чеклист

После завершения всех шагов проверьте:

- [ ] Google Cloud Project создан
- [ ] Все необходимые API включены
- [ ] OAuth Client ID создан
- [ ] Service Account создан и настроен
- [ ] Папка в Google Drive создана и расшарена
- [ ] Пакеты установлены на сервере
- [ ] .env файл обновлен
- [ ] Refresh Token получен
- [ ] Тестовый файл успешно загружен
- [ ] Автоматический бэкап работает

---

## 🔧 Устранение неполадок

### Ошибка: "Invalid credentials"
```bash
# Проверьте .env файл
cat /var/www/styleus/.env | grep GOOGLE

# Очистите кеш
php artisan config:cache
```

### Ошибка: "Folder not found"
```bash
# Проверьте ID папки
# Убедитесь, что папка расшарена с Service Account email
```

### Ошибка: "Permission denied"
```bash
# Проверьте права на файл Service Account
chmod 600 /var/www/styleus/storage/app/google-service-account.json
chown www-data:www-data /var/www/styleus/storage/app/google-service-account.json
```

---

## 📞 Полезные ссылки

- **Google Cloud Console**: https://console.cloud.google.com
- **Google Drive**: https://drive.google.com
- **API Documentation**: https://developers.google.com/drive/api/v3/about-sdk
- **Laravel Filesystem**: https://laravel.com/docs/filesystem

---

## 🎉 Готово!

После завершения настройки:

1. ✅ Все файлы CRM автоматически сохраняются в Google Drive
2. ✅ Ежедневные бэкапы базы данных
3. ✅ Доступ к файлам из любого места
4. ✅ Безопасное хранилище с версионированием

**Следующий шаг:** Настройка Gmail интеграции для отправки email из CRM.

---

**Документ создан:** 2025-12-01  
**Версия:** 1.0
