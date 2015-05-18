<?


abstract class Cacheable
{
	private $filename = '';
	private $out_time = 0;
	
	//override to process input to new cache file!
	abstract protected function process($buf_in);
	
	
	public static function addversion($file_name, $out_time = false)
	{
		if(!$out_time)
		{
			$out_time = filemtime($file_name);
		}
		return $file_name.'?v='.dechex($out_time);
	}

	protected function readfile($filein)
	{
		return file_get_contents($filein);
	}

	public function __construct($filein, $out_ext = '.cache', $cache_dir ='cache')
	{
		$fileout = self::get_cache_name($filein, $out_ext, $cache_dir);
		if(file_exists($fileout))
		{
			$out_time = filemtime($fileout);
		}
		else
		{
			$out_time = 0;
		}
		
		if(filemtime($filein) > $out_time)
		{
			$buf_in = $this->readfile($filein);
			$cache_content = $this->process($buf_in);
			file_put_contents($fileout, $cache_content);
			$out_time = filemtime($fileout);
		}
		$this->out_time = $out_time;
		$this->filename = $fileout;
	}
	
	public function get_url()
	{
		return self::addversion($this->filename,$this->out_time);
	}
	
	public function get_contents()
	{
		return file_get_contents($this->filename);
	}

	
	private static function get_cache_name($input, $out_fext, $cache_dir)
	{
		$pi = pathinfo($input);
		$path = $cache_dir.'/';
		if(!file_exists($path))
		{
			mkdir($path);
		}
		return $path.$pi['filename'].$out_fext;
		//['filename'] beware! only available since PHP 5.2.0
	}
}