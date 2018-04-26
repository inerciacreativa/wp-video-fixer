<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Html\Document;
use ic\Framework\Html\Element;

/**
 * Class AbstractFixer
 *
 * @package ic\Plugin\VideoFixer
 */
abstract class AbstractFixer
{

	protected const URL = '%URL%';

	protected const TAG = '%TAG%';

	protected const WIDTH = 640;

	protected const HEIGHT = 360;

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public function url($path = ''): string
	{
		return rtrim(static::URL, '/') . '/' . ltrim($path, '/');
	}

	/**
	 * @return string
	 */
	public function tag(): string
	{
		return static::TAG;
	}

	/**
	 * @inheritdoc
	 */
	public function check(string $content): bool
	{
		return strpos('<' . $this->tag(), $content) !== null && strpos($this->url(), $content) !== null;
	}

	/**
	 * @param Document $dom
	 * @param string   $source
	 * @param string   $poster
	 *
	 * @return Element
	 */
	protected function getVideo(Document $dom, $source, $poster = null): Element
	{
		/** @var Element $video */
		$video = $dom->createElement('video');

		$video->setAttribute('width', static::WIDTH);
		$video->setAttribute('height', static::HEIGHT);
		$video->setAttribute('preload', 'auto');
		$video->setAttribute('controls', 'controls');
		$video->setAttribute('src', $source);

		if ($poster) {
			$video->setAttribute('poster', $poster);
		}

		return $video;
	}

	/**
	 * @param Document $dom
	 * @param string   $source
	 *
	 * @return Element
	 */
	protected function getAudio(Document $dom, $source): Element
	{
		/** @var Element $audio */
		$audio = $dom->createElement('audio');

		$audio->setAttribute('preload', 'auto');
		$audio->setAttribute('controls', 'controls');
		$audio->setAttribute('src', $source);

		return $audio;
	}

	/**
	 * @param Document $dom
	 * @param Element  $embed
	 * @param string   $title
	 * @param string   $href
	 *
	 * @return Element
	 */
	protected function getFigure(Document $dom, Element $embed, $title = null, $href = null): Element
	{
		/** @var Element $figure */
		$figure = $dom->createElement('figure');
		$figure->setAttribute('class', $embed->tagName);
		$figure->appendChild($embed);

		if ($title && $href) {
			$caption = $dom->createElement('figcaption');
			$link    = $dom->createElement('a', $title);

			$link->setAttribute('href', $this->url($href));

			$caption->appendChild($link);
			$figure->appendChild($caption);
		}

		return $figure;
	}

}