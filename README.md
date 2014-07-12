serverside
==========

# Server Configs

## Apache

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule v1/(.*)$ v1/index.php?request=$1 [QSA,NC,L]
</IfModule>

## Nginx

if (!-e $request_filename) {
  rewrite ^\/ptm\/v1\/(.+)$ /ptm/v1/index.php?request=$1 last;
}

---

## Google Spreadsheet

* original https://docs.google.com/spreadsheets/d/13E_Sdi3RwNvL6XoEec-8oAVu1K8o0PBDZ6mt5uvh2wc/pubhtml
* converted https://spreadsheets.google.com/feeds/list/13E_Sdi3RwNvL6XoEec-8oAVu1K8o0PBDZ6mt5uvh2wc/od6/public/basic?hl=en_US&alt=json
* documentation https://developers.google.com/gdata/docs/json


