<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'third_party/cacheable.inc.php');
require_once(APPPATH.'third_party/jsmin.php');

class MY_Controller extends CI_Controller
{
	public static $JS_CACHE			= FALSE; // TRUE = compact in one file and minify
	public static $JS_CACHE_LIBS	= FALSE; // Only cache files on the libs path
	public static $JS_USE_CACHED	= FALSE; // Loads the already cached site/libs
	public static $JS_PATH			= './';

	protected $a_css		= array();
	protected $a_ext_css	= array();
	protected $a_js			= array();
	protected $a_ext_js		= array();
	protected $a_og			= array();
	protected $a_rss		= array();
	protected $sections		= NULL;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load_menu();
		
		$this->auto_add();
	}
	
	protected function load_menu()
	{
		$this->load->model('Categories_model');
		
		$this->sections = $this->Categories_model->get_menu_sections();
	}
	
	public function error_404()
	{
		$this->output->set_status_header('404');
		$this->add_frame_view('404');
	}

	//override to avoid auto_add, or to add more files
	protected function auto_add()
	{
		$this->add_js('assets/libs/jquery/jquery-3.4.1.min.js');
		$this->add_js('assets/libs/jquery.scrollTo/jquery.scrollTo.min.js');
		$this->add_js('assets/libs/jquery.placeholder/jquery.placeholder.min.js');
		$this->add_js('assets/libs/alertify.js/alertify.min.js');
		
		$this->add_js('ckeditor/ckeditor.js');
		$this->add_js('ckeditor/adapters/jquery.js');
		$this->add_js('ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js');
		
		$this->add_js('assets/js/kajax.js');
		$this->add_js('assets/js/site.js');
		
		$this->add_base_css();
		$this->add_css('assets/css/font-awesome.min.css');
		$this->add_css('assets/libs/alertify.js/alertify.core.css');
		$this->add_css('assets/libs/alertify.js/alertify.default.css');
		
		$this->add_css('ckeditor/plugins/codesnippet/lib/highlight/styles/obsidian.css');
		
		$this->add_rss('/rss');
	}
	
	protected function add_base_css()
	{
		$this->add_css('assets/css/global.css');
	}

	protected static function get_file_hash($file)
	{
		$time_in = filemtime($file);
		return hash('md5',$time_in);
	}

	protected static function css_tag($curr_css, $file)
	{
		return $curr_css."\t".'<link rel="stylesheet" type="text/css" href="'.base_url($file).'" />'."\n";
	}

	protected static function ext_css_tag($curr_css, $file)
	{
		return $curr_css."\t".'<link rel="stylesheet" type="text/css" href="'.$file.'" />'."\n";
	}

	protected static function js_tag($curr_js, $file)
	{
		if ( self::$JS_CACHE_LIBS && strstr( $file, 'libs/' ) )
		{
			return $curr_js;
		}
		
		return $curr_js.'<script type="text/javascript" src="'.base_url($file).'"></script>'."\n";
	}

	protected static function ext_js_tag($curr_js, $file)
	{
		return $curr_js.'<script type="text/javascript" src="'.$file.'"></script>'."\n";
	}

	protected static function og_tag($curr_og, $metadata)
	{
		return $curr_og."\t".'<meta property="' . $metadata['property'] . '" content="' . $metadata['content']. '" />'."\n";
	}
	
	protected function add_ext_js( $file )
	{
		$this->a_ext_js[] = $file;
	}

	protected function add_ext_css( $file )
	{
		$this->a_ext_css[] = $file;
	}

	protected function add_og( $property, $content )
	{
		$this->a_og[] = array( 'property' => $property, 'content' => $content );
	}

	protected function add_less($file)
	{
		$this->load->library('less');
		$this->add_css($this->less->parse($file), false);
	}

	protected static function js_combine($curr_js, $file)
	{
		if ( !self::$JS_CACHE_LIBS || ( self::$JS_CACHE_LIBS && strstr( $file, 'libs/' ) ) )
		{
			$buf = file_get_contents( self::$JS_PATH . $file );
		
			if( !strstr( $file, 'min' ) )//not minified already
			{
				$buf = JSMin::minify( $buf );
			}
		
			return $curr_js . ' { '.$buf.' } ';
		}
		else
		{
			return $curr_js;
		}
	}

	protected function render_css()
	{
		return array_reduce($this->a_css,'MY_Controller::css_tag');	
	}

	protected function render_ext_css()
	{
		return array_reduce($this->a_ext_css,'MY_Controller::ext_css_tag');	
	}

	protected function render_js()
	{
		if(!self::$JS_CACHE||self::$JS_USE_CACHED)
		{
			return array_reduce($this->a_js,'MY_Controller::js_tag');
		}
		else
		{
			$all_js =  array_reduce($this->a_js,'MY_Controller::js_combine');
			$filepath = 'assets/cache/' . ( self::$JS_CACHE_LIBS ? 'libs' : 'site' ) . '.js';
			file_put_contents(self::$JS_PATH.$filepath, $all_js);
			$js_final = self::js_tag('', $filepath);
			return $js_final . array_reduce($this->a_js,'MY_Controller::js_tag');
		}
	}

	protected function render_ext_js()
	{
		return array_reduce($this->a_ext_js,'MY_Controller::ext_js_tag');
	}

	protected function render_og()
	{
		return array_reduce($this->a_og,'MY_Controller::og_tag');	
	}

	protected function add_css($file, $add_version = FALSE)
	{
		if ( !in_array( $file, $this->a_css ) )
		{
			if($add_version)
			{
				$file = Cacheable::addversion($file);
			}
			$this->a_css[] = $file;
		}
	}

	protected function add_js($file, $add_version = FALSE)
	{
		if ( self::$JS_USE_CACHED && !strstr( $file, 'cache/' ) )
		{
			if ( !self::$JS_CACHE_LIBS || ( self::$JS_CACHE_LIBS && strstr( $file, 'libs/' ) ) )
			{
				return;
			}
		}
		
		if ( !in_array( $file, $this->a_js ) )
		{
			if($add_version && !self::$JS_CACHE)
			{
				$file = Cacheable::addversion($file);
			}
			
			$this->a_js[] = $file;
		}
	}
	
	private static function rss_tag($curr_rss, $file)
	{
		return $curr_rss."\t".'<link rel="alternate" type="application/rss+xml" title="' . PAGE_TITLE . ' RSS" href="'.base_url($file).'" />'."\n";
	}
	
	private function render_rss()
	{
		return array_reduce($this->a_rss,'MY_Controller::rss_tag');	
	}
	
	protected function add_rss($file)
	{
		$this->a_rss[] = $file;
	}

	protected function is_kajax_request()
	{
		return $this->input->is_ajax_request() && $this->input->post('kajax') == true;
	}
	
	protected function add_frame_view( $content_view, $data = array(), $show_header = TRUE, $show_footer = TRUE, $return = FALSE, $hf_folder = 'frame' )
	{
		if ( $this->is_kajax_request() )
		{
			$inject = '';
			
			if ( isset($data['page_title']) )
			{
				$this->kajax->text('title', PAGE_TITLE . ' - ' . $data['page_title'] );
				
				$inject = $this->kajax->out( TRUE, TRUE );
			}
			
			if ( !$return )
			{
				$this->load->view($content_view, $data);
				echo $inject;
			}
			else
			{
				return $this->load->view($content_view, $data, TRUE ) . $inject;
			}
		}
		else
		{
			$frame_data = array();
			$frame_data['content']		= $this->load->view($content_view, $data, true);
			$frame_data['css']			= $this->render_css();
			$frame_data['rss'] 			= $this->render_rss();
			$frame_data['js']			= $this->render_ext_js() . $this->render_js(); //external js first, usuarlly local js depend on those
			$frame_data['og']			= $this->render_og();
			$frame_data['page_title']	= isset($data['page_title']) ? ' - '.$data['page_title'] : '';
			$frame_data['clean_view']	= null !== get_var( 'clean_view' );
			
			$hf_data['clean_view']		= $frame_data['clean_view'];
			$hf_data['sections'] 		= $this->sections;
			
			if ( isset( $data['_user'] ) )
			{
				$hf_data['user'] = $data['_user'];
			}
			
			if ( $show_header )
			{
				$frame_data['header']	= $this->load->view('/' . $hf_folder . '/header', $hf_data, TRUE);
			}
			
			if ( $show_footer )
			{
				$frame_data['footer']	= $this->load->view('/' . $hf_folder . '/footer', $hf_data, TRUE);
			}
			
			if ( !$return )
			{
				$this->load->view('/frame/frame', $frame_data );
			}
			else
			{
				return $this->load->view('/frame/frame', $frame_data, TRUE );
			}
		}
	}
	
	protected function redirect( $uri )
	{
		if ( $this->input->is_ajax_request() )
		{
			$this->kajax->redirect($uri);
			$this->kajax->out( TRUE );
			die();
		}
		else
		{
			redirect($uri);
		}
	}
	
	protected function kajax_validation_set_input_states( $rules, $id, $rows_extra = NULL, $row_name = '#row_' )
	{
		if ( !empty( $rules ) )
		{
			$rows = array( $row_name . ( intval( $id ) == 0 ? 'new' : $id ) );
			
			if ( NULL != $rows_extra )
			{
				if ( is_array( $rows_extra ) && !empty( $rows_extra ) )
				{
					foreach ( $rows_extra as $re )
					{
						array_push( $rows, $re . ( intval( $id ) == 0 ? 'new' : $id ) );
					}
				}
				else if ( is_string( $rows_extra ) )
				{
					array_push( $rows, $rows_extra . ( intval( $id ) == 0 ? 'new' : $id ) );
				}
			}
			
			foreach ( $rows as $row_id )
			{
				$this->kajax_validate_inputs( $rules, $row_id );
			}
		}
	}
	
	protected function kajax_validate_inputs( $rules, $id = '' )
	{
		if ( !empty( $rules ) )
		{
			foreach ( $rules as $r )
			{
				$field	= $r['field'];
				
				if ( isset( $r['type'] ) && 'textarea' == $r['type'] )
				{
					$target	= ( '' != $id ? $id . ' ' : '' ) . 'textarea[name="' . $field . '"]';
				}
				else
				{
					$target	= ( '' != $id ? $id . ' ' : '' ) . 'input[name="' . $field . '"]';
				}
				
				if ( '' != form_error( $field ) )
				{
					$this->kajax->addClass( $target, 'error' );
				}
				else
				{
					$this->kajax->removeClass( $target, 'error' );
				}
			}
		}
	}
	
	/**
	 * @param $id Is the new id of the elements
	 * @param $elements_base_name is an array of elements base names, this means: if element is called 'row_new', the base name is 'row_'
	 * */
	protected function kajax_replace_new_ids( $id, $elements_base_name )
	{
		foreach( $elements_base_name as $el )
		{
			$this->kajax->attr( '#' . $el . 'new', 'id', $el . $id );
		}
	}
}
