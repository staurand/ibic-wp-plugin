# In Browser Image Compression #
**Contributors:** [staurand](https://profiles.wordpress.org/staurand/)
**Tags:** image optimization, webp
**Requires at least:** 6.6
**Tested up to:** 6.8
**Requires PHP:** 7.4
**Stable tag:** 2.0.0
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Compress your images in your browser!

## Description ##

IBIC (In Browser Image Compression) compresses your images in your browser.

Useful for websites on shared hosting where no extra image libraries can be installed.

Why should you reduce images file size:

* faster page load
* improve your SEO (Search Engine Optimization)

## Installation ##

1. Activate the plugin through the 'Plugins' menu in WordPress
2. Upload images
3. Enjoy!

You can check the image compression progress in the Media section.

## Frequently Asked Questions ##

### How does it work? ###

Once you upload an image, the image will be optimized in the background.
Then this optimized image will be uploaded on the server.
Finally, URL rewriting will be used to serve the optimized image on your site.

### What browsers are supported? ###

An up-to-date browser (Chrome, Firefox, Safari and Edge) is required in the admin to compress the uploaded images.
On your public website, webp image could be served for browsers that support this format, otherwise an optimized jpg / png image will be used instead.

### What images formats are supported? ###

jpg and png are supported and will be optimized.

## Screenshots ##

1. The media compression status page

## Changelog ##

### 2.0.0 ###
* The media compression status page now displays errors in a separate list
* New design
* Batch upload optimized images to avoid some server limitations (like max_file_uploads)
* New health check available in the Site Health page /wp-admin/site-health.php?tab=debug
* Fix missing translations

### 1.1.1 ###
* The media compression status page now displays the remaining images to be compressed
* Add loading state to the image compression status list

### 1.1.0 ###
* Improve error management
* New interface to check image compression status (Medias > Image compression)

### 1.0.0 ###
* First release

## Troubleshooting ##

### I can't see the image compression progress in the Media section ###

Please check that there is no error in the IBIC plugin section of the Site Health page:
/wp-admin/site-health.php?tab=debug
