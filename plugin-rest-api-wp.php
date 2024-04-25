<?php
/*
Plugin Name: WP REST API
Plugin URI: https://shamskhan.xyz
Description: create db tables, then insert some data, fetch the data from postman and insert some data from post using rest api.
Author: Shams Khan
Version: 1.0.0
Author URI: https://shamskhan.xyz
*/

// Exit if accessed directly
if( !defined('ABSPATH'))
{
    exit; 
}

register_activation_hook( __FILE__, 'setup_table');

//setup table for api data in Database

function setup_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submission';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar (100) NOT NULL,
        email varchar (100) NOT NULL,
        PRIMARY KEY (id)
        )";    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

//Create and Register new Rest Route/endpoint/

add_action('rest_api_init', 'registering_routes');

function registering_routes(){
    register_rest_route(
        'form_submission_route/v1',
        '/form-submission',
        array(
            'method' => 'GET',
            'callback' => 'form_sub_callback',
            'permission_callback' => '__return_true'
        )
    );
    register_rest_route(
        'form_submission_route/v1',
        '/form-submission',
        array(
            'methods' => WP_REST_SERVER::CREATABLE,,
            'callback' => 'form_get_callback',
            'permission_callback' => 'wp_check_permission'
        )
    );
}

function form_sub_callback(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submission';
    $results = $wpdb->get_results( "SELECT * FROM $table_name" );
    return $results;
}

function wp_check_permission(){
	return current_user_can('edit_posts');
}
            
function form_get_callback($request){
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submission';

    $row = $wpdb->insert(
        $table_name,
        array(
            'name' => $request['name'],
            'email' => $request['email']
        )
    );
    return $row;
}