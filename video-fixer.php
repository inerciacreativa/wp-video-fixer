<?php
/**
 * Plugin Name: ic Video Fixer
 * Plugin URI:  https://github.com/inerciacreativa/wp-video-fixer
 * Version:     4.0.1
 * Text Domain: ic-video-fixer
 * Domain Path: /languages
 * Description: Soluciona problemas al insertar vídeos de EiTB, DIPC y IAA.
 * Author:      Jose Cuesta
 * Author URI:  https://inerciacreativa.com/
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */

use ic\Framework\Framework;
use ic\Plugin\VideoFixer\VideoFixer;

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists(Framework::class)) {
	throw new RuntimeException(sprintf('Could not find %s class.', Framework::class));
}

if (!class_exists(VideoFixer::class)) {
	$autoload = __DIR__ . '/vendor/autoload.php';

	if (file_exists($autoload)) {
		/** @noinspection PhpIncludeInspection */
		include_once $autoload;
	} else {
		throw new RuntimeException(sprintf('Could not load %s class.', VideoFixer::class));
	}
}

VideoFixer::create(__FILE__);
