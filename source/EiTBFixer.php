<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Dom\Document;
use ic\Framework\Dom\Element;

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

			$id   = $this->getIdFromUrl($url);
			$info = null;

			if (count($id) === 2) {
				$info = $this->getInfoFromApi($id);
			}

			if ($info) {
				if ($id['type'] === 'video' && isset($info->FILE_MP4, $info->IMAGENEMBED, $info->TITULO, $info->URL)) {
					$video  = $this->getVideo($dom, $info->FILE_MP4, $this->addScheme($info->IMAGENEMBED));
					$figure = $this->getFigure($dom, $video, $info->TITULO, $info->URL);
				} else if (isset($info->URL_AUDIO)) {
					$audio  = $this->getAudio($dom, $info->URL_AUDIO);
					$figure = $this->getFigure($dom, $audio);
				}
			}

			if (isset($figure)) {
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
	protected function getIdFromUrl(string $url): array
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
	 * @param array $id
	 *
	 * @return null|object
	 */
	protected function getInfoFromApi(array $id): ?object
	{
		$query  = $this->getApiQuery($id);
		$result = get_transient($query);

		if ($result === false) {
			$result  = 'null';
			$request = [
				'user-agent' => 'ic HTTP/2.0',
				'sslverify'  => false,
				'headers'    => ['Accept-Encoding' => 'gzip'],
				'cookies'    => [],
				'body'       => null,
			];

			$response = wp_remote_request($this->url($query), $request);

			if (wp_remote_retrieve_response_code($response) === 200) {
				$result = json_decode(wp_remote_retrieve_body($response), false);
			}

			set_transient($query, $result, DAY_IN_SECONDS);
		}

		return $result === 'null' ? null : $result;
	}

	/**
	 * @param array $id
	 *
	 * @return string
	 */
	protected function getApiQuery(array $id): string
	{
		$query = self::QUERY . self::$parameters[$id['type']];
		$query = str_replace(['%ID%', '%TYPE%'], [
			$id['id'],
			$id['type'],
		], $query);

		return $query;
	}
}
