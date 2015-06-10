<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}
/**
 *
 * Event Espresso
 *
 * Event Registration and Ticketing Management Plugin for WordPress
 *
 * @package            Event Espresso
 * @author            Event Espresso
 * @copyright        (c) 2008-2011 Event Espresso  All Rights Reserved.
 * @license            http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @link                    http://www.eventespresso.com
 * @version            $VID:$
 *
 * ------------------------------------------------------------------------
 */



/**
 * EE_Registry Class
 *
 * Centralized Application Data Storage and Management
 *
 * @package                Event Espresso
 * @subpackage            core
 * @author                    Brent Christensen
 */
class EE_Registry {

	/**
	 *    EE_Registry Object
	 * @var EE_Registry $_instance
	 * @access    private
	 */
	private static $_instance = null;

	/**
	 * @var array $_class_abbreviations
	 * @access    private
	 */
	protected $_class_abbreviations = array();

	/**
	 *    EE_Cart Object
	 * @access    public
	 * @var    EE_Cart $CART
	 */
	public $CART = null;

	/**
	 *    EE_Config Object
	 * @access    public
	 * @var    EE_Config $CFG
	 */
	public $CFG = null;

	/**
	 * EE_Network_Config Object
	 * @access public
	 * @var EE_Network_Config $NET_CFG
	 */
	public $NET_CFG = null;

	/**
	 *    StdClass object for storing library classes in
	 * @public LIB
	 */
	public $LIB = null;

	/**
	 *    EE_Request_Handler Object
	 * @access    public
	 * @var    EE_Request_Handler $REQ
	 */
	public $REQ = null;

	/**
	 *    EE_Session Object
	 * @access    public
	 * @var    EE_Session $SSN
	 */
	public $SSN = null;

	/**
	 * holds the ee capabilities object.
	 *
	 * @since 4.5.0
	 *
	 * @var EE_Capabilities
	 */
	public $CAP = null;

	/**
	 *    $addons - StdClass object for holding addons which have registered themselves to work with EE core
	 * @access    public
	 * @var    EE_Addon[]
	 */
	public $addons = null;

	/**
	 *    $models
	 * @access    public
	 * @var    EEM_Base[] $models keys are 'short names' (eg Event), values are class names (eg 'EEM_Event')
	 */
	public $models = array();

	/**
	 *    $modules
	 * @access    public
	 * @var    EED_Module[] $modules
	 */
	public $modules = null;

	/**
	 *    $shortcodes
	 * @access    public
	 * @var    EES_Shortcode[] $shortcodes
	 */
	public $shortcodes = null;

	/**
	 *    $widgets
	 * @access    public
	 * @var    WP_Widget[] $widgets
	 */
	public $widgets = null;

	/**
	 * $non_abstract_db_models
	 * @access public
	 * @var array this is an array of all implemented model names (i.e. not the parent abstract models, or models
	 * which don't actually fetch items from the DB in the normal way (ie, are not children of EEM_Base))
	 */
	public $non_abstract_db_models = array();

	/**
	 *    $i18n_js_strings - internationalization for JS strings
	 *    usage:   EE_Registry::i18n_js_strings['string_key'] = __( 'string to translate.', 'event_espresso' );
	 *    in js file:  var translatedString = eei18n.string_key;
	 *
	 * @access    public
	 * @var    array
	 */
	public static $i18n_js_strings = array();

	/**
	 *    $main_file - path to espresso.php
	 *
	 * @access    public
	 * @var    array
	 */
	public $main_file;



