<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_action('trexanhproperty_before_listing_loop_item_title', 'trexanhproperty_template_loop_listing_thumbnail', 10);
add_action('trexanhproperty_after_listing_loop_item_title','trexanhproperty_template_loop_listing_summary', 10);