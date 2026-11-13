#!/bin/bash

echo "🔧 Creating admin user directly in database..."

cd /var/www/styleus

# Recreate DB user
./fix-database.sh

# Create admin user via SQL
mysql styleuscrm_prod -u styleuscrm_user -pPobeda8888 << 'EOF'
INSERT INTO users (name, email, password, role_id, view_permission, status, created_at, updated_at)
VALUES (
    'svmod',
    'svmod@styleus.us',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1,
    'global',
    1,
    NOW(),
    NOW()
);
EOF

if [ $? -eq 0 ]; then
    echo "✅ Admin user created!"
    echo ""
    echo "Login credentials:"
    echo "  Email: svmod@styleus.us"
    echo "  Password: password"
    echo ""
    echo "⚠️  IMPORTANT: Change password after first login!"
    echo ""
    echo "🌐 Visit: https://crm.styleus.us"
else
    echo "❌ Failed to create admin user"
    exit 1
fi
