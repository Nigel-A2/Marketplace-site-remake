<?php
namespace AIOSEO\Plugin\Common\Sitemap;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles sitemap localization logic.
 *
 * @since 4.2.1
 */
class Localization {
	/**
	 * This is cached so we don't do the lookup each query.
	 *
	 * @since 4.0.0
	 *
	 * @var boolean
	 */
	private static $wpml = null;

	/**
	 * Class constructor.
	 *
	 * @since 4.2.1
	 */
	public function __construct() {
		if ( aioseo()->helpers->isWpmlActive() ) {
			self::$wpml = [
				'defaultLanguage' => apply_filters( 'wpml_default_language', null ),
				'activeLanguages' => apply_filters( 'wpml_active_languages', null )
			];

			add_filter( 'aioseo_sitemap_term', [ $this, 'localizeEntry' ], 10, 3 );
			add_filter( 'aioseo_sitemap_post', [ $this, 'localizeEntry' ], 10, 3 );
		}
	}

	/**
	 * Localize the entries if WPML (or others in the future) are active.
	 *
	 * @since 4.0.0
	 *
	 * @param  array   $entry     The entry.
	 * @param  WP_Post $entryId   The post/term ID.
	 * @param  string  $entryType The post/term type.
	 * @param  boolean $rss       Whether or not we are localizing for the RSS sitemap.
	 * @return array              The entry.
	 */
	public function localizeEntry( $entry, $entryId, $entryType ) {
		$translationGroupId = apply_filters( 'wpml_element_trid', null, $entryId );
		$translations       = apply_filters( 'wpml_get_element_translations', null, $translationGroupId, $entryType );
		if ( empty( $translations ) ) {
			return $entry;
		}

		$entry['languages'] = [];
		foreach ( $translations as $translation ) {
			if ( empty( $translation->element_id ) || ! isset( self::$wpml['activeLanguages'][ $translation->language_code ] ) ) {
				continue;
			}

			if ( (int) $entryId === (int) $translation->element_id ) {
				$entry['language'] = $translation->language_code;
				continue;
			}

			$translatedId = apply_filters( 'wpml_object_id', $entryId, $entryType, false, $translation->language_code );
			$location = get_permalink( $translatedId );

			// Special treatment for the home page translations.
			if ( 'page' === get_option( 'show_on_front' ) && aioseo()->helpers->wpmlIsHomePage( $entryId ) ) {
				$location = aioseo()->helpers->wpmlHomeUrl( $translation->language_code );
			}

			$currentLanguage = ! empty( self::$wpml['activeLanguages'][ $translation->language_code ] ) ? self::$wpml['activeLanguages'][ $translation->language_code ] : null;
			$languageCode    = ! empty( $currentLanguage['tag'] ) ? $currentLanguage['tag'] : $translation->language_code;

			$entry['languages'][] = [
				'language' => $languageCode,
				'location' => $location
			];
		}

		if ( empty( $entry['languages'] ) ) {
			unset( $entry['languages'] );
		} else {
			$entry['languages'][] = [
				'language' => $entry['language'],
				'location' => $entry['loc']
			];
		}

		return $entry;
	}
}