<?php

/**
 * The WordPress importer plugin has a few issues that break
 * serialized data in certain cases. This class is our own
 * patched version that fixes these issues.
 *
 * @since 1.8
 */
class FLBuilderImporter extends WP_Import {

	/**
	 * @since 1.8
	 * @return array
	 */
	function parse( $file ) {

		if ( extension_loaded( 'xml' ) ) {
			$parser = new FLBuilderImportParserXML;
			$result = $parser->parse( $file );

				// If SimpleXML succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) ) {
				return $result;
			}
		} else {
			$result = new WP_Error( 'no_xml', __( 'The xml PHP extension is not installed.', 'fl-builder' ) );
		}

		// We have a malformed XML file, so display the error and fallthrough to regex
		if ( is_wp_error( $result ) ) {
			echo '<pre>';
			if ( 'XML_parse_error' == $result->get_error_code() ) {
				$error = $result->get_error_data();
				echo $error[0] . ':' . $error[1] . ' ' . esc_html( $error[2] );
			} else {
				echo $result->get_error_message();
			}
			echo '</pre>';
		}

			$data = file_get_contents( $file );
			$bad  = preg_match( '#[^\x00-\x7F]#', $data );
		if ( $bad ) {
				echo __( 'Some bad characters were found in the xml file', 'fl-builder' );
		}
			echo '</pre>';
			echo '<p><strong>' . __( 'There was an error when reading this WXR file', 'fl-builder' ) . '</strong><br />';
			echo '<p>' . __( 'Details are shown above. The importer will now try again with a different parser...', 'fl-builder' ) . '</p>';
		$parser = new FLBuilderImportParserRegex();
		return $parser->parse( $file );
	}
}


class FLBuilderImportParserXML extends WXR_Parser_XML {

