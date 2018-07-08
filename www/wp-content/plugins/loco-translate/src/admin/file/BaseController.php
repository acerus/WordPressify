<?php
/**
 * Base class for a file resource belonging to a bundle
 * Root > List > Bundle > Resource
 */
abstract class Loco_admin_file_BaseController extends Loco_admin_bundle_BaseController {
    
    /**
     * @var Loco_Locale
     */
    private $locale;


    /**
     * @return Loco_Locale
     */
    protected function getLocale(){
        return $this->locale;
    }


    /**
     * Check file is valid or return error
     * @return string rendered error
     */
    protected function getFileError( Loco_fs_File $file = null ){
        // file must exist for editing
        if( is_null($file) || ! $file->exists() ){
            return $this->view( 'admin/errors/file-missing', array() );
        }
        if( $file->isDirectory() ){
            $this->set('info', Loco_mvc_FileParams::create($file) );
            return $this->view( 'admin/errors/file-isdir', array() );
        }
        
        return '';
    }



    /**
     * {@inheritdoc}
     */
    public function init(){
        parent::init();

        // views at this level are always related to a file 
        // file is permitted to be missing during this execution.
        $path = $this->get('path');
        if( ! $path ){
            throw new Loco_error_Exception('path argument required');
        }
        $file = new Loco_fs_LocaleFile( $path );
        $file->normalize( loco_constant('WP_CONTENT_DIR') );

        // POT file has no locale
        $ext = $file->extension();
        if( 'pot' === $ext ){
            $locale = null;
        }
        // else file may have a locale suffix (unless invalid, such as "default.po")
        else {
            $locale = $file->getLocale();
        }
        
        if( $locale && $locale->isValid() ){
            $this->locale = $locale;
            $code = (string) $locale;
            $this->set( 'locale', new Loco_mvc_ViewParams( array(
                'code' => $code,
                'lang' => $locale->lang,
                'icon' => $locale->getIcon(),
                'name' => $locale->ensureName( new Loco_api_WordPressTranslations ),
                'href' => Loco_mvc_AdminRouter::generate('lang-view', array('locale'=>$code) ),
            ) ) );
        }
        else {
            $this->set( 'locale', null );
        }

        $this->set('file', $file );
        $this->set('filetype', strtoupper($ext) );
        $this->set('title', $file->basename() );
    
        // navigate up to root from this bundle sub view
        $bundle = $this->getBundle();
        $breadcrumb = Loco_admin_Navigation::createBreadcrumb( $bundle );
        $this->set( 'breadcrumb', $breadcrumb );
        
        // navigate between sub view siblings for this resource
        $tabs = new Loco_admin_Navigation;
        $this->set( 'tabs', $tabs );
        $actions = array (
            'file-edit' => __('Editor','loco-translate'),
            'file-view' => __('Source','loco-translate'),
            'file-info' => __('File info','loco-translate'),
            'file-diff' => __('Restore','loco-translate'),
            'file-delete' => __('Delete','loco-translate'),
        );
 
        $suffix = $this->get('action');
        $prefix = $this->get('type');
        foreach( $actions as $action => $name ){
            $href = Loco_mvc_AdminRouter::generate( $prefix.'-'.$action, $_GET );
            $tabs->add( $name, $href, $action === $suffix );
        }
        
        // Provide common language creation link if project scope is is valid
        try {
            $project = $this->getProject();
            $args = array( 'bundle' => $bundle->getHandle(), 'domain' => $project->getId() );
            $this->set( 'msginit', new Loco_mvc_ViewParams( array (
                'href' => Loco_mvc_AdminRouter::generate( $prefix.'-msginit', $args ),
                'text' => __('New language','loco-translate'),
            ) ) );
        }
        catch( Exception $e ){
            
        }
    }



    /**
     * {@inheritdoc}
     */
    public function view( $tpl, array $args = array() ){
        
        if( $breadcrumb = $this->get('breadcrumb') ){
            
            // Add project name into breadcrumb if not the same as bundle name
            try {
                $project = $this->getProject();
                if( $project->getName() !== $this->getBundle()->getName() ){
                    $breadcrumb->add( $project->getName() );
                }
            }
            catch( Loco_error_Exception $e ){
                // ignore missing project in breadcrumb
            }
            
            // Always add page title as final breadcrumb element
            $title = $this->get('title') or $title = 'Untitled';
            $breadcrumb->add( $title );
        }
        
        return parent::view( $tpl, $args );
    }
    
    
    
}