	/**
	 * @singleton method used to instantiate class object
	 * @access public
	 * @return EE_Registry instance
	 */
	public static function instance() {
		// check if class object is instantiated
		if ( ! self::$_instance instanceof EE_Registry ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}



	/**
	 *protected constructor to prevent direct creation
	 * @Constructor
	 * @access protected
	 * @return \EE_Registry
	 */
	protected function __construct() {
		$this->_class_abbreviations = array(
			'EE_Config'          => 'CFG',
			'EE_Session'         => 'SSN',
			'EE_Capabilities'    => 'CAP',
			'EE_Cart' => 'CART',
			'EE_Network_Config'  => 'NET_CFG',
			'EE_Request_Handler' => 'REQ',
		);
		// class library
		$this->LIB = new StdClass();
		$this->addons = new StdClass();
		$this->modules = new StdClass();
		$this->shortcodes = new StdClass();
		$this->widgets = new StdClass();
		$this->load_core( 'Base', array(), true );
		add_action( 'AHEE__EE_System__set_hooks_for_core', array( $this, 'init' ) );
	}



	/**
	 *    init
	 *
	 * @access    public
	 * @return    void
	 */
	public function init() {
		// Get current page protocol
		$protocol = isset( $_SERVER[ 'HTTPS' ] ) ? 'https://' : 'http://';
		// Output admin-ajax.php URL with same protocol as current page
		self::$i18n_js_strings[ 'ajax_url' ] = admin_url( 'admin-ajax.php', $protocol );
		self::$i18n_js_strings[ 'wp_debug' ] = defined( 'WP_DEBUG' ) ? WP_DEBUG : false;
	}



	/**
	 * @param mixed string | EED_Module $module
	 */
	public function add_module( $module ) {
		if ( $module instanceof EED_Module ) {
			$module_class = get_class( $module );
			$this->modules->{$module_class} = $module;
		} else {
			if ( ! class_exists( 'EE_Module_Request_Router' ) ) {
				$this->load_core( 'Module_Request_Router' );
			}
			$this->modules->{$module} = EE_Module_Request_Router::module_factory( $module );
		}
	}



	/**
	 * @param string $module_name
	 * @return mixed EED_Module | NULL
	 */
	public function get_module( $module_name = '' ) {
		return isset( $this->modules->{$module_name} ) ? $this->modules->{$module_name} : null;
	}



	/**
	 *    loads core classes - must be singletons
	 *
	 * @access    public
	 * @param string $class_name - simple class name ie: session
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @param bool $resolve_dependencies
	 * @return mixed
	 */
	public function load_core( $class_name, $arguments = array(), $load_only = false, $resolve_dependencies = false ) {
		$core_paths = apply_filters(
			'FHEE__EE_Registry__load_core__core_paths',
			array(
				EE_CORE,
				EE_ADMIN,
				EE_CPTS,
				EE_CORE . 'data_migration_scripts' . DS
			)
		);
		// retrieve instantiated class
		return $this->_load( $core_paths, 'EE_', $class_name, 'core', $arguments, false, true, $load_only, $resolve_dependencies );
	}



	/**
	 *    loads service classes
	 *
	 * @access    public
	 * @param string $class_name - simple class name ie: session
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @return mixed
	 */
	public function load_service( $class_name, $arguments = array(), $load_only = false ) {
		$service_paths = apply_filters(
			'FHEE__EE_Registry__load_service__service_paths',
			array(
				EE_CORE . 'services' . DS,
			)
		);
		// retrieve instantiated class
		return $this->_load( $service_paths, 'EE_', $class_name, 'class', $arguments, false, true, $load_only, true );
	}



	/**
	 *    loads data_migration_scripts
	 *
	 * @access    public
	 * @param string $class_name - class name for the DMS ie: EE_DMS_Core_4_2_0
	 * @param mixed $arguments
	 * @return EE_Data_Migration_Script_Base
	 */
	public function load_dms( $class_name, $arguments = array() ) {
		// retrieve instantiated class
		return $this->_load( EE_Data_Migration_Manager::instance()->get_data_migration_script_folders(), 'EE_DMS_', $class_name, 'dms', $arguments, false, false, false );
	}



	/**
	 *    loads object creating classes - must be singletons
	 *
	 * @param string $class_name - simple class name ie: attendee
	 * @param mixed $arguments - an array of arguments to pass to the class
	 * @param bool $from_db - some classes are instantiated from the db and thus call a different method to instantiate
	 * @param bool $cache if you don't want the class to be stored in the internal cache (non-persistent) then set this to FALSE (ie. when instantiating model objects from client in a loop)
	 * @param bool $load_only whether or not to just load the file and NOT instantiate, or load AND instantiate (default)
	 * @return EE_Base_Class
	 */
	public function load_class( $class_name, $arguments = array(), $from_db = false, $cache = true, $load_only = false ) {
		$paths = apply_filters( 'FHEE__EE_Registry__load_class__paths', array(
			EE_CORE,
			EE_CLASSES,
			EE_BUSINESS
		) );
		// retrieve instantiated class
		return $this->_load( $paths, 'EE_', $class_name, 'class', $arguments, $from_db, $cache, $load_only );
	}



	/**
	 *    loads helper classes - must be singletons
	 *
	 * @param string $class_name - simple class name ie: price
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @return EEH_Base
	 */
	public function load_helper( $class_name, $arguments = array(), $load_only = true ) {
		$helper_paths = apply_filters( 'FHEE__EE_Registry__load_helper__helper_paths', array( EE_HELPERS ) );
		// retrieve instantiated class
		return $this->_load( $helper_paths, 'EEH_', $class_name, 'helper', $arguments, false, true, $load_only );
	}



	/**
	 *    loads core classes - must be singletons
	 *
	 * @access    public
	 * @param string $class_name - simple class name ie: session
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @return mixed
	 */
	public function load_lib( $class_name, $arguments = array(), $load_only = false ) {
		$paths = array(
			EE_LIBRARIES,
			EE_LIBRARIES . 'messages' . DS,
			EE_LIBRARIES . 'shortcodes' . DS,
			EE_LIBRARIES . 'qtips' . DS,
			EE_LIBRARIES . 'payment_methods' . DS,
		);
		// retrieve instantiated class
		return $this->_load( $paths, 'EE_', $class_name, 'lib', $arguments, false, true, $load_only );
	}



	/**
	 *    loads model classes - must be singletons
	 *
	 * @param string $class_name - simple class name ie: price
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @return EEM_Base
	 */
	public function load_model( $class_name, $arguments = array(), $load_only = false ) {
		$paths = apply_filters( 'FHEE__EE_Registry__load_model__paths', array(
			EE_MODELS,
			EE_CORE
		) );
		// retrieve instantiated class
		return $this->_load( $paths, 'EEM_', $class_name, 'model', $arguments, false, true, $load_only );
	}



	/**
	 *    loads model classes - must be singletons
	 *
	 * @param string $class_name - simple class name ie: price
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @return mixed
	 */
	public function load_model_class( $class_name, $arguments = array(), $load_only = true ) {
		$paths = array(
			EE_MODELS . 'fields' . DS,
			EE_MODELS . 'helpers' . DS,
			EE_MODELS . 'relations' . DS,
			EE_MODELS . 'strategies' . DS
		);
		// retrieve instantiated class
		return $this->_load( $paths, 'EE_', $class_name, '', $arguments, false, true, $load_only );
	}



	/**
	 * Determines if $model_name is the name of an actual EE model.
	 * @param string $model_name like Event, Attendee, Question_Group_Question, etc.
	 * @return boolean
	 */
	public function is_model_name( $model_name ) {
		return isset( $this->models[ $model_name ] ) ? true : false;
	}



	/**
	 *    generic class loader
	 *
	 * @param string $path_to_file - directory path to file location, not including filename
	 * @param string $file_name - file name  ie:  my_file.php, including extension
	 * @param string $type - file type - core? class? helper? model?
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @return mixed
	 */
	public function load_file( $path_to_file, $file_name, $type = '', $arguments = array(), $load_only = true ) {
		// retrieve instantiated class
		return $this->_load( $path_to_file, '', $file_name, $type, $arguments, false, true, $load_only );
	}



	/**
	 *    load_addon
	 *
	 * @param string $path_to_file - directory path to file location, not including filename
	 * @param string $class_name - full class name  ie:  My_Class
	 * @param string $type - file type - core? class? helper? model?
	 * @param mixed $arguments
	 * @param bool $load_only
	 * @return EE_Addon
	 */
	public function load_addon( $path_to_file, $class_name, $type = 'class', $arguments = array(), $load_only = false ) {
		// retrieve instantiated class
		return $this->_load( $path_to_file, 'addon', $class_name, $type, $arguments, false, true, $load_only );
	}



	/**
	 *    loads and tracks classes
	 *
	 * @param array $file_paths
	 * @param string $class_prefix - EE  or EEM or... ???
	 * @param bool|string $class_name - $class name
	 * @param string $type - file type - core? class? helper? model?
	 * @param mixed $arguments - an argument or array of arguments to pass to the class upon instantiation
	 * @param bool $from_db - some classes are instantiated from the db and thus call a different method to instantiate
	 * @param bool $cache
	 * @param bool $load_only
	 * @param bool $resolve_dependencies
	 * @return null|object
	 * @internal param string $file_path - file path including file name
	 */
	private function _load( $file_paths = array(), $class_prefix = 'EE_', $class_name = false, $type = 'class', $arguments = array(), $from_db = false, $cache = true, $load_only = false, $resolve_dependencies = false ) {
		// strip php file extension
		$class_name = str_replace( '.php', '', trim( $class_name ) );
		// does the class have a prefix ?
		if ( ! empty( $class_prefix ) && $class_prefix != 'addon' ) {
			// make sure $class_prefix is uppercase
			$class_prefix = strtoupper( trim( $class_prefix ) );
			// add class prefix ONCE!!!
			$class_name = $class_prefix . str_replace( $class_prefix, '', $class_name );
		}
		// return object if it's already cached
		$cached_class = $this->_get_cached_class( $class_name, $class_prefix );
		if ( $cached_class !== null ) {
			return $cached_class;
		}
		// get full path to file
		$path = $this->_resolve_path( $class_name, $type, $file_paths );
		// load the file
		$this->_require_file( $path, $class_name, $type, $file_paths );
		// instantiate the requested object
		$class_obj = $this->_create_object( $class_name, $arguments, $type, $from_db, $load_only, $resolve_dependencies );
		// save it for later... kinda like gum  { : $
		$this->_set_cached_class( $class_obj, $class_name, $class_prefix, $from_db, $cache );
		return $class_obj;
	}



	/**
	 * _get_cached_class
	 *
	 * attempts to find a cached version of the requested class
	 * by looking in the following places:
	 * 		$this->{class_abbreviation} 		 	ie:   	$this->CART
	 * 		$this->{$class_name}           		 	ie:    $this->Some_Class
	 * 		$this->LIB->{$class_name}			ie: 	$this->LIB->Some_Class
	 * 		$this->addon->{$$class_name}	ie: 	$this->addon->Some_Addon_Class
	 *
	 * @access protected
	 * @param string $class_name
	 * @param string $class_prefix
	 * @return null|object
	 */
	protected function _get_cached_class( $class_name, $class_prefix = '' ) {
		if ( isset( $this->_class_abbreviations[ $class_name ] ) ) {
			$class_abbreviation = $this->_class_abbreviations[ $class_name ];
		} else {
			// have to specify something, but not anything that will conflict
			$class_abbreviation = 'FANCY_BATMAN_PANTS';
		}
		// check if class has already been loaded, and return it if it has been
		if ( isset( $this->$class_abbreviation ) && ! is_null( $this->$class_abbreviation ) ) {
			return $this->$class_abbreviation;
		} else if ( isset ( $this->{$class_name} ) ) {
			return $this->{$class_name};
		} else if ( isset ( $this->LIB->$class_name ) ) {
			return $this->LIB->$class_name;
		} else if ( $class_prefix == 'addon' && isset ( $this->addons->$class_name ) ) {
			return $this->addons->$class_name;
		}
		return null;
	}



	/**
	 * _resolve_path
	 *
	 * attempts to find a full valid filepath for the requested class.
	 * loops thru each of the base paths in the $file_paths array and appends : "{classname} . {file type} . php"
	 * then returns that path if the target file has been found and is readable
	 *
	 * @access protected
	 * @param string $class_name
	 * @param string $type
	 * @param array $file_paths
	 * @return string | bool
	 */
	protected function _resolve_path( $class_name, $type = '', $file_paths = array() ) {
		// make sure $file_paths is an array
		$file_paths = is_array( $file_paths ) ? $file_paths : array( $file_paths );
		// cycle thru paths
		foreach ( $file_paths as $key => $file_path ) {
			// convert all separators to proper DS, if no filepath, then use EE_CLASSES
			$file_path = $file_path ? str_replace( array( '/', '\\' ), DS, $file_path ) : EE_CLASSES;
			// prep file type
			$type = ! empty( $type ) ? trim( $type, '.' ) . '.' : '';
			// build full file path
			$file_paths[ $key ] = rtrim( $file_path, DS ) . DS . $class_name . '.' . $type . 'php';
			//does the file exist and can be read ?
			if ( is_readable( $file_paths[ $key ] ) ) {
				return $file_paths[ $key ];
			}
		}
		return false;
	}



	/**
	 * _require_file
	 *
	 * basically just performs a require_once()
	 * but with some error handling
	 *
	 * @access protected
	 * @param string $path
	 * @param string $class_name
	 * @param string $type
	 * @param array $file_paths
	 * @return void
	 * @throws \EE_Error
	 */
	protected function _require_file( $path, $class_name, $type = '', $file_paths = array() ) {
		// don't give up! you gotta...
		try {
			//does the file exist and can it be read ?
			if ( ! $path ) {
				// so sorry, can't find the file
				throw new EE_Error (
					sprintf(
						__( 'The %1$s file %2$s could not be located or is not readable due to file permissions. Please ensure that the following filepath(s) are correct: %3$s', 'event_espresso' ),
						trim( $type, '.' ),
						$class_name,
						'<br />' . implode( ',<br />', $file_paths )
					)
				);
			}
			// get the file
			require_once( $path );
			// if the class isn't already declared somewhere
			if ( class_exists( $class_name, false ) === false ) {
				// so sorry, not a class
				throw new EE_Error(
					sprintf(
						__( 'The %s file %s does not appear to contain the %s Class.', 'event_espresso' ),
						$type,
						$path,
						$class_name
					)
				);
			}

		} catch ( EE_Error $e ) {
			$e->get_error();
		}
	}



	/**
	 * _create_object
	 * Attempts to instantiate the requested class via any of the
	 * commonly used instantiation methods employed throughout EE.
	 * The priority for instantiation is as follows:
	 * 		- abstract classes or any class flagged as "load only" (no instantiation occurs)
	 * 	 	- model objects via their 'new_instance_from_db' method
	 * 	 	- model objects via their 'new_instance' method
	 * 	 	- "singleton" classes" via their 'instance' method
	 *  	- standard instantiable classes via their __constructor
	 * Prior to instantiation, if the $resolve_dependencies flag is set to true,
	 * then the constructor for the requested class will be examined to determine
	 * if any dependencies exist, and if they can be injected.
	 * If so, then those classes will be added to the array of arguments passed to the constructor
	 *
	 * @access protected
	 * @param string $class_name
	 * @param array $arguments
	 * @param string $type
	 * @param bool $from_db
	 * @param bool $load_only
	 * @param bool $resolve_dependencies
	 * @return null | object
	 * @throws \EE_Error
	 */
	protected function _create_object( $class_name, $arguments = array(), $type = '', $from_db = false, $load_only = false, $resolve_dependencies = false ) {
		$class_obj = null;
		// don't give up! you gotta...
		try {
			// create reflection
			$reflector = new ReflectionClass( $class_name );
			// attempt to inject dependencies ?
			if ( $resolve_dependencies && ! $from_db && ! $load_only ) {
				$arguments = $this->_resolve_dependencies( $reflector, $class_name, $arguments );
			}
			$arguments = is_array( $arguments ) ? $arguments : array( $arguments );
			// instantiate the class and add to the LIB array for tracking
			// EE_Base_Classes are instantiated via new_instance by default (models call them via new_instance_from_db)
			if ( $reflector->getConstructor() === null || $reflector->isAbstract() || $load_only ) {
				// no constructor = static methods only... nothing to instantiate, loading file was enough
				//$instantiation_mode = "no constructor";
				return true;
			} else if ( $from_db && method_exists( $class_name, 'new_instance_from_db' ) ) {
				//$instantiation_mode = "new_instance_from_db";
				$class_obj = call_user_func_array( array( $class_name, 'new_instance_from_db' ), $arguments );
			} else if ( method_exists( $class_name, 'new_instance' ) ) {
				//$instantiation_mode = "new_instance";
				$class_obj = call_user_func_array( array( $class_name, 'new_instance' ), $arguments );
			} else if ( method_exists( $class_name, 'instance' ) ) {
				//$instantiation_mode = "instance";
				$class_obj = call_user_func_array( array( $class_name, 'instance' ), $arguments );
			} else if ( $reflector->isInstantiable() ) {
				//$instantiation_mode = "isInstantiable";
				$class_obj = $reflector->newInstanceArgs( $arguments );
			} else if ( ! $load_only ) {
				// heh ? something's not right !
				//$instantiation_mode = 'none';
				throw new EE_Error(
					sprintf(
						__( 'The %s file %s could not be instantiated.', 'event_espresso' ),
						$type,
						$class_name
					)
				);
			}
		} catch ( EE_Error $e ) {
			$e->get_error();
		}
		return $class_obj;
	}



	/**
	 * _resolve_dependencies
	 *
	 * examines the constructor for the requested class to determine
	 * if any dependencies exist, and if they can be injected.
	 * If so, then those classes will be added to the array of arguments passed to the constructor
	 * For example:
	 * 		if attempting to load a class "Foo" with the following constructor:
	 *        __construct( Bar $bar_class, Fighter $grohl_class )
	 * 		then $bar_class and $grohl_class will be added to the $arguments array
	 * 		IF they are NOT already present in the incoming arguments array,
	 * 		and the correct classes can be loaded
	 *
	 * @access protected
	 * @param ReflectionClass $reflector
	 * @param string $class_name
	 * @param array $arguments
	 * @return array
	 */
	protected function _resolve_dependencies( ReflectionClass $reflector, $class_name, $arguments = array() ) {
		// let's examine the constructor
		$constructor = $reflector->getConstructor();
		// whu? huh? nothing?
		if ( ! $constructor ) {
			return $arguments;
		}
		//echo "\n\n class_name: $class_name";
		// get constructor parameters
		$params = $constructor->getParameters();
		// and the keys for the incoming arguments array so that we can compare existing arguments with what is expected
		$argument_keys = array_keys( $arguments );
		//echo "\n\n arguments:\n";
		//var_dump( $arguments );
		//echo "\n argument_keys:\n";
		//var_dump( $argument_keys );
		// now loop thru all of the constructors expected parameters
		foreach ( $params as $index => $param ) {
			// is this a dependency for a specific class ?
			$param_class = $param->getClass() ? $param->getClass()->name : null;
			//echo "\n\n param_class: " . $param_class;
			if (
				// param is not even a class
				$param_class === null ||
				(
					// something already exists in the incoming arguments for this param
					isset( $argument_keys[ $index ], $arguments[ $argument_keys[ $index ] ] ) &&
					// AND it's the correct class
					$arguments[ $argument_keys[ $index ] ] instanceof $param_class
				)
			) {
				// so let's skip this argument and move on to the next
				continue;
			}
			// we might have a dependency... let's try and find it in our cache
			$cached_class = $this->_get_cached_class( $param_class );
			$dependency = null;
			// and grab it if it exists
			if ( $cached_class instanceof $param_class ) {
				$dependency = $cached_class;
			} else if ( $param_class != $class_name ) {
				// or if not cached, then let's try and load it directly
				$core_class = $this->load_core( $param_class );
				// as long as we aren't creating some recursive loading loop
				if ( $core_class instanceof $param_class ) {
					$dependency = $core_class;
				}
			}
			// did we successfully find the correct dependency ?
			if ( $dependency instanceof $param_class ) {
				//echo "\n\n arguments:\n";
				//var_dump( $arguments );
				//echo "\n index: $index";
				//echo "\n dependency:\n";
				//var_dump( $dependency );
				// then let's inject it into the incoming array of arguments at the correct location
				array_splice( $arguments, $index, 1, array( $dependency ) );
				//echo "\n arguments:\n";
				//var_dump( $arguments );
			}
		}
		return $arguments;
	}



	/**
	 * _set_cached_class
	 *
	 * attempts to cache the instantiated class locally
	 * in one of the following places, in the following order:
	 *        $this->{class_abbreviation} 			ie:    $this->CART
	 *        $this->{$class_name}                    	ie:    $this->Some_Class
	 *        $this->addon->{$$class_name} 	ie:    $this->addon->Some_Addon_Class
	 *        $this->LIB->{$class_name} + 		ie:    $this->LIB->Some_Class +
	 * 		+ only classes that are NOT model objects with the $cache flag set to true
	 * 	 	will be cached under LIB (libraries)
	 *
	 * @access protected
	 * @param object $class_obj
	 * @param string $class_name
	 * @param string $class_prefix
	 * @param bool $from_db
	 * @param bool $cache
	 * @return void
	 */
	protected function _set_cached_class( $class_obj, $class_name, $class_prefix = '', $from_db = false, $cache = true ) {
		// return newly instantiated class
		if ( isset( $this->_class_abbreviations[ $class_name ] ) ) {
			$class_abbreviation = $this->_class_abbreviations[ $class_name ];
			$this->$class_abbreviation = $class_obj;
		} else if ( property_exists( $this, $class_name ) ) {
			$this->{$class_name} = $class_obj;
		} else if ( $class_prefix == 'addon' && $cache ) {
			$this->addons->$class_name = $class_obj;
		} else if ( ! $from_db && $cache ) {
			$this->LIB->$class_name = $class_obj;
		}
	}



	/**
	 * Gets the addon by its name/slug (not classname. For that, just
	 * use the classname as the property name on EE_Config::instance()->addons)
	 * @param string $name
	 * @return EE_Addon
	 */
	public function get_addon_by_name( $name ) {
		foreach ( $this->addons as $addon ) {
			if ( $addon->name() == $name ) {
				return $addon;
			}
		}
		return null;
	}



	/**
	 * Gets an array of all the registered addons, where the keys are their names. (ie, what each returns for their name() function) They're already available on EE_Config::instance()->addons as properties, where each property's name is the addon's classname. So if you just want to get the addon by classname, use EE_Config::instance()->addons->{classname}
	 *
	 * @return EE_Addon[] where the KEYS are the addon's name()
	 */
	public function get_addons_by_name() {
		$addons = array();
		foreach ( $this->addons as $addon ) {
			$addons[ $addon->name() ] = $addon;
		}
		return $addons;
	}



	/**
	 * Resets that specified model's instance AND makes sure EE_Registry doesn't keep
	 * a stale copy of it around
	 * @param string $model_name
	 * @return \EEM_Base
	 * @throws \EE_Error
	 */
	public function reset_model( $model_name ) {
		$model = $this->load_model( $model_name );
		$model_class_name = get_class( $model );
		//get that model reset it and make sure we nuke the old reference to it
		if ( is_callable( array( $model_class_name, 'reset' ) ) ) {
			$this->LIB->$model_class_name = $model::reset();
		} else {
			throw new EE_Error( sprintf( __( 'Model %s does not have a method "reset"', 'event_espresso' ), $model_name ) );
		}
		return $this->LIB->$model_class_name;
	}



	/**
	 * Resets the registry and everything in it (eventually, getting it to properly
	 * reset absolutely everything will probably be tricky. right now it just resets
	 * the config, data migration manager, and the models)
	 * @param boolean $hard whether to reset data in the database too, or just refresh
	 * the Registry to its state at the beginning of the request
	 * @param boolean $reinstantiate whether to create new instances of EE_Registry's singletons too,
	 * or just reset without re-instantiating (handy to set to FALSE if you're not sure if you CAN
	 * currently reinstantiate the singletons at the moment)
	 * @return EE_Registry
	 */
	public static function reset( $hard = false, $reinstantiate = true ) {
		$instance = self::instance();
		$instance->load_helper( 'Activation' );
		EEH_Activation::reset();
		$instance->CFG = EE_Config::reset( $hard, $reinstantiate );
		$instance->LIB->EE_Data_Migration_Manager = EE_Data_Migration_Manager::reset();
		$instance->LIB = new stdClass();
		foreach ( array_keys( $instance->non_abstract_db_models ) as $model_name ) {
			$instance->reset_model( $model_name );
		}
		return $instance;
	}



	/**
	 * @override magic methods
	 * @return void
	 */
	final function __destruct() {
	}



	/**
	 * @param $a
	 * @param $b
	 */
	final function __call( $a, $b ) {
	}



	/**
	 * @param $a
	 */
	final function __get( $a ) {
	}



	/**
	 * @param $a
	 * @param $b
	 */
	final function __set( $a, $b ) {
	}



	/**
	 * @param $a
	 */
	final function __isset( $a ) {
	}



	/**
	 * @param $a
	 */
	final function __unset( $a ) {
	}



	/**
	 * @return array
	 */
	final function __sleep() {
		return array();
	}



	final function __wakeup() {
	}



	/**
	 * @return string
	 */
	final function __toString() {
		return '';
	}



	final function __invoke() {
	}



	final function __set_state() {
	}



	final function __clone() {
	}



	/**
	 * @param $a
	 * @param $b
	 */
	final static function __callStatic( $a, $b ) {
	}



}
// End of file EE_Registry.core.php
// Location: ./core/EE_Registry.core.php