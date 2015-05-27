<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends MY_Controller
{
	public function index()
	{
		$data['page_title'] = 'contacto';
		$this->add_frame_view('contacto',$data);
	}
	
	protected function send_mail( $subject, $message, $name, $mail )
	{
		$this->load->library( 'email' );
		
		$config['protocol']	= 'mail';
		$config['wordwrap']	= TRUE;

		$this->email->initialize( $config );

		$this->email->from( $mail, $name );
		
		$this->email->to( 'webmaster@ensoft-dev.com' );
		$this->email->cc( 'martin.golini@gmail.com' );
		
		$this->email->subject( $subject );

		$this->email->message( $message );

		$this->email->send();
	}
	
	public function mail()
	{
		$arr = $this->input->post();

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Nombre', 'required|min_length[2]' );
		$this->form_validation->set_rules('mail', 'Email', 'required|valid_email' );
		$this->form_validation->set_rules('message', 'Mensaje', 'required|min_length[3]' );
		
		if ( $this->form_validation->run() )
		{
			$subject = PAGE_TITLE . ' email de ' . $arr['name'];
			
			$this->send_mail( $subject, $arr['message'], $arr['name'], $arr['mail'] );
			
			$this->kajax->html( '#contact-box', '<div style="padding:30px;"><strong><p>El email ha sido enviado con &eacute;xito</p><p>Gracias por contactarte con nosotros.</p><p>Nos prondremos en contacto con vos a la brevedad.</p></strong></div>' );
			$this->kajax->out();
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
