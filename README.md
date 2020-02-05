# wordpress-papercite-pages
Automatically generate virtual subpages for papercite entries

## How to use

The following todo is VERY important as this plugin uses specifically named pages. Yes this is very limiting, but submit a PR with an admin interface and we can fix this.

Make a page with the slug "publications" and on that page put you full bibliography shortcode (and anything else you want on your publication page)

i.e. 

```
[bibfilter format="ieee_link" group=year group_order=desc author="Casper the Ghost|Richard Burton|John Legend" allow=incollection,mastersthesis,article,book,inbook,techreport,misc sortauthors=1]
```

Then create a page with the slug "publication" and make sure its parent page is the previously made "publications" so the full permalink is "publications/publication"

on this page put the following shortcode

```
[singletex]
```

## Samples

* https://zeal.global/publications