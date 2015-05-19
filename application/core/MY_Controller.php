<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'third_party/cacheable.inc.php');
require_once(APPPATH.'third_party/jsmin.php');


class  MY_Controller  extends  CI_Controller
{
	public static $JS_DEBUG = true; //false = compact in one file and minify

	public static $JS_PATH = './';

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('file');
		
		$this->load->library('session');
		
		$this->auto_add();
		
		$this->session_recover();
	}

	protected $a_css = array();
	protected $a_js = array();
	protected $a_rss = array();
    protected $a_ext_js = array();

	public function error_404()
	{
		$this->output->set_status_header('404');
		$this->add_frame_view('404');
	}

	//override to avoid auto_add, or to add more files
	protected function auto_add()
	{
		//$this->addless('assets/style.less');
		$this->addjs('assets/js/jquery-1.11.3.min.js');
		$this->addjs('assets/js/jquery.color.js');
		$this->addjs('assets/js/jquery.placeholder.min.js');
		$this->addjs('assets/js/jquery.autosize-min.js');
		$this->addjs('ckeditor/ckeditor.js');
		$this->addjs('ckeditor/adapters/jquery.js');
		$this->addjs('ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js');
		$this->addjs('assets/js/kajax.js');
		$this->addjs('assets/js/site.js');
		$this->addcss('assets/css/global.css');
		$this->addcss('ckeditor/plugins/codesnippet/lib/highlight/styles/obsidian.css');
		$this->addrss('/rss');
	}

	private static function get_file_hash($file)
	{
		$time_in = filemtime($file);
		return hash('md5',$time_in);
	}

	private static function css_tag($curr_css, $file)
	{
		return $curr_css."\t".'<link rel="stylesheet" type="text/css" href="'.base_url($file).'" />'."\n";
	}

	private static function rss_tag($curr_rss, $file)
	{
		return $curr_rss."\t".'<link rel="alternate" type="application/rss+xml" title="ensoft RSS" href="'.base_url($file).'" />'."\n";
	}
	
	private static function js_tag($curr_js, $file)
	{
		return $curr_js.'<script type="text/javascript" src="'.base_url($file).'"></script>'."\n";
	}

    private static function ext_js_tag($curr_js, $file)
    {
        return $curr_js.'<script type="text/javascript" src="'.$file.'"></script>'."\n";
    }

	private static function js_combine($curr_js, $file)
	{
		$buf = file_get_contents(self::$JS_PATH.$file);
		if(!strstr($file,'min'))//not minified already
		{
			$buf = JSMin::minify($buf);
		}
		return $curr_js.' { '.$buf.' } ';
	}

	private function render_css()
	{
		return array_reduce($this->a_css,'MY_Controller::css_tag');	
	}

	private function render_rss()
	{
		return array_reduce($this->a_rss,'MY_Controller::rss_tag');	
	}
	
	private function render_js()
	{
		if(self::$JS_DEBUG)
		{
			return array_reduce($this->a_js,'MY_Controller::js_tag');
		}
		else
		{
			$all_js =  array_reduce($this->a_js,'MY_Controller::js_combine');
			$filepath = 'assets/cache/site.js';
			file_put_contents(self::$JS_PATH.$filepath, $all_js);
			return self::js_tag('', $filepath);
		}
	}

    private function render_ext_js()
    {
        return array_reduce($this->a_ext_js,'MY_Controller::ext_js_tag');
    }

	public function addcss($file, $add_version = true)
	{
		if($add_version)
		{
			$file = Cacheable::addversion($file);
		}
		$this->a_css[] = $file;
	}

	public function addrss($file)
	{
		$this->a_rss[] = $file;
	}
	
	public function addjs($file, $add_version = true)
    {
        if($add_version && self::$JS_DEBUG)
        {
            $file = Cacheable::addversion($file);
        }
        $this->a_js[] = $file;
    }

    public function addextjs($file)
    {
         $this->a_ext_js[] = $file;
    }

	public function addless($file)
	{
		$this->load->library('less');
		$this->addcss($this->less->parse($file), false);
	}

    public function isKajaxRequest()
    {
        return $this->input->post('kajax') == true;
    }

	public function add_frame_view($content_view, $data = array())
	{
        if ( $this->isKajaxRequest()  )
        {
            $this->load->view($content_view, $data);
        }
        else
        {
            $frame_data = array();
            $frame_data['content'] = $this->load->view($content_view, $data, true);
            $frame_data['css'] = $this->render_css();
            $frame_data['rss'] = $this->render_rss();
            $frame_data['js'] = $this->render_ext_js(); //external js first, usuarlly local js depend on those
            $frame_data['js'] .= $this->render_js();

			$frame_data['page_title'] = isset($data['page_title']) ? ' - '.$data['page_title'] : '';
            
            $bar_data = array();
            
      		if ( $this->session_exists() )
      		{
				$bar_data['is_admin'] = TRUE;
			}
			
      		$frame_data['bar'] = $this->load->view('/frame/bar', $bar_data, TRUE);
      		
            $this->load->view('/frame/frame', $frame_data);
        }
	}
	
	protected function session_exists()
	{
		return isset( $this->sess ) && isset( $this->sess['id'] );
	}
	
	protected function session_recover()
	{
		$this->sess = $this->session->all_userdata();
	}
	
	protected function sess()
	{
		return $this->sess;
	}

	protected function create_session( $user, $pass )
	{
		$this->load->model( 'user_model' );
		
		$user = $this->user_model->by_user( $user, $pass );
		
		if ( !empty( $user ) )
		{
			$data = array(
				'id' => $user->id,
				'name' => $user->name,
				'token' => hash( 'sha256', time() + $user->id ),
				'is_logged_in' => true
			);
			
			$this->session->set_userdata( $data );
			
			return TRUE;
		}
		
		return FALSE;
	}
}
