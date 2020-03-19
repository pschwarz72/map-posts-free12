=== Map Posts Free ===
Contributors: pschwarzgm
Donate link: http://integratedproductivitytools.com
Tags: map, geocode, simple map plugin, open street map, insert map wordpress
Requires at least: 3.9
Tested up to: 4.9
Requires PHP: 5.2.4
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Configure and insert a map without leaving the editor. Location is automatically saved as part of post metadata.
 
== Description ==

Map Posts allows you to add location as post metadata and insert a map directly in the post, without ever leaving the editor.
No plugin configuration is required; simply install, activate and within the post editor define a location and insert a map.
 
= Post Map =

There are 2 types of map that can be inserted. The first (Post Map) displays only one marker, which is the current location 
associated with the post. Note that Map Posts automatically adds a location to every post, defaulting to co-ordinates of 0.00, 
0.00 (the intersection of the Equator and the prime meridian) and a blank value for the address. Post Maps are typically
inserted to display a location that is related to the post. 

To actually insert the map, there are two options. If using the visual editor, simply place the cursor in the content area
where you would like to insert the map, then click the Insert Post Map button. Alternatively you can simply type in the
Post Map shortcode, which is __postmap__. The second option can be done in visual or text editor mode.


= All Posts Map =

The second type of map is called the All Posts Map. This map inserts a marker for every post that is published (not saved as
a draft) and has a custom location (it has co-ordinates other then 0.00,0.00). Insert this map if you want to display
all post locations in relation to one another.

To actually insert the map, either use the Insert All Posts Map button from the visual editor toolbar, or type in the
shortcode __allpostmap__  (in either visual or text editor mode).


= Adding Location =

To update the location, find the Define Map configuration panel within the post editor and expand it if necessary. If it is
not visible open the WordPress editor screen options and ensure Define Map is listed and is checked.

Locations can be updated by:

* Typing in an Address
* Clicking on Map
* Dragging the Marker
* Typing in the Latitude and Longitude Manually

If an address is provided (or an existing address is changed) Map Posts will attempt to geocode it, which means it will send 
the address to a geocoding service that can match addresses with a set of co-ordinates. If a match can be found the post location 
will be updated with the new latitude and longitude; if not, no changes will be made and the user will be informed that no locations 
could be found matching the address.


= Co-ordinate Units =

Map Posts uses latitude and longitude co-ordinates in decimal degree (DD) units. This means it will not recognize 
co-ordinates in the format of degrees, minutes and seconds (DMS)or with N,S,E or W abbreviations indicating hemisphere.
If you intend to provide co-ordinates manually but they are in a non-supported format, there are a number of online 
converters available:

[Geoinfo San Diego State University](https://geoinfo.sdsu.edu/hightech/LM3/dd1.php)
[RapidTables](https://www.rapidtables.com/convert/number/degrees-minutes-seconds-to-degrees.html)

or Google:

convert degrees minutes seconds to decimal degrees


= Map Configuration =

You can further customize your map appearance by defining a width, height and initial zoom level.

* Map Width - as a percentage of width available, from 10 to 100 (default - 75%)
* Map Height - in pixels, from 10 to 10 000 (default - 300px)
* Initial Map Zoom - from 0 (world) to 18 (highest) - (default - 0)

NOTE: Map configuration applies to all maps (Post Map or All Posts Map) inserted in the post. Each post retains its own
configuration.


= Preview Map =

A preview map is used as a visual means to verify post location is correct. It shows the most recently successfully 
saved location and the currently configured initial zoom level. The preview map height and width is fixed and 
does not reflect custom map configuration. To verify the suitability of a customized height or width, insert a map
in the post and view it in preview mode.


= Saving Configuration =

Location and map configuration is applied whenever a post is updated (when the Save As Draft, Publish or Update button
is clicked, depending on post status). If no data validation errors occur, the values entered will be saved to the
database and map configuration updated. It is possible for changes to be only partially saved - if any configuration
fails, it will revert to its last saved value and a message will be provided to the user. 



 
== Installation ==

To install automatically

1. Log into the WordPress dashboard
2. Go to Plugins
3. Click Add New
4. Find Map Posts Free and click Install Now
5. Once installed, activate the plugin

To install the plugin manually

1. Download the plugin zip file
2. Log into the WordPress dashboard
3. Go to Plugins
3. Click Add New
4. Click Upload Plugin
5. Click Choose File and navigate to the location the plugin zip file was downloaded
6. Select the plugin zip file and click Open
7. Click Install Now
8. Once installed, activate the plugin



== Screenshots ==

1. The Define Map configuration screen including a preview map should be visible below the visual and text editor screens. If not, open Screen Options in top right corner and ensure Define Map is list and checked.

2. Post location is supplied by providing an address or manually typing in known latitude and longitude. Optional map configuration includes map width (as a percentage of width available), map height (in screen pixels - but not exceeding 9999) and initial zoom level (default is 0 but a value around 14 or higher is recommended for most maps). Configurations are saved by saving the post (i.e. click Save as Draft, Publish or Update - depending on post status).

3. To actually insert a map in the post, place the cursor somewhere in the content area and (in Visual editor) click the Insert Post Map or Insert All Posts Map button. Alternatively to clicking the buttons type in the shortcode __postmap__ or __allpostmap__ (in Visual or Text editor).

4. The shortcode after insertion by clicking the button or manually typing in. To see the map as it appears in the published post, click Preview Changes or View Post. The Preview Map below Define Map also shows the currently saved location but does not use any custom width or height configuration.

5. An example Post Map in a (very short) blog post.
 
 
== Changelog ==
 
= 1.0 =

* First released version

= 1.0.1 =

* Fix: Renamed images subfolder under css folder to use all lower case letters.

= 1.0.2 =

* Improvement Fix: Generating the geocoding http request no longer dependent on allow_url_fopen being enabled.

= 1.1 =

* New Feature: Supports all built-in and custom post types.
* New Feature: Can drag markers to fine-tune location.
* New Feature: Click on preview map to update location.
* Improvement Fix: All Posts Map shows only locations associated with the same post type (except 'page' post type).
* Improvement Fix: Define map screen has a new appearance.
* Improvement Fix: Define map screen default user message updated to reflect additional methods for defining location.
 