#!/bin/bash

echo "🔧 Fixing database user and permissions..."

# Drop and recreate user to ensure clean state
mysql -e "DROP USER IF EXISTS 'styleuscrm_user'@'localhost';"
mysql -e "CREATE USER 'styleuscrm_user'@'localhost' IDENTIFIED BY 'Pobeda8888';"
mysql -e "GRANT ALL PRIVILEGES ON styleuscrm_prod.* TO 'styleuscrm_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "✅ Database user recreated!"
echo ""
echo "Testing connection..."
mysql -u styleuscrm_user -pPobeda8888 -e "SHOW DATABASES;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Connection successful!"
else
    echo "❌ Connection failed!"
    exit 1
fi

echo ""
echo "Now run: ./final-setup.sh"
