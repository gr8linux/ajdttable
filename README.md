# AJAX Table with Custom CRUD APIs #

**Plugin Name:** AJAX Table with Custom CRUD API
**Contributors:** shajeeb.s
**Tags:** bootstrap, filters, data filters, ajax data filters, advanced data filters, CRUD operations, CRUD API, Custom REST API
**Tested up to:** 5.5.1
**Stable tag:**  1.0.2
**License:** GPLv2 or later
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html
	
	
## Description ##
	
'AJAX Table with Custom CRUD API' is a multipurpose wordpress plugin data table used for displaying custom data from the database.
	
### Features: ###
* Generates Custom REST API
* Integrates Bootsrap tables
* Search records with AJAX
* Filter visibility by data category. Different categories pages = different ( + global ) filters.
* No reloading, only ajax
 
### How It Works: ###
	
* Install and activate plugin
* Go to Admin area -> AjaxTable Settings 
* Create API by selecting database table. Test the api by clicking URL field
* 
### Usage of API: ###
* Use the short code wherever required to display the data
* Syntax

    [AJDT api=' API - Name given while creation from via AjaxTable Settings']

* Short Code example:-
 
         [AJDT api='apiname']
           
* From Code example:-      

        echo do_shortcode("[AJDT api='apiname']");
        
### Installation ###

1. Install 'AJAX Table with Custom CRUD API' either via the WordPress.org plugin directory, or by uploading the files to your server
2. Activate 'AJAX Table with Custom CRUD API' and you're ready to go!

