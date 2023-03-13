<?php
/**
 * @since 3.6.10
 */
final class WPBDP_FS {

    static function ls( $dir, $args = array() ) {
        if ( ! is_dir( $dir ) )
            return false;

        $dir = wp_normalize_path( untrailingslashit( $dir ) );

		$args = wp_parse_args(
			$args,
			array(
				'recursive' => false,
				'filter'    => false,
				'output'    => 'path',
			)
		);
        extract( $args );

        if ( 'file' == $filter )
            $filter = 'is_file';
        elseif ( 'dir' == $filter )
            $filter = 'is_dir';

        $res = array();
        $h = opendir( $dir );

        while ( false !== ( $entry = readdir( $h ) ) ) {
            if ( '.' == $entry || '..' == $entry )
                continue;

            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            $passes = $filter ? ( (bool) call_user_func( $filter, $path ) ) : true;

            if ( is_dir( $path ) )
                $path = wp_normalize_path( trailingslashit( $path ) );

            $item = $path;

            if ( 'all' == $output || 'details' == $output ) {
                $item = new StdClass();
                $item->path = $path;
                $item->type = is_dir( $path ) ? 'dir' : ( is_link( $path ) ? 'link' : 'file' );
                $item->size = absint( filesize( $path ) );
            }

			$res = array_merge(
				$res,
				$passes ? array( $item ) : array(),
				$recursive && is_dir( $path ) ? self::ls( $path, $args ) : array()
			);
        }

        closedir( $h );

        return $res;
    }

    static function rm( $path, $recursive = false ) {
        if ( ! is_file( $path ) && ! is_dir( $path ) )
            return true;

        if ( $recursive && is_dir( $path ) ) {
            $files = self::ls( $path, 'recursive=0' );

            foreach ( $files as $f ) {
                if ( is_dir( $f ) ) {
                    self::rm( $f, true );
                } else {
                    @unlink( $f );
                }
            }
        }

        return ( is_dir( $path ) ? @rmdir( $path ) : @unlink( $path ) );
    }

    static function rmdir( $path ) {
        if ( ! is_dir( $path ) )
            return true;

        return self::rm( $path, true );
    }

    static function mkdir( $path ) {
        if ( ! wp_mkdir_p( $path ) )
            return false;

        return true;
    }

    static function temp_dir() {
        $sys_tmp = get_temp_dir();
        $dir = $sys_tmp . uniqid( 'wpbdp' ) . DIRECTORY_SEPARATOR;

        if ( ! self::mkdir( $dir ) || ! wp_is_writable( $dir ) )
            return false;

        return $dir;
    }

    static function unzip_to_temp_dir( $zipfile ) {
        $temp_dir = self::temp_dir();
        $res = self::unzip( $zipfile, $temp_dir );

        return array_merge( array( $temp_dir ), $res );
    }

    static function unzip( $zipfile, $destdir ) {
        if ( ! class_exists( 'PclZip' ) )
            require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

        if ( ! wp_is_writable( $destdir ) ) {
			return new WP_Error(
				'dest-not-writable',
				sprintf( _x( 'Destination dir "%s" is not writable.', 'fs helper', 'business-directory-plugin' ), $destdir )
			);
        }

        $normalized = basename( basename( strtolower( $zipfile ), '.zip' ), '.tmp' );
        $normalized = str_replace( array( 'business-directory-', 'businessdirectory' ), '', $normalized );
        $destdir = untrailingslashit( $destdir );

        $zip = new PclZip( $zipfile );
		$files = $zip->extract(
			PCLZIP_OPT_PATH, $destdir,
			PCLZIP_OPT_REMOVE_PATH, $normalized,
			PCLZIP_OPT_ADD_PATH, $normalized
		);

        // Filter '__MACOSX' dir if present.
        self::rmdir( $destdir . DIRECTORY_SEPARATOR . '__MACOSX' );
        self::rmdir( $destdir . DIRECTORY_SEPARATOR . $normalized . DIRECTORY_SEPARATOR . '__MACOSX' );

        return array( wp_normalize_path( untrailingslashit( $destdir ) . DIRECTORY_SEPARATOR . $normalized . DIRECTORY_SEPARATOR ),
                      $normalized );
    }

    static function join() {
        $sep = DIRECTORY_SEPARATOR;
        $args = func_get_args();

        $res = '';
        foreach ( $args as $a ) {
            $a = rtrim( $a, $sep );
            $res .= $a . $sep;
        }

        $res = substr( $res, 0, -1 );

        return $res;
    }

    static function cp( $source, $dest ) {
        if ( is_dir( $dest ) )
            $dest = wp_normalize_path( trailingslashit( $dest ) . basename( $source ) );

        return copy( $source, $dest );
    }

    static function copydir( $dir, $destdir ) {
        if ( ! is_dir( $dir ) || ! is_dir( $destdir ) )
            return false;

        $dir = trailingslashit( $dir );
        $destdir = wp_normalize_path( trailingslashit( $destdir ) );

        $dirname = basename( $dir );

        if ( is_file( $destdir . $dirname ) || is_dir( $destdir . $dirname ) )
            return false;

        self::mkdir( $destdir . $dirname );

        $dirlist = self::ls( $dir, 'recursive=0&output=details' );

        foreach ( $dirlist as $f ) {
            if ( 'file' == $f->type ) {
                if ( ! self::cp( $f->path, $destdir . $dirname ) )
                    return false;
            } elseif ( 'dir' == $f->type ) {
                if ( ! self::copydir( $f->path, $destdir . $dirname ) )
                    return false;
            }
        }

        return true;
    }

    static function movedir( $dir, $destdir ) {
        if ( ! is_dir( $dir ) || ! is_dir( $destdir ) )
            return false;

        $dir = trailingslashit( $dir );
        $destdir = wp_normalize_path( trailingslashit( $destdir ) );

        $dirname = basename( $dir );

        if ( is_file( $destdir . $dirname ) || is_dir( $destdir . $dirname ) )
            return false;

        if ( ! self::copydir( $dir, $destdir ) )
            return false;

		if ( ! self::rmdir( $dir ) ) {
			return false;
		}

        return true;
    }

}
