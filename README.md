# IBIC #
**Contributors:** [staurand](https://profiles.wordpress.org/staurand/)  
**Tags:** image optimization, webp  
**Requires at least:** 5.2  
**Tested up to:** 5.9  
**Requires PHP:** 5.6  
**Stable tag:** 1.0.0  
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

## Changelog ##

### 1.0 ###
* First release

## Troubleshooting ##

### I can't see the image compression progress in the Media section ###

Please check that there is no error in the IBIC plugin section of the Site Health page:
/wp-admin/site-health.php?tab=debug
