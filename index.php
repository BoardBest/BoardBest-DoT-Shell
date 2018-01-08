<?php
/*
Plugin Name: Frontend shell
Description: Dot.js library GUI shell for WordPress
Version:     0.0.1
Author:      dadmor@gmail.com

Copyright © 2017-2017 FutureNet.club

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

########################################################################################
Modal
----------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------
Open empty modal:

http://...#showmodal

------------------------------
Open and load data:

http://...#showmodal/a=sGETtemplate/tplf=body-tabs-content-buttons
a 		- action from /actions.php file
tplf 	- templete from /tpl directory
tg 		- DOM element id (default: modal-body)

------------------------------
Close modal:

http://...#closemodal

*/

/* Include actions */
include __DIR__.'/actions.php';
include __DIR__.'/actions-newsletter.php';
include __DIR__.'/actions-autoresponder.php';
include __DIR__.'/actions-contactForm.php';
include __DIR__.'/actions-footer.php';
include __DIR__.'/actions_api.php';
include __DIR__.'/actions-add-shortcode.php';
include __DIR__.'/actions-add-icons.php';
include __DIR__.'/actions-section-options.php';
include __DIR__.'/actions-add-templates.php';
include __DIR__.'/actions-progressbar.php';

/* 
----------------------------------------------------------------------------------------
drip automation 
*/

foreach (glob(__DIR__.'/shortcodes/*.php') as $filename)
{
    include $filename;
}




