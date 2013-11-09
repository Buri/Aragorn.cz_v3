

Virtual host config:

<VirtualHost *:80>
	DocumentRoot /home/domeny/aragorn.cz/web/subdomeny/www

	ServerName aragorn.cz
	ServerAlias www.aragorn.cz s1.aragorn.cz s2.aragorn.cz
	ServerAdmin webmaster@aragorn.cz

	ErrorLog /home/domeny/aragorn.cz/web/log/error.log
	CustomLog /home/domeny/aragorn.cz/web/log/access.log combined

	<Directory /home/domeny/aragorn.cz/web/subdomeny>
		Options FollowSymLinks
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>

	...

	<IfModule mpm_itk_module>
		AssignUserId ... ...
	</IfModule>

	Alias /c /home/domeny/aragorn.cz/web/subdomeny/www/css
	Alias /j /home/domeny/aragorn.cz/web/subdomeny/www/js
	Alias /i /home/domeny/aragorn.cz/web/subdomeny/www/system/icos
	Alias /r /home/domeny/aragorn.cz/web/subdomeny/www/system/roz_icos
	Alias /gg /home/domeny/aragorn.cz/web/subdomeny/www/gal
	Alias /g /home/domeny/aragorn.cz/web/subdomeny/www/graphic
	Alias /s /home/domeny/aragorn.cz/web/subdomeny/www/system
</VirtualHost>
