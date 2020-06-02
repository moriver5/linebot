@servers(['web' => ['moritomo@153.150.75.210']])

@task('deploy', ['on' => 'web'])
	cd /data/www/release/jray;
	svn up;
	sudo chown -R apache:apache /data/www/release/jray;
	composer install --optimize-autoloader
	composer dump-autoload --optimize;
	php artisan clear-compiled;
	php artisan optimize;
	php artisan config:cache;
	php artisan route:cache;
	php artisan view:clear;
	php artisan env --env=production
	sudo chmod -R 775 /data/www/release/jray;
	sudo chmod -R 777 /data/www/release/jray/.svn;
	rsync -pogrv --exclude-from=/data/www/release/jray/rsync_exclude.txt --delete /data/www/release/jray /data/www;
@endtask
