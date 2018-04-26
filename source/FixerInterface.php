<?php

namespace ic\Plugin\VideoFixer;

use ic\Framework\Html\Document;

/**
 * Interface FixerInterface
 *
 * @package ic\Plugin\VideoFixer
 */
interface FixerInterface
{

	/**
	 * @param string $content
	 *
	 * @return bool
	 */
	public function check(string $content): bool;

	/**
	 * @param Document $content
	 *
	 * @return Document
	 */
	public function fix(Document $content): Document;

}