<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Html\Document;
use ic\Framework\Html\Element;

/**
 * Class EiTBFixer
 *
 * @package ic\Plugin\VideoFixer
 */
class EiTBFixer extends AbstractFixer implements FixerInterface
{

	protected const URL = 'http://www.eitb.eus/';

	protected const TAG = 'iframe';

	protected const QUERY = 'es/get/multimedia/%TYPE%_json/id/%ID%';

	static protected $parameters = [
		'video' => '/size/original_imagen',
		'audio' => '',
	];

	/**
	 * @inheritdoc
	 */
	public function fix(Document $dom): Document
	{
		$elements = $dom->getElementsByTagName($this->tag());

		/** @var Element $element */
		foreach ($elements as $element) {
			$url = $element->getAttribute('src');

			if (strpos($url, $this->url()) !== 0) {
				continue;
			}

			$info  = $this->getUrlInfo($url);
			$embed = null;

			if (\count($info) === 2) {
				$embed = $this->getEmbedInfo($info);
			}

			if ($embed) {
				if ($info['type'] === 'video') {
					$video  = $this->getVideo($dom, $embed->FILE_MP4, $this->addScheme($embed->IMAGENEMBED));
					$figure = $this->getFigure($dom, $video, $embed->TITULO, $embed->URL);
				} else {
					$audio  = $this->getAudio($dom, $embed->URL_AUDIO);
					$figure = $this->getFigure($dom, $audio);
				}

				$element->parentNode->parentNode->replaceChild($figure, $element->parentNode);
			} else {
				$url = str_replace($this->url('es/get/multimedia/screen/'), $this->url('es/get/multimedia/embed/'), $url);

				$element->setAttribute('src', $url);
				$element->setAttribute('width', 640);
				$element->setAttribute('height', 480);
			}
		}

		return $dom;
	}

	/**
	 * @param string $url
	 * @param string $scheme
	 *
	 * @return string
	 */
	protected function addScheme(string $url, string $scheme = 'http://'): string
	{
		$url = ltrim($url, '/');

		return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
	}

	/**
	 * @param string $url
	 *
	 * @return array
	 */
	protected function getUrlInfo(string $url): array
	{
		$path = explode('/', parse_url($url, PHP_URL_PATH));
		$info = [];

		foreach ($path as $index => $segment) {
			if (!isset($path[$index + 1])) {
				break;
			}

			if ($segment === 'id') {
				$info['id'] = (int) $path[$index + 1];
			} else if ($segment === 'tipo') {
				// Possible values: 'videos' and 'audio'
				// Strip the 's' from 'videos'
				$info['type'] = rtrim($path[$index + 1], 's');
			}
		}

		return $info;
	}

	/**
	 * @param array $info
	 *
	 * @return null|\stdClass
	 */
	protected function getEmbedInfo(array $info): ?\stdClass
	{
		$query = self::QUERY . self::$parameters[$info['type']];
		$query = str_replace(['%ID%', '%TYPE%'], [
			$info['id'],
			$info['type'],
		], $query);

		$request = [
			'user-agent' => 'ic HTTP/2.0',
			'sslverify'  => false,
			'headers'    => ['Accept-Encoding' => 'gzip'],
			'cookies'    => [],
			'body'       => null,
		];

		$response = wp_remote_request($this->url($query), $request);

		if (wp_remote_retrieve_response_code($response) !== 200) {
			return null;
		}

		return json_decode(wp_remote_retrieve_body($response));
	}

}
