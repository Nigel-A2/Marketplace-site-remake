<?php
namespace AIOSEO\Plugin\Common\Options;

use AIOSEO\Plugin\Common\Traits;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the dynamic options.
 *
 * @since 4.1.4
 */
class DynamicOptions {
	use Traits\Options;

	/**
	 * The default options.
	 *
	 * @since 4.1.4
	 *
	 * @var array
	 */
	protected $defaults = [
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
		'sitemap'          => [
			'priority' => [
				'postTypes'  => [],
				'taxonomies' => []
			]
		],
		'social'           => [
			'facebook' => [
				'general' => [
					'postTypes' => []
				]
			]
		],
		'searchAppearance' => [
			'postTypes'  => [],
			'taxonomies' => [],
			'archives'   => []
		]
		// phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	];

	/**
	 * Class constructor.
	 *
	 * @since 4.1.4
	 *
	 * @param string $optionsName The options name.
	 */
	public function __construct( $optionsName = 'aioseo_options_dynamic' ) {
		$this->optionsName = is_network_admin() ? $optionsName . '_network' : $optionsName;

		// Load defaults in case this is a complete fresh install.
		$this->init();

		add_action( 'shutdown', [ $this, 'save' ] );
	}

	/**
	 * Initializes the options.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	protected function init() {
		$this->addDynamicDefaults();
		$this->setDbOptions();
	}

	/**
	 * Sets the DB options.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	protected function setDbOptions() {
		$dbOptions = $this->getDbOptions( $this->optionsName );

		// Refactor options.
		$this->defaultsMerged = array_replace_recursive( $this->defaults, $this->defaultsMerged );

		$dbOptions = array_replace_recursive(
			$this->defaultsMerged,
			$this->addValueToValuesArray( $this->defaultsMerged, $dbOptions )
		);

		aioseo()->core->optionsCache->setOptions( $this->optionsName, $dbOptions );

		// Get the localized options.
		$dbOptionsLocalized = get_option( $this->optionsName . '_localized' );
		if ( empty( $dbOptionsLocalized ) ) {
			$dbOptionsLocalized = [];
		}
		$this->localized = $dbOptionsLocalized;
	}

	/**
	 * Sanitizes, then saves the options to the database.
	 *
	 * @since 4.1.4
	 *
	 * @param  array $options An array of options to sanitize, then save.
	 * @return void
	 */
	public function sanitizeAndSave( $options ) {
		if ( ! is_array( $options ) ) {
			return;
		}

		$cachedOptions = aioseo()->core->optionsCache->getOptions( $this->optionsName );

		aioseo()->dynamicBackup->maybeBackup( $cachedOptions );

		// Refactor options.
		$dbOptions = array_replace_recursive(
			$cachedOptions,
			$this->addValueToValuesArray( $cachedOptions, $options, [], true )
		);

		aioseo()->core->optionsCache->setOptions( $this->optionsName, $dbOptions );

		// Update localized options.
		update_option( $this->optionsName . '_localized', $this->localized );

		// Update values.
		$this->save( true );
	}

	/**
	 * Adds some defaults that are dynamically generated.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	public function addDynamicDefaults() {
		$this->addDynamicPostTypeDefaults();
		$this->addDynamicTaxonomyDefaults();
		$this->addDynamicArchiveDefaults();
	}

	/**
	 * Adds the dynamic defaults for the public post types.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	protected function addDynamicPostTypeDefaults() {
		$postTypes = aioseo()->helpers->getPublicPostTypes();
		foreach ( $postTypes as $postType ) {
			if ( 'type' === $postType['name'] ) {
				$postType['name'] = '_aioseo_type';
			}

			$defaultTitle       = '#post_title #separator_sa #site_title';
			$defaultDescription = $postType['hasExcerpt'] ? '#post_excerpt' : '#post_content';
			$defaultSchemaType  = 'WebPage';
			$defaultWebPageType = 'WebPage';
			$defaultArticleType = 'BlogPosting';

			switch ( $postType['name'] ) {
				case 'post':
					$defaultSchemaType = 'Article';
					break;
				case 'attachment':
					$defaultDescription = '#attachment_caption';
					$defaultSchemaType  = 'ItemPage';
					$defaultWebPageType = 'ItemPage';
					break;
				case 'product':
					$defaultSchemaType  = 'WebPage';
					$defaultWebPageType = 'ItemPage';
					break;
				case 'news':
					$defaultArticleType = 'NewsArticle';
					break;
				default:
					break;
			}

			$defaultOptions = array_replace_recursive(
				$this->getDefaultSearchAppearanceOptions(),
				[
					'title'           => [
						'type'      => 'string',
						'localized' => true,
						'default'   => $defaultTitle
					],
					'metaDescription' => [
						'type'      => 'string',
						'localized' => true,
						'default'   => $defaultDescription
					],
					'schemaType'      => [
						'type'    => 'string',
						'default' => $defaultSchemaType
					],
					'webPageType'     => [
						'type'    => 'string',
						'default' => $defaultWebPageType
					],
					'articleType'     => [
						'type'    => 'string',
						'default' => $defaultArticleType
					],
					'customFields'    => [ 'type' => 'html' ],
					'advanced'        => [
						'bulkEditing' => [
							'type'    => 'string',
							'default' => 'enabled'
						]
					]
				]
			);

			if ( 'attachment' === $postType['name'] ) {
				$defaultOptions['redirectAttachmentUrls'] = [
					'type'    => 'string',
					'default' => 'attachment'
				];
			}

			$this->defaults['searchAppearance']['postTypes'][ $postType['name'] ] = $defaultOptions;
			$this->setDynamicSocialOptions( 'postTypes', $postType['name'] );
			$this->setDynamicSitemapOptions( 'postTypes', $postType['name'] );
		}
	}

	/**
	 * Adds the dynamic defaults for the public taxonomies.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	protected function addDynamicTaxonomyDefaults() {
		$taxonomies = aioseo()->helpers->getPublicTaxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			if ( 'type' === $taxonomy['name'] ) {
				$taxonomy['name'] = '_aioseo_type';
			}

			$defaultOptions = array_replace_recursive(
				$this->getDefaultSearchAppearanceOptions(),
				[
					'title'           => [
						'type'      => 'string',
						'localized' => true,
						'default'   => '#taxonomy_title #separator_sa #site_title'
					],
					'metaDescription' => [
						'type'      => 'string',
						'localized' => true,
						'default'   => '#taxonomy_description'
					],
				]
			);

			$this->defaults['searchAppearance']['taxonomies'][ $taxonomy['name'] ] = $defaultOptions;
		}
	}

	/**
	 * Adds the dynamic defaults for the archive pages.
	 *
	 * @since 4.1.4
	 *
	 * @return void
	 */
	protected function addDynamicArchiveDefaults() {
		$postTypes = aioseo()->helpers->getPublicPostTypes( false, true );
		foreach ( $postTypes as $postType ) {
			if ( 'type' === $postType['name'] ) {
				$postType['name'] = '_aioseo_type';
			}

			$defaultOptions = array_replace_recursive(
				$this->getDefaultSearchAppearanceOptions(),
				[
					'title'           => [
						'type'      => 'string',
						'localized' => true,
						'default'   => '#archive_title #separator_sa #site_title'
					],
					'metaDescription' => [
						'type'      => 'string',
						'localized' => true,
						'default'   => ''
					],
					'customFields'    => [ 'type' => 'html' ],
					'advanced'        => [
						'keywords' => [
							'type'      => 'string',
							'localized' => true
						]
					]
				]
			);

			$this->defaults['searchAppearance']['archives'][ $postType['name'] ] = $defaultOptions;
		}
	}

	/**
	 * Returns the search appearance options for dynamic objects.
	 *
	 * @since 4.1.4
	 *
	 * @return array The default options.
	 */
	protected function getDefaultSearchAppearanceOptions() {
		return [ // phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
			'show'     => [ 'type' => 'boolean', 'default' => true ],
			'advanced' => [
				'robotsMeta'                => [
					'default'         => [ 'type' => 'boolean', 'default' => true ],
					'noindex'         => [ 'type' => 'boolean', 'default' => false ],
					'nofollow'        => [ 'type' => 'boolean', 'default' => false ],
					'noarchive'       => [ 'type' => 'boolean', 'default' => false ],
					'noimageindex'    => [ 'type' => 'boolean', 'default' => false ],
					'notranslate'     => [ 'type' => 'boolean', 'default' => false ],
					'nosnippet'       => [ 'type' => 'boolean', 'default' => false ],
					'noodp'           => [ 'type' => 'boolean', 'default' => false ],
					'maxSnippet'      => [ 'type' => 'number', 'default' => -1 ],
					'maxVideoPreview' => [ 'type' => 'number', 'default' => -1 ],
					'maxImagePreview' => [ 'type' => 'string', 'default' => 'large' ]
				],
				'showDateInGooglePreview'   => [ 'type' => 'boolean', 'default' => true ],
				'showPostThumbnailInSearch' => [ 'type' => 'boolean', 'default' => true ],
				'showMetaBox'               => [ 'type' => 'boolean', 'default' => true ]
			]
		]; // phpcs:enable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	}

	/**
	 * Sets the dynamic social settings for a given post type or taxonomy.
	 *
	 * @since 4.1.4
	 *
	 * @param  string $objectType Whether the object belongs to the dynamic "postTypes" or "taxonomies".
	 * @param  string $objectName The object name.
	 * @return void
	 */
	protected function setDynamicSocialOptions( $objectType, $objectName ) {
		$defaultOptions = [
			'objectType' => [
				'type'    => 'string',
				'default' => 'article'
			]
		];

		$this->defaults['social']['facebook']['general'][ $objectType ][ $objectName ] = $defaultOptions;
	}

	/**
	 * Sets the dynamic sitemap settings for a given post type or taxonomy.
	 *
	 * @since 4.1.4
	 *
	 * @param  string $objectType Whether the object belongs to the dynamic "postTypes" or "taxonomies".
	 * @param  string $objectName The object name.
	 * @return void
	 */
	protected function setDynamicSitemapOptions( $objectType, $objectName ) {
		$this->defaults['sitemap']['priority'][ $objectType ][ $objectName ] = [
			'priority'  => [
				'type'    => 'string',
				'default' => '{"label":"default","value":"default"}'
			],
			'frequency' => [
				'type'    => 'string',
				'default' => '{"label":"default","value":"default"}'
			]
		];
	}
}