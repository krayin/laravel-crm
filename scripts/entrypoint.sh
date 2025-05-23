until nc -z -v -w30 krayin_mysql 3306
do
  echo "⏳ Ainda aguardando o MySQL..."
  sleep 3
done

echo "MySQL está pronto - executando comandos de inicialização..."

INSTALLED=$(mysql --skip-ssl -h krayin_mysql -u root -p"Flip123@" -D laravel-crm -e "SHOW TABLES LIKE 'core_config';" | grep core_config)

echo "INSTALLED=$INSTALLED"

if echo "$INSTALLED" | grep -q "core_config"; then
  
  echo "\033[1;35m Krayin já está instalado. Pulando instalação...\033[0m"

else
  echo "============================="
  echo "\033[1;34mKrayin\033[0m"
  echo "\033[1;33mMulti Tenant\033[0m"
  echo "\033[0;31mAPI Rest\033[0m"
  echo "============================="

  #CONFIGURAÇÕES KRAYIN

  echo "\033[1;35m Krayin ainda não instalado. Executando instalação...\033[0m"

  # echo "\033[1;33m-> composer require stancl/tenancy\033[0m"
  # composer require stancl/tenancy

  echo "\033[1;34m->composer install\033[0m"
  composer install

  # echo "\033[1;34m-> composer create-project\033[0m"
  # composer create-project ...

  echo "\033[1;34m-> php artisan krayin-crm:install\033[0m"
  php artisan krayin-crm:install

  #CONFIGURAÇÕES API 

  # echo "nameserver 8.8.8.8" > /etc/resolv.conf
  # echo "nameserver 8.8.4.4" >> /etc/resolv.conf

  # echo "\033[0;31m-> composer config -g repo.packagist composer https://mirrors.cloud.tencent.com/composer/\033[0m"
  # composer config -g repo.packagist composer https://mirrors.cloud.tencent.com/composer/

  echo "\033[0;31m-> composer clear-cache\033[0m"
  composer clear-cache

  # echo "\033[0;31m-> composer require krayin/rest-api -vvv\033[0m"
  # composer require krayin/rest-api -vvv

  echo "\033[0;31m-> php artisan krayin-rest-api:install\033[0m"
  php artisan krayin-rest-api:install 

  #CONFIGURAÇÕES MULTI TENANT

  # echo "\033[1;33m-> php artisan db:seed\033[0m"
  # php artisan db:seed

fi

php artisan serve --host=0.0.0.0 --port=8000

