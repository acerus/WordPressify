<?php
/**
 * Provides write operation context via the WordPress file system API
 */
class Loco_fs_FileWriter {

   /**
    * @var Loco_fs_File
    */
    private $file;

    /**
     * @var WP_Filesystem_Direct
     */
    private $fs;


    public function __construct( Loco_fs_File $file ){
        $this->file = $file;
        $this->disconnect();
    }
    
    
    /**
     * @return Loco_fs_FileWriter
     */
    public function setFile( Loco_fs_File $file ){
        $this->file = $file;
        return $this;
    }
    


    /**
     * Connect to alternative file system context
     * @return Loco_fs_FileWriter
     * @throws Loco_error_WriteException
     */
    public function connect( WP_Filesystem_Base $fs, $disconnected = true ){
        if( $disconnected && ! $fs->connect() ){
            $errors = $fs->errors;
            if( is_wp_error($errors) ){
                foreach( $errors->get_error_messages() as $reason ){
                    Loco_error_AdminNotices::warn($reason);
                }
            }
            throw new Loco_error_WriteException( __('Failed to connect to remote server','loco-translate') );
        }
        $this->fs = $fs;
        return $this;
    }
    
    
    /**
     * Revert to direct file system connection
     * @return Loco_fs_FileWriter
     */
    public function disconnect(){
        $this->fs = Loco_api_WordPressFileSystem::direct();
        return $this;
    }


    /**
     * Get mapped path for use in indirect file system manipulation
     * @return string
     */
    public function getPath(){
        return $this->mapPath( $this->file->getPath() );
    }


    /**
     * @internal
     * Map virtual path for remote file system
     */
    private function mapPath( $path ){
        if( ! $this->isDirect() ){
            $base = rtrim( loco_constant('WP_CONTENT_DIR'), '/' );
            $snip = strlen($base);
            if( substr( $path, 0, $snip ) !== $base ){
                // fall back to default path in case of symlinks
                $base = rtrim(ABSPATH,'/').'/wp-content';
                $snip = strlen($base);
                if( substr( $path, 0, $snip ) !== $base ){
                    throw new Loco_error_WriteException('Remote path must be under WP_CONTENT_DIR');
                }
            }
            $virt = $this->fs->wp_content_dir();
            if( false === $virt ){
                throw new Loco_error_WriteException('Failed to find WP_CONTENT_DIR via remote connection');
            }
            $virt = rtrim( $virt, '/' );
            $path = substr_replace( $path, $virt, 0, $snip );
        }
        return $path;
    }


    /**
     * Test if a direct (not remote) file system
     * @return bool
     */
    public function isDirect(){
        return $this->fs instanceof WP_Filesystem_Direct;
    }


    /**
     * @return bool
     */
    public function writable(){
        return ! $this->disabled() && $this->fs->is_writable( $this->getPath() );
    }


    /**
     * @return Loco_fs_FileWriter
     * @throws Loco_error_WriteException
     */
    public function chmod( $mode, $recursive = false ){
        $this->authorize();
        if( ! $this->fs->chmod( $this->getPath(), $mode, $recursive ) ){
            throw new Loco_error_WriteException( sprintf( __('Failed to chmod %s','loco-translate'), $this->file->basename() ) );
        }
        return $this;
    }



    /**
     * @return Loco_fs_FileWriter
     * @throws Loco_error_WriteException
     */
    public function copy( Loco_fs_File $copy ){
        $this->authorize();
        $source = $this->getPath();
        $target = $this->mapPath( $copy->getPath() );
        // bugs in WP file system "exists" methods mean forcing $overwrite, but checking reliably first
        if( $copy->exists() ){
            throw new Loco_error_WriteException( __('Refusing to copy over an existing file','loco-translate') );
        }
        if( ! $this->fs->copy( $source, $target, true ) ){
            throw new Loco_error_WriteException( sprintf( __('Failed to copy %s to %s','loco-translate'), basename($source), basename($target) ) );
        }

        return $this;
    }



