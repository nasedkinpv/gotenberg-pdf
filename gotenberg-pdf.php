<?php

/**
 * 
 * Plugin Name: Gotenberg PDF
 * Plugin URI:  https://github.com/nasedkinpv/gotenberg-pdf
 * Description: Wordpress plugin for creating PDF files from docker container Gotenberg
 * Version:     1.0.0
 * Author:      Ben Nasedkin
 * Author URI:  nasedk.in
 * License:     GNU GENERAL PUBLIC LICENSE
 * GitHub Plugin URI: nasedkinpv/gotenberg-pdf
 * GitHub Plugin URI: https://github.com/nasedkinpv/gotenberg-pdf
 * 
 */

require "vendor/autoload.php";
include_once "options-page.php";

use TheCodingMachine\Gotenberg\Client;
use TheCodingMachine\Gotenberg\URLRequest;
use TheCodingMachine\Gotenberg\Request;
// use TheCodingMachine\Gotenberg\Request\setAssets;

use TheCodingMachine\Gotenberg\DocumentFactory;

if (!defined("ABSPATH")) {
    exit; // Exit if accessed directly
}

/** Class Gotenberg PDF Plugin */
class Gotenberg
{
    const DEBUG = 1;
    private $webserver = 'http://nginx:80';
    private $gotenberg = 'http://gotenberg:3000';
    private $query_print = 'print';
    private $query_pdf = 'pdf';
    private $post_type = 'yacht';
    private $theme = 'fairline';

    function __construct()
    {
        $options = get_option('gotenberg_pdf_settings');
        $this->webserver = $options['webserver_uri'] ?: $this->webserver;
        $this->pdf_folder = get_stylesheet_directory() . '/print/';
        $this->mode = isset($_REQUEST[$this->query_pdf]) ? $_REQUEST[$this->query_pdf] : null;
        $this->print = isset($_REQUEST[$this->query_print]) ? $_REQUEST[$this->query_print] : null;
    }
    function watch_query_and_post_type($html)
    {
        if ($this->print == 1) {
            include_once(plugin_dir_path(__FILE__) . '/themes/' . $this->theme . '/index.php');
            exit;
        }
        if (get_post_type() == $this->post_type and $this->mode) {
            $this->request_uri = self::get_uri_request();
            $this->file_exist = self::check_pdf_exist();
            $this->filename = self::get_pdf_filename();
            $this->file_path = $this->pdf_folder . $this->filename;
            $this->file_uri = get_stylesheet_directory_uri() . '/print/' . $this->filename;
            // if ($this::DEBUG) var_dump($this);
            if ($this->mode) {
                header('Content-type:application/pdf');
                header('Content-Disposition:attachment; filename="' . $this->filename . '"');
                switch ($this->mode) {
                    case 1:
                        // regular download, if not exist create, if exist download.
                        if ($this->file_exist) {
                            readfile($this->file_path);
                        } else {
                            $this->generateFromUri($this->request_uri, $this->file_path);
                            readfile($this->file_path);
                        }
                        break;
                    case 2:
                        // overwrite
                        $this->generateFromUri($this->request_uri, $this->file_path);
                        readfile($this->file_path);
                        break;
                    default:
                        $this->generateFromUri($this->request_uri, $this->file_path);
                        readfile($this->file_path);
                        break;
                }
                exit;
            }
        } else {
            return $html;
        }
    }
    public function load()
    {
        add_filter('template_redirect', [$this, 'watch_query_and_post_type']);
        // add_action('admin_menu', [$this, 'add_plugin_options_page']);
        // add_action('admin_init', [$this, 'add_plugin_settings']);
    }
    function get_uri_request()
    {
        $request = $this->webserver . str_replace(get_home_url(), '', get_permalink());
        $request .= '?' . $this->query_print . '=1';
        return $request;
    }
    function check_pdf_exist()
    {
        if (file_exists($this->pdf_folder . self::get_pdf_filename())) return true;
    }
    function get_pdf_filename(): String
    {
        global $post;
        $filename = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $filename = preg_replace('~[^\pL\d]+~u', '-', $filename);
        $filename = iconv('utf-8', 'us-ascii//TRANSLIT', $filename);
        $filename = preg_replace('~[^-\w]+~', '', $filename);
        $filename = trim($filename, '-');
        $filename = preg_replace('~-+~', '-', $filename);
        $filename = strtolower($filename);
        $filename .= '-' . get_the_ID() . '.pdf';
        return $filename;
    }

    function generateFromUri($url, $filepath)
    {
        $client = new Client($this->gotenberg, new \Http\Adapter\Guzzle6\Client());
        $request = new URLRequest($url);
        // $request->setAssets($assets);
        $request->setPaperSize(URLRequest::A4);
        $request->setMargins([0.2, 0.2, 0.2, 0.2]);
        $request->setWaitDelay(2.5);
        $request->setWaitTimeout(10);

        $client->store($request, $filepath);
        return $request;
    }
}

$plugin = new Gotenberg();
$plugin->load();
