<VirtualHost *:80>
        ServerName baseapp.com
        ServerAlias www.baseapp.com
        ServerAdmin webmaster@baseapp.com

        DocumentRoot /var/www/baseapp.com/www/

        <Directory />
                Options FollowSymLinks
                AllowOverride All
        </Directory>

        <Directory /var/www/baseapp.com/www/>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
        <Directory "/usr/lib/cgi-bin">
                AllowOverride None
                Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
                Order allow,deny
                Allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/baseapp-error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/baseapp-access.log combined
</VirtualHost>

