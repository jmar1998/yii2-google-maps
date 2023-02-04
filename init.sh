#!/bin/bash
composer install;
chmod 777 ./runtime;
chmod 777 ./web/assets;
echo "y" | ./yii migrate 
apache2-foreground;