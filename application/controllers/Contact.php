<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends MY_Controller
{
	public function index()
	{
		$data['page_title'] = lang_line('contact');
		$this->add_frame_view('contact',$data);
	}
	
	protected function send_mail( $subject, $message, $name, $mail )
	{
		$this->load->library( 'email' );
		
		$config['protocol']	= 'mail';
		$config['wordwrap']	= TRUE;

		$this->email->initialize( $config );

		$this->email->from( $mail, $name );
		
		$this->email->to( 'info@ensoft.dev' );
		$this->email->cc( 'martin.golini@gmail.com' );
		
		$this->email->subject( $subject );

		$this->email->message( $message );

		$this->email->send();
	}
	
	protected function verify_recaptcha( $secret, $response )
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"secret={$secret}&response={$response}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);
		$resp = json_decode($server_output);
		
		if ( null != $resp && isset($resp->success) && $resp->success == true )
		{
			return true;
		}
		
		return false;
	}
	
	public function mail()
	{
		$arr = $this->input->post();

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', lang_line('name'), 'required|min_length[2]' );
		$this->form_validation->set_rules('mail', lang_line('email'), 'required|valid_email' );
		$this->form_validation->set_rules('message', lang_line('message'), 'required|min_length[3]' );
		
		if ( $this->form_validation->run() )
		{
			if ( $this->verify_recaptcha( CIBLOG_RECAPTCHA_SECRET, $arr['g-recaptcha-response'] ) )
			{
				$subject = PAGE_TITLE . ' ' . lang_line('email_from') . ' ' . $arr['name'];
				
				$this->send_mail( $subject, $arr['message'], $arr['name'], $arr['mail'] );
				
				$this->kajax->html( '#contact-box', '<div style="padding:30px;"><strong><p>' . lang_line('mail_send_success') . '</p><p>' . lang_line('thanks_for_contacting_us') . '.</p><p>' . lang_line('in_touch_soon') . '</p></strong></div>' );
				$this->kajax->out();
			}
			else
			{
				$this->kajax->fadeIn('.form-error',500);
				$this->kajax->html( '.form-error', lang_line('invalid_captcha_validation') );
				$this->kajax->out();
			}
		}
		else
		{
			$err_str = validation_errors();
			$this->kajax->fadeIn('.form-error',500);
			$this->kajax->html( '.form-error', $err_str );
			$this->kajax->out();
		}
	}
}
