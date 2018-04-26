<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Html\Document;
use ic\Framework\Plugin\PluginClass;

/**
 * Class Frontend
 *
 * @package ic\Plugin\VideoFixer
 */
class Frontend extends PluginClass
{

	/**
	 * @var array
	 */
	protected $videoFixers = [
		EiTBFixer::class,
		DIPCFixer::class,
		IAAFixer::class,
	];

	/**
	 * @inheritdoc
	 */
	protected function onCreation()
	{
		$this->setHook()->on('the_content', 'fixVideos');
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	protected function fixVideos(string $content): string
	{
		$applyFixers = [];

		foreach ($this->videoFixers as $fixer) {
			/** @var FixerInterface $videoFixer */
			$videoFixer = new $fixer();

			if ($videoFixer->check($content)) {
				$applyFixers[] = $videoFixer;
			}
		}

		if (!empty($applyFixers)) {
			$dom = new Document();
			$dom->loadMarkup($content);

			foreach ($applyFixers as $videoFixer) {
				$dom = $videoFixer->fix($dom);
			}

			$content = $dom->saveMarkup();
		}

		return $content;
	}

}