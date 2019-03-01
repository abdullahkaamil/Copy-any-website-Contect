# Copy-any-website-Contect
Using Php Curl and Html_Dom to copy the webiste Text, picture, and other items


Note: I don’t intend to maintain this package. Other copies of Simple HTML DOM are already available on Packagist, are easier to install and don’t clutter your composer.json file.

simple-html-dom
A copy of the PHP Simple HTML DOM Parser project usable as a Composer package.

Installation
First, you need to add this repository at the root of your composer.json:

"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Youpie/simple-html-dom"
    }
]
Then, require this package in the same way as any other package:

"require": {
    "simple-html-dom/simple-html-dom": "*"
}
Do a composer validate, just to be sure that your file is still valid.

And voilà, you’re ready to composer update.

and include this in your Controller (if using laravel)

include 'simple_html_dom.php';
