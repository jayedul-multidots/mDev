<?php
/**
 * Helpful poll vote values.
 *
 * @package Mdev\Gb
 */

declare( strict_types=1 );

namespace Mdev\Gb\HelpfulPoll;

/**
 * An answer to "Was this article helpful?".
 *
 * Keep the values in sync with blocks/helpful-poll/lib/vote-client.js.
 *
 * @since 0.3.0
 */
enum Vote: string {

	case Yes = 'yes';
	case No  = 'no';

	/**
	 * Prefix of the post meta keys holding the counts. Leading underscore:
	 * protected meta, hidden from the Custom Fields panel.
	 *
	 * @since 0.3.0
	 */
	public const META_PREFIX = '_mdev_gb_helpful_';

	/**
	 * Post meta key holding this answer's count.
	 *
	 * @since 0.3.0
	 *
	 * @return string
	 */
	public function meta_key(): string {
		return self::META_PREFIX . $this->value;
	}

	/**
	 * All values, for schema enums.
	 *
	 * @since 0.3.0
	 *
	 * @return list<string>
	 */
	public static function values(): array {
		return array_map( static fn ( self $vote ): string => $vote->value, self::cases() );
	}
}