    /**
     * @return Loco_fs_FileWriter
     * @throws Loco_error_WriteException
     */
    public function delete( $recursive = false ){
        $this->authorize();
        if( ! $this->fs->delete( $this->getPath(), $recursive ) ){
            throw new Loco_error_WriteException( sprintf( __('Failed to delete %s','loco-translate'), $this->file->basename() ) );
        }

        return $this;
    }



    /**
     * @return Loco_fs_FileWriter
     * @throws Loco_error_WriteException
     */
    public function putContents( $data ){
        $this->authorize();
        $file = $this->file;
        if( $file->isDirectory() ){
            throw new Loco_error_WriteException( sprintf( __('"%s" is a directory, not a file','loco-translate'), $file->basename() ) );
        }
        // avoid chmod of existing file
        if( $file->exists() ){
            $mode = $file->mode();
        }
        // may have bypassed definition of FS_CHMOD_FILE
        else {
            $mode = defined('FS_CHMOD_FILE') ? FS_CHMOD_FILE : 0644;
        }
        $path = $this->getPath();
        if( ! $this->fs->put_contents( $path, $data, $mode ) ){
            // provide useful reason for failure if possible
            if( $file->exists() && ! $this->fs->is_writable($path) ){
                throw new Loco_error_WriteException( __("Permission denied to update file",'loco-translate') );
            }
            // else check directory exists in which to create a new file
            else if( ( $dir = $file->getParent() ) && ! $dir->exists() ){
                throw new Loco_error_WriteException( __("Parent directory doesn't exist",'loco-translate') );
            }
            // else reason for failure is not established
            throw new Loco_error_WriteException( __('Failed to save file','loco-translate').': '.$file->basename() );
        }
        
        return $this;
    }



    /**
     * @return Loco_fs_FileWriter
     * @throws Loco_error_WriteException
     */
     public function mkdir(){
        $this->authorize();
        $fs = $this->fs;
        // may have bypassed definition of FS_CHMOD_DIR
        $mode = defined('FS_CHMOD_DIR') ? FS_CHMOD_DIR : 0755;
        // find first ancestor that exists while building tree
        $stack = array();
        $here = $this->file;
        /* @var $parent Loco_fs_Directory */
        while( $parent = $here->getParent() ){
            array_unshift( $stack, $this->mapPath( $here->getPath() ) );
            if( $parent->exists() ){
                // have existent directory, now build full path
                foreach( $stack as $path ){
                    if( ! $fs->mkdir( $path, $mode ) ){
                        throw new Loco_error_WriteException( __('Failed to create directory','loco-translate') );
                    }
                }
                return true;
            }
            $here = $parent;
        }
        throw new Loco_error_WriteException( __('Failed to build directory path','loco-translate') );
    }



    /**
     * Check whether write operations are permitted, or throw
     * @throws Loco_error_WriteException
     * @return Loco_fs_FileWriter
     */
    public function authorize(){
        if( $this->disabled() ){
            throw new Loco_error_WriteException( __('File modification is disallowed by your WordPress config','loco-translate') );
        }
        // deny system file changes (fs_protect = 2)
        if( 1 < Loco_data_Settings::get()->fs_protect && $this->file->getUpdateType() ){
            throw new Loco_error_WriteException( __('Modification of installed files is disallowed by the plugin settings','loco-translate') );
        }
        return $this;
    } 



    /**
     * Check if file system modification is banned at WordPress level
     * @return bool
     */
    public function disabled(){
        // WordPress >= 4.8
        if( function_exists('wp_is_file_mod_allowed') ){
            $context = apply_filters( 'loco_file_mod_allowed_context', 'download_language_pack', $this->file );
            return ! wp_is_file_mod_allowed( $context );
        }
        // fall back to direct constant check
        return (bool) loco_constant('DISALLOW_FILE_MODS');
    }

}
