<?php

/**
 * 
 * Plugin Name: Gotenberg PDF
 * Plugin URI:  https://github.com/nasedkinpv/gotenberg-pdf
 * Description: Wordpress plugin for creating PDF files from docker container Gotenberg (thecodingmachine)
 * Version:     1.0.1
 * Author:      Ben Nasedkin
 * Author URI:  nasedk.in
 * License:     GNU GENERAL PUBLIC LICENSE
 * GitHub Plugin URI: https://github.com/nasedkinpv/gotenberg-pdf
 * 
 */

require "vendor/autoload.php";
include_once "options-page.php";

use TheCodingMachine\Gotenberg\Client;
use TheCodingMachine\Gotenberg\ClientException;
use TheCodingMachine\Gotenberg\URLRequest;
use TheCodingMachine\Gotenberg\Request;
use TheCodingMachine\Gotenberg\RequestException;
// use TheCodingMachine\Gotenberg\Request\setAssets;

use TheCodingMachine\Gotenberg\DocumentFactory;


// deny direct access
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// check WordPress version
global $wp_version;
if (version_compare($wp_version, '3.9', "<")) {
    exit('3.9' . ' requires WordPress ' . '3.9' . ' or newer.');
}

if (!class_exists('GOTENGERG_SERVICE')) :
    class GOTENGERG_SERVICE
    {
        const DEBUG = 1;
        private $theme = 'nordmarine';
        function __construct()
        {
            $this->get_settings();
            // Plugin action links
            add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
            // Activation hooks
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook(__FILE__, array($this, 'deactivate'));
            // Filterable init action
            add_action('init', array($this, 'init'), 0, 0);
            add_filter('template_redirect', [$this, 'run']);

            // $this->run(); // non-filterable init action
        }

        /**
         * Runs on enabling plugin
         * 
         * @param null
         * @return boolean
         */
        public function activate()
        {
            // set defaults
            $this->webserver = 'http://nginx:80';
            $this->gotenberg_uri = 'http://gotenberg:3000';
            $this->query_for_print = 'print';
            $this->query_for_pdf = 'pdf';
            $this->custom_post_type = 'page';
            $this->pdf_folder = get_stylesheet_directory() . '/print/';
        }


        /**
         * Initialization of plugin, runs on start
         * 
         * @return boolean
         */
        public function init()
        {
            // add_filter('template_redirect', [$this, 'watch_query_and_post_type']);

        }


        /**
         * Runs on disabling plugin
         * 
         * @param null
         * @return boolean
         */
        public function deactivate()
        {
            // fired in actions
        }


        /**
         * Run, non-filtered
         * 
         * @param null
         * @return boolean
         */
        public function run()
        {
            // echo '<pre>';
            // var_dump($this);
            // var_dump($_REQUEST);
            // echo '</pre>';

            if ($mode = @$_REQUEST[$this->query_for_pdf] && get_post_type() == $this->custom_post_type) {

                $this->request_uri = $this->webserver . str_replace(get_home_url(), '', get_permalink());
                $this->request_uri .= '?' . $this->query_print . '=1';
                $this->filename = self::get_pdf_filename();
                $this->file_path = $this->pdf_folder . $this->filename;
                $this->file_uri = get_stylesheet_directory_uri() . '/print/' . $this->filename;
                // check mode
                $mode = intval($mode);
                switch ($mode) {
                    case 1:
                        // regular download, if not exist create, if exist download.
                        if (file_exists($this->pdf_folder . self::get_pdf_filename())) {
                            readfile($this->file_path);
                        } else {
                            $this->generatePDF($this->request_uri, $this->file_path);
                            readfile($this->file_path);
                        }
                        break;
                    case 2:
                        // overwrite
                        $this->generatePDF($this->request_uri, $this->file_path);
                        readfile($this->file_path);
                        break;
                    default:
                        $this->generatePDF($this->request_uri, $this->file_path);
                        readfile($this->file_path);
                        break;
                }
                exit;
                // run generate
            }
            if (@$_REQUEST[$this->query_for_print] === '1') {
                // loads template only for print
                include_once(plugin_dir_path(__FILE__) . '/themes/' . $this->theme . '/index.php');
                exit;
            }
        }

        /**
         * Check availability on Gotenberg service
         * 
         * @param $service_uri ? (from options page loaded in class)
         * @return boolean
         */
        public function check_service()
        {
            return true; // false
        }

        /**
         * Loads setting from admin-page
         * 
         * @param null
         * @return $options
         */
        public function get_settings()
        {
            $options = get_option('gotenberg_pdf_settings');
            // var_dump($options);
            $this->webserver = $options['webserver_uri'] ?: 'http://nginx:80';
            $this->gotenberg_uri = $options['gotenberg_uri'] ?: 'http://gotenberg:3000';
            $this->query_for_print = $options['query_for_print'] ?: 'print';
            $this->query_for_pdf = $options['query_for_pdf'] ?: 'pdf';
            $this->custom_post_type = $options['custom_post_type'] ?: 'page';
            $this->pdf_folder = get_stylesheet_directory() . '/print/';
            return $options;
        }


        /**
         * Loads template from plugin-folder/templates/
         * 
         * @param $template
         * @return null
         */
        public function load_template()
        {
        }


        /**
         * PDF filename
         * 
         * @param $post

         */

        function watch_query_and_post_type($html)
        {
            if ($this->print == 1) $this->load_template();
            if (get_post_type() == $this->post_type and $this->mode) {
                $this->request_uri = $this->webserver . str_replace(get_home_url(), '', get_permalink());
                $this->request_uri .= '?' . $this->query_print . '=1';
                $this->filename = self::get_pdf_filename();
                $this->file_path = $this->pdf_folder . $this->filename;
                $this->file_uri = get_stylesheet_directory_uri() . '/print/' . $this->filename;
                if ($this::DEBUG) var_dump($this);
                if ($this->mode) {
                    header('Content-type:application/pdf');
                    header('Content-Disposition:attachment; filename="' . $this->filename . '"');
                    switch ($this->mode) {
                        case 1:
                            // regular download, if not exist create, if exist download.
                            if (file_exists($this->pdf_folder . self::get_pdf_filename())) {
                                readfile($this->file_path);
                            } else {
                                $this->generatePDF($this->request_uri, $this->file_path);
                                readfile($this->file_path);
                            }
                            break;
                        case 2:
                            // overwrite
                            $this->generatePDF($this->request_uri, $this->file_path);
                            readfile($this->file_path);
                            break;
                        default:
                            $this->generatePDF($this->request_uri, $this->file_path);
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
            // add_action('admin_menu', [$this, 'add_plugin_options_page']);
            // add_action('admin_init', [$this, 'add_plugin_settings']);
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

        function generatePDF($url, $filepath)
        {
            $client = new Client($this->gotenberg, new \Http\Adapter\Guzzle6\Client());
            $request = new URLRequest($url);
            // // $request->setAssets($assets);
            // $request->setPaperSize(URLRequest::A4);
            // $request->setMargins([0.2, 0.2, 0.2, 0.2]);
            // $request->setWaitDelay(2.5);
            // $request->setWaitTimeout(10);

            // $client->store($request, $filepath);
            // return $request;

            // try {
            $request = new URLRequest($url);
            // $header = DocumentFactory::makeFromPath('header.html', __DIR__ . '/themes/nordmarine/header.html');
            $footer = DocumentFactory::makeFromPath('footer.html', __DIR__ . '/themes/nordmarine/footer.html');
            $request->setPaperSize(URLRequest::A4);
            $request->setMargins([0.4, 0.75, 0.4, 0.4]);
            $request->setWaitDelay(2.5);
            $request->setWaitTimeout(10);
            $request->setFooter($footer);
            // $request->setHeader($header);
            // dd($this);
            $client->store($request, $filepath);
            // return $request;
            try {
                return $client->post($request);
            } catch (RequestException $e) {
                var_dump($this);
                # this exception is thrown if given paper size or margins are not correct.
            } catch (ClientException $e) {
                # this exception is thrown by the client if the API has returned a code != 200.
                var_dump($this);
            } catch (\Exception $e) {
                # some (random?) exception.
            }
        }
    }
endif;
$plugin = new GOTENGERG_SERVICE();
$plugin->load();
