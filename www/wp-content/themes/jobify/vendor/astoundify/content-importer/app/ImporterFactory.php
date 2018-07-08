<?php
/**
 * Importer factory
 *
 * @since 1.0.0
 */
class Astoundify_ImporterFactory {

	/**
	 * Instantiate a new import class depending on the type of file(s)
	 *
	 * All files must be of the same type, and only the first file name will
	 * be used to determine the importer type.
	 *
	 * @since 1.0.0
	 * @param array $files An array of files to determine the importer type
	 * @return object|WP_Error The instantiated importer or WP_Error if files are mixed or invalid
	 */
	public static function create( $files = array() ) {
		if ( empty( $files ) ) {
			return new WP_Error( 'no-files', 'No files supplied to ImporterFactory' );
		}

		if ( ! self::is_single_file_type( $files ) ) {
			return new WP_Error( 'multiple-file-types', 'Importing multiple file types is not supported' );
		}

		$file = current( $files );

		if ( false == ( $ext = self::is_valid_file_type( $file ) ) ) {
			return new WP_Error( 'invalid-file', 'Invalid file type supplied to ImporterFactory' );
		}

		$classname = "Astoundify_{$ext}Importer";

		return new $classname( $files );
	}

	/**
	 * Determine if an array of URLs contain the same file type
	 *
	 * @since 1.0.0
	 * @param array $files An array of files to check file types
	 * @return bool True if all file types are equal
	 */
	public static function is_single_file_type( $files ) {
		$previous_ext = false;

		foreach ( $files as $file ) {
			$path_parts = pathinfo( $file );
			$ext = $path_parts['extension'];

			if ( $previous_ext && ( $previous_ext != $ext ) ) {
				return false;
			}

			$previous_ext = $ext;
		}

		return true;
	}

	/**
	 * Determine if the file is a valid import type
	 *
	 * @since 1.0.0
	 * @param string $file The URl to the file to check
	 * @return string|bool False if the type is invalid or the extension if valid
	 */
	public static function is_valid_file_type( $file ) {
		$valid = array( 'json' );

		$path_parts = pathinfo( $file );
		$ext = $path_parts['extension'];

		return in_array( $ext, $valid ) ? $ext : false;
	}

}
