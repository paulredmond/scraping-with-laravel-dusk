## Demo Commands from the Presentation

Summary:

- `php app.php scrape:hn`
- `php app.php scrape:recursive http://www.example.com/`
- `php app.php link:analyze https://yahoo.com --nofollow`
- `php app.php link:analyze https://yahoo.com --dofollow`

Example command that grabs all the story links from Hacker News
```
php app.php scrape:hn
```

Recursively scrapes links X deep, be careful this can get heavy.
```
php app.php scrape:recursive http://www.example.com/
```

Get links on a page with `rel="nofollow"`:
```
php app.php link:analyze https://yahoo.com --nofollow
```

Get links on a page without a `rel="nofollow"`:
```
php app.php link:analyze https://yahoo.com --dofollow
```
