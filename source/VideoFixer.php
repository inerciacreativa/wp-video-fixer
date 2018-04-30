<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Plugin\Plugin;

/**
 * Class EiTBVideos
 *
 * @package ic\Plugin\EiTBVideos
 */
class VideoFixer extends Plugin
{

	protected function configure(): void
	{
		parent::configure();

		$this->setOptions([]);
	}

}
