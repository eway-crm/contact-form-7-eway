Eway extension for Contact Form 7
=================================
This plugin provides ability to create eway lead when user sends email 
through contact form 7 Wordpress plugin.

Setup
=====
1) Copy this plugin to wordpress plugin directory.

2) Activate plugin in Wordpress plugin administartion page. Note that this plugin will 
   affect each contact form 7 on your pages.

3) Fill in all fields in CF7EwayExt plugin page (you can acces this page from WP 
   administration Settings menu).

4) Go to CF7EwayExtConstants.php (plugin config file). Update contact form 7 fileds (f.ex.
   define("CF_MAIL",     "name of email field in your Contact form");)

5) Update SALES_MAIL and absolute paths to LOG_FILE, RAW_XML_FILE, PEM_FILE if necessary.

6) Enjoy your Contact Form.