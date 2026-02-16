#!/bin/bash
read -p "Apakah Anda yakin akan backup DB dan Push ke Git ? (Y/N)" JAWAB
if [ "$JAWAB" = "Y" ]
then
	echo "Backup database MYSQL....."
	mysqldump  maxindorental -u maxindoerp -pQeHukIKITiFo > dump.sql;
	echo "Push ke Git....."
	read -p "Masukkan pesan Commit : " COMMENT
	git add .;
	git commit -m "$COMMENT";
	git push;
	echo "Selesai...."
fi
