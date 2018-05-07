<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Html\Document;
use ic\Framework\Html\Element;

/**
 * Class DIPCFixer
 *
 * @package ic\Plugin\VideoFixer
 */
class DIPCFixer extends AbstractFixer implements FixerInterface
{

	protected const URL = 'http://dipc.tv/';

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

			if ($url !== $this->url('player.swf')) {
				continue;
			}

			wp_parse_str($element->getAttribute('flashvars'), $info);

			if (!isset($info['file'], $info['image'])) {
				continue;
			}

			$video  = $this->getVideo($dom, $info['file'], $info['image']);
			$figure = $this->getFigure($dom, $video);

			$element->parentNode->parentNode->replaceChild($figure, $element->parentNode);
		}

		return $dom;
	}

}
