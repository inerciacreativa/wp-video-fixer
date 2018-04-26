<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Html\Document;
use ic\Framework\Html\Element;

/**
 * Class IAAFixer
 *
 * @package ic\Plugin\VideoFixer
 */
class IAAFixer extends AbstractFixer implements FixerInterface
{

	protected const URL = 'http://old.iaa.es/';

	protected const TAG = 'embed';

	/**
	 * @inheritdoc
	 */
	public function fix(Document $dom): Document
	{
		$elements = $dom->getElementsByTagName($this->tag());

		/** @var Element $element */
		foreach ($elements as $element) {
			$url = trim($element->getAttribute('src'));

			if ((strpos($url, 'player.swf') === false) || (strpos($url, $this->url()) !== 0)) {
				continue;
			}

			wp_parse_str($element->getAttribute('flashvars'), $info);

			$video  = $this->getVideo($dom, $info['file'], $info['image']);
			$figure = $this->getFigure($dom, $video);

			$element->parentNode->parentNode->replaceChild($figure, $element->parentNode);
		}

		return $dom;
	}

}