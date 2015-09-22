#StackLife @ Andover

StackLife is a community-based wayfinding tool for navigating the vast resources of the North of Boston Library Exchange catalog. It enables researchers, teachers, scholars, and students to find what they need and help others learn from them and their paths.

## Installation

### PHP and the web server

StackLife is written in PHP. PHP 5.3 or later is recommended.

Serving up StackLife in [Apache](http://httpd.apache.org/) is probably the easiest way to get started. StackLife relies on rewrite rules in .htaccess. Be sure you're allowing for .htaccess in your httpd configuration and that you have mod_php and mod_rewrite installed.

### Installation

Use the git clone command to get the latest version of StackLife

### Supporting MySQL database

StackLife requires a supporting database called 'stackview' that has two tables.

    CREATE TABLE sl_tags (id mediumint(11) NOT NULL AUTO_INCREMENT, item_id varchar(255) NOT NULL, tag varchar(255) NOT NULL, PRIMARY KEY (id));
    CREATE TABLE sl_also_viewed (id mediumint(11) NOT NULL AUTO_INCREMENT, book_one varchar(256) NOT NULL, book_two varchar(256) NOT NULL, PRIMARY KEY (id));
    
The connection details for this database should be put into the configuration file.

### Typekit

StackLife uses fonts from TypeKit.  The TypeKit embed code should be put into includes/includes.php.  There are two font selector groups:

    #overlaynav, #results, #search_results_body, .button, .creator, .footer-content, .navigation, .tk-ff-tisa-web-pro, h1, h2, input, p 

and

    #all-rank, #recentlyviewed, #search_results_header, #welcome, #wrap, .addfield, .facet_heading, .facet_set, .hdr, .heading, .hits, .reload, .rem_filter, .removefield, .tk-futura-pt

### StackLife configuration

Configuration takes place in etc/sl_ini.php. Copy the example and edit the values:

    cd stacklife/etc
    cp sl_ini.example.php sl_ini.php

### .htaccess configuration

We use a .htaccess file to make URLs pretty. Copy the example:

    cd stacklife
    cp .htaccess.example .htaccess

### Generating the landing page, static stack data

The landing page stack is populated by data from the NOBLE API.

To generate the static JSON file:

    cd src/batch
    php -f retrieve_data.php

If things bang off without issues you should now have src/web/js/awesome.js

## License

Dual licensed under the MIT license (below) and [GPL license](http://www.gnu.org/licenses/gpl-3.0.html).

<small>
MIT License

Copyright (c) 2012 The Harvard Library Innovation Lab

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
</small>