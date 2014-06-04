# Pretty URLs

## Apache

The framework quick-starters ship with a **.htaccess** file that is used to allow URLs without **index.php**.
If you use Apache to serve your PIMF application, be sure to enable the **mod_rewrite** module.

If the **.htaccess** file that ships with PIMF does not work with your Apache installation, try this one:

```cli
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^ index.php [QSA,L]
</IfModule>
```

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]

## Nginx

On Nginx, the following directive in your site configuration will allow pretty URLs:

```cli
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```