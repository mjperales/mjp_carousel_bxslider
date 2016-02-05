# Carousel Box Slider

Tags: slideshow, slider, slides, slide, images, image, responsive, mobile, jquery, javascript, featured, HTML content
Requires at least: 4.0

## Installation
Include the folder in your plugins folder or go through `Plugins > Add New > Upload`  *please make sure the file is in a zip format

### Display a slider
To display the slider, you can use any of the following methods.

**In a post/page:**
Simply insert the shortcode below into the post/page to display the slider. Be sure to replace the "1" with the ID of the slider you wish to display.
You can also click on the Add Carousel button in the editor to select a slider to display.

`[tcu_carousel id="1"]`

**Function in template files (via php):**
To insert the slider into your theme, add the following code to the appropriate theme file. As above, replace the "1" with the ID of the slider you wish to display.

`<?php if ( function_exists( "tcu_display_carousel" ) ) { tcu_display_carousel( 1 ); } ?>`