	function tag_close( $parser, $tag ) {
		switch ( $tag ) {
			case 'wp:comment':
				unset( $this->sub_data['key'], $this->sub_data['value'] ); // remove meta sub_data
				if ( ! empty( $this->sub_data ) ) {
					$this->data['comments'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:commentmeta':
				$this->sub_data['commentmeta'][] = array(
					'key'   => $this->sub_data['key'],
					'value' => $this->sub_data['value'],
				);
				break;
			case 'category':
				if ( ! empty( $this->sub_data ) ) {
					$this->sub_data['name'] = $this->cdata;
					$this->data['terms'][]  = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'wp:postmeta':
				if ( ! empty( $this->sub_data ) ) {
					if ( stristr( $this->sub_data['key'], '_fl_builder_' ) ) {
						FLBuilderImporterDataFix::set_pcre_limit( apply_filters( 'fl_builder_importer_pcre', '23001337' ) );
						if ( '_fl_builder_data_settings' == $this->sub_data['key'] || '_fl_builder_draft_settings' == $this->sub_data['key'] ) {
							$data = FLBuilderImporterDataFix::run( $this->sub_data['value'], false );
						} else {
							$data = FLBuilderImporterDataFix::run( $this->sub_data['value'], true );
							if ( ! $data && ( $data != $this->sub_data['value'] ) ) {
								$data = $this->sub_data['value'];
							}
						}

						if ( is_object( $data ) || is_array( $data ) ) {
							$data = serialize( $data );
						}
						$this->sub_data['value'] = $data;
					}
					$this->data['postmeta'][] = $this->sub_data;
				}
				$this->sub_data = false;
				break;
			case 'item':
				$this->posts[] = $this->data;
				$this->data    = false;
				break;
			case 'wp:category':
			case 'wp:tag':
			case 'wp:term':
				$n = substr( $tag, 3 );
				array_push( $this->$n, $this->data );
				$this->data = false;
				break;
			case 'wp:author':
				if ( ! empty( $this->data['author_login'] ) ) {
					$this->authors[ $this->data['author_login'] ] = $this->data;
				}
				$this->data = false;
				break;
			case 'wp:base_site_url':
				$this->base_url = $this->cdata;
				break;
			case 'wp:wxr_version':
				$this->wxr_version = $this->cdata;
				break;
			default:
				if ( $this->in_sub_tag ) {
					$this->sub_data[ $this->in_sub_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_sub_tag                    = false;
				} elseif ( $this->in_tag ) {
					$this->data[ $this->in_tag ] = ! empty( $this->cdata ) ? $this->cdata : '';
					$this->in_tag                = false;
				}
		}
		$this->cdata         = false;
		$this->base_blog_url = '';
	}
}


/**
 * The Regex parser is the only parser we have found that
 * doesn't break serialized data. It does have two bugs
 * that can break serialized data. Those are calling rtrim
 * on each $importline and adding a newline to each $importline.
 * This class fixes those bugs.
 *
 * @since 1.8
 */
class FLBuilderImportParserRegex extends WXR_Parser_Regex {

	/**
	 * @since 1.8
	 * @return array
	 */
	function parse( $file ) {

		// @codingStandardsIgnoreLine
		$wxr_version = $in_post = false;

		$fp = $this->fopen( $file, 'r' );
		if ( $fp ) {
			while ( ! $this->feof( $fp ) ) {
				$importline = $this->fgets( $fp );

				if ( ! $wxr_version && preg_match( '|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version ) ) {
					$wxr_version = $version[1];
				}

				if ( false !== strpos( $importline, '<wp:base_site_url>' ) ) {
					preg_match( '|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url );
					$this->base_url = $url[1];
					continue;
				}
				if ( false !== strpos( $importline, '<wp:category>' ) ) {
					preg_match( '|<wp:category>(.*?)</wp:category>|is', $importline, $category );
					if ( isset( $category[1] ) ) {
						$this->categories[] = $this->process_category( $category[1] );
					}
					continue;
				}
				if ( false !== strpos( $importline, '<wp:tag>' ) ) {
					preg_match( '|<wp:tag>(.*?)</wp:tag>|is', $importline, $tag );
					if ( isset( $tag[1] ) ) {
						$this->tags[] = $this->process_tag( $tag[1] );
					}
					continue;
				}
				if ( false !== strpos( $importline, '<wp:term>' ) ) {
					preg_match( '|<wp:term>(.*?)</wp:term>|is', $importline, $term );
					if ( isset( $term[1] ) ) {
						$this->terms[] = $this->process_term( $term[1] );
					}
					continue;
				}
				if ( false !== strpos( $importline, '<wp:author>' ) ) {
					preg_match( '|<wp:author>(.*?)</wp:author>|is', $importline, $author );
					if ( isset( $author[1] ) ) {
						$a = $this->process_author( $author[1] );
					}
					$this->authors[ $a['author_login'] ] = $a;
					continue;
				}
				if ( false !== strpos( $importline, '<item>' ) ) {
					$post    = '';
					$in_post = true;
					continue;
				}
				if ( false !== strpos( $importline, '</item>' ) ) {
					$in_post = false;

					FLBuilderImporterDataFix::set_pcre_limit( apply_filters( 'fl_builder_importer_pcre', '23001337' ) );
					$this->posts[] = $this->process_post( $post );
					continue;
				}
				if ( $in_post ) {
					$post .= $importline;
				}
			}

			$this->fclose( $fp );

			// Try to fix any broken builder data.
			foreach ( $this->posts as $post_index => $post ) {
				if ( ! isset( $post['postmeta'] ) || ! is_array( $post['postmeta'] ) ) {
					continue;
				}
				foreach ( $post['postmeta'] as $postmeta_index => $postmeta ) {
					if ( stristr( $postmeta['key'], '_fl_builder_' ) ) {

						if ( '_fl_builder_data_settings' == $postmeta['key'] || '_fl_builder_draft_settings' == $postmeta['key'] ) {
							$data = FLBuilderImporterDataFix::run( $postmeta['value'], false );
						} else {
							$data = FLBuilderImporterDataFix::run( $postmeta['value'], true );
						}
						if ( is_object( $data ) || is_array( $data ) ) {
							$data = serialize( $data );
						}
						$this->posts[ $post_index ]['postmeta'][ $postmeta_index ]['value'] = $data;
					}
				}
			}
		}

		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'fl-builder' ) );
		}

		return array(
			'authors'    => $this->authors,
			'posts'      => $this->posts,
			'categories' => $this->categories,
			'tags'       => $this->tags,
			'terms'      => $this->terms,
			'base_url'   => $this->base_url,
			'version'    => $wxr_version,
		);
	}
}

/**
 * Portions borrowed from https://github.com/Blogestudio/Fix-Serialization/blob/master/fix-serialization.php
 *
 * Attempts to fix broken serialized data.
 *
 * @since 1.8
 */
final class FLBuilderImporterDataFix {

	/**
	 * @since 1.8
	 * @return string
	 */
	static public function run( $data, $linebreaks = true ) {
		// return if empty
		if ( empty( $data ) ) {
			return $data;
		}

		if ( is_object( $data ) || is_array( $data ) ) {
			return $data;
		}

		if ( ! is_serialized( $data ) ) {
			return $data;
		}

		if ( $linebreaks ) {
			$data = preg_replace_callback('!([a-z-_0-9]+)";s:(\d+):"(.*?)";!', function( $m ) {
				// new replace logic.
				if ( 'css' === $m[1] || 'js' === $m[1] || 'html' === $m[1] ) {
					$m[3] = str_replace( '<br data-fl-fixed=true />', "\n", $m[3] );
				}
				$m[3] = str_replace( '<br data-fl-fixed=true />', '<br />', $m[3] );
				return $m[1] . '";s:' . strlen( $m[3] ) . ':"' . $m[3] . '";';
			}, self::sanitize_from_word( $data, $linebreaks ) );
		} else {
			$data = preg_replace_callback('!s:(\d+):"(.*?)";!', function( $m ) {
				return 's:' . strlen( $m[2] ) . ':"' . $m[2] . '";';
			}, self::sanitize_from_word( $data, $linebreaks ) );
		}
		$data = maybe_unserialize( $data );

		// return if maybe_unserialize() returns an object or array, this is good.
		if ( is_object( $data ) || is_array( $data ) ) {
			return $data;
		}

		return preg_replace_callback( '!s:(\d+):([\\\\]?"[\\\\]?"|[\\\\]?"((.*?)[^\\\\])[\\\\]?");!', 'FLBuilderImporterDataFix::regex_callback', $data );
	}

	/**
	 * Remove quotes etc pasted from a certain word processor.
	 */
	public static function sanitize_from_word( $content, $linebreaks ) {
		// Convert microsoft special characters
		$replace = array(
			'‘'  => "'",
			'’'  => "'",
			'”'  => '"',
			'“'  => '"',
			'–'  => '-',
			'—'  => '-',
			'…'  => '&#8230;',
			"\n" => '<br data-fl-fixed=true />',
		);

		if ( ! $linebreaks ) {
			unset( $replace["\n"] );
		}

		foreach ( $replace as $k => $v ) {
			$content = str_replace( $k, $v, $content );
		}

		/**
		 * Optional strip all illegal chars, defaults to false
		 * @see fl_import_strip_all
		 * @since 2.3
		 */
		if ( true === apply_filters( 'fl_import_strip_all', false ) ) {
			// Remove any non-ascii character
			$content = preg_replace( '/[^\x20-\x7E]*/', '', $content );
		}

		return $content;
	}


	/**
	 * @since 1.8
	 * @return string
	 */
	static public function regex_callback( $matches ) {
		if ( ! isset( $matches[3] ) ) {
			return $matches[0];
		}

		return 's:' . strlen( self::unescape_mysql( $matches[3] ) ) . ':"' . self::unescape_quotes( $matches[3] ) . '";';
	}

	/**
	 * Unescape to avoid dump-text issues.
	 *
	 * @since 1.8
	 * @access private
	 * @return string
	 */
	static private function unescape_mysql( $value ) {
		return str_replace( array( '\\\\', "\\0", "\\n", "\\r", '\Z', "\'", '\"' ),
			array( '\\', "\0", "\n", "\r", "\x1a", "'", '"' ),
		$value );
	}

	/**
	 * Fix strange behaviour if you have escaped quotes in your replacement.
	 *
	 * @since 1.8
	 * @access private
	 * @return string
	 */
	static private function unescape_quotes( $value ) {
		return str_replace( '\"', '"', $value );
	}

	/**
	 * Try increasing PCRE limit to avoid failing of importing huge postmeta data.
	 *
	 * @since 1.10.9
	 * @param string $value
	 */
	static public function set_pcre_limit( $value ) {
		@ini_set( 'pcre.backtrack_limit', $value ); // @codingStandardsIgnoreLine
		@ini_set( 'pcre.recursion_limit', $value ); // @codingStandardsIgnoreLine
	}
}
