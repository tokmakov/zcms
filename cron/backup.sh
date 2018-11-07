#!/bin/bash
export DB_BACKUP="***" # каталог для хранения
export DB_USER="***" # имя пользователя БД
export DB_PASSWD="***" # пароль пользователя БД
export DB_NAME="***" # имя базы данных

rm -rf $DB_BACKUP/20
mv $DB_BACKUP/19 $DB_BACKUP/20
mv $DB_BACKUP/18 $DB_BACKUP/19
mv $DB_BACKUP/17 $DB_BACKUP/18
mv $DB_BACKUP/16 $DB_BACKUP/17
mv $DB_BACKUP/15 $DB_BACKUP/16
mv $DB_BACKUP/14 $DB_BACKUP/15
mv $DB_BACKUP/13 $DB_BACKUP/14
mv $DB_BACKUP/12 $DB_BACKUP/13
mv $DB_BACKUP/11 $DB_BACKUP/12
mv $DB_BACKUP/10 $DB_BACKUP/11
mv $DB_BACKUP/09 $DB_BACKUP/10
mv $DB_BACKUP/08 $DB_BACKUP/09
mv $DB_BACKUP/07 $DB_BACKUP/08
mv $DB_BACKUP/06 $DB_BACKUP/07
mv $DB_BACKUP/05 $DB_BACKUP/06
mv $DB_BACKUP/04 $DB_BACKUP/05
mv $DB_BACKUP/03 $DB_BACKUP/04
mv $DB_BACKUP/02 $DB_BACKUP/03
mv $DB_BACKUP/01 $DB_BACKUP/02
mkdir $DB_BACKUP/01

# база данных целиком
# mysqldump --user=$DB_USER --password=$DB_PASSWD -R $DB_NAME > $DB_BACKUP/01/$DB_NAME-`date +%d.%m.%Y-%H.%M.%S`.sql
# отдельные таблицы
# mysqldump --user=$DB_USER --password=$DB_PASSWD $DB_NAME имя_таблицы > $DB_BACKUP/01/$DB_NAME-имя_таблицы-`date +%d.%m.%Y-%H.%M.%S`.sql

mysqldump --user=$DB_USER --password=$DB_PASSWD -R $DB_NAME | gzip -9 -c > $DB_BACKUP/01/$DB_NAME-`date +%d.%m.%Y-%H.%M.%S`.sql.gz

# чтобы в письме можно было увидеть размер бэкапа
ls -lah $DB_BACKUP/01/
exit 0