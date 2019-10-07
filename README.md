#Install Magento using Composer

##Magento Open Source

```composer create-project --repository=https://repo.magento.com/ magento/project-community-edition <install-directory-name>```


###Set file permissions

You must set read-write permissions for the web server group before you install the Magento software. This is necessary so that the Setup Wizard and command line can write files to the Magento file system.

cd /var/www/html/<magento install directory>
find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} +
find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} +
chown -R :www-data . # Ubuntu
chmod u+x bin/magento

###DB mysql
* create db "magento"
* add user db "magento"

###Command line
```
sudo -su www-data php bin/magento setup:install \
                      --base-url=http://magento2.local \
                      --db-host=localhost \
                      --db-name=magento \
                      --db-user=magento \
                      --db-password=magento \
                      --admin-firstname=admin \
                      --admin-lastname=admin \
                      --admin-email=admin@admin.com \
                      --admin-user=admin \
                      --admin-password=admin123 \
                      --language=en_US \
                      --currency=USD \
                      --timezone=America/Chicago \
                      --use-rewrites=1
```
