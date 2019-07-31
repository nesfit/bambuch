# Project: BitInfoCharts parser
# Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz

FILES=./Makefile README.md ./app/Console/Commands/* ./app/Console/Config.php ./app/Console/Utils.php ./app/Models/Pg/* ./database/**/* ./db_dumps/*.csv

all:
	php artisan migrate --force
	php artisan db:seed --force

zip:
	zip pds-xbambu03.zip $(FILES)
