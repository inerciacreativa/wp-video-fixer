<?php
/**
 * Plugin Name: ic Video Fixer
 * Plugin URI:  https://github.com/inerciacreativa/wp-video-fixer
 * Version:     2.0.1
 * Text Domain: ic-video-fixer
 * Domain Path: /languages
 * Description: Soluciona problemas al insertar vídeos de EiTB, DIPC y IAA.
 * Author:      Jose Cuesta
 * Author URI:  https://inerciacreativa.com/
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

if (!defined('ABSPATH')) {
	exit;
}

include_once __DIR__ . '/vendor/autoload.php';

ic\Plugin\VideoFixer\VideoFixer::create(__FILE__);
