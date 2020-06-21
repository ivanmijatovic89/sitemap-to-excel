# Crawl sitemap.xml to Excel

This project you can crawl any url containing sitemap.xml

**Crawler** will go to each page from given sitemap.xml url and collect:
* title
* meta description
* url
* canonical
* image
* og:title
* og:image
* og:url
* og:site_name
* og:description
* og:type
* h1
* h2 ( all h2 on page)

Script will crawl urls asynchronously...


Run Script
```bash
php artisan seo:export "https://yourdomain.com/sitemap.xml"
```

File will be stored at
```bash
storage/app/{DOMAIN}-{YEAR}-{MONTH}-{DAY}.xls

#example
storage/app/yourdomain.com-2020-06-25.xls
```

Exported file:
![Export Example](/screenshots/export-example.png?raw=true "Export Example")
