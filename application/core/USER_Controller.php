<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class USER_Controller extends SESSION_Controller
{
	protected function user_update_data( $allow_change_role = TRUE, $is_profile = FALSE )
	{
		$post = $this->input->post();
		
		$this->load->model('Users_model');
		$this->load->library('form_validation');
		
		$rules	= array(
			array(
				'field'   => 'firstname', 
				'label'   => lang_line_ucwords('first_name'), 
				'rules'   => 'trim|max_length[64]'
			),
			array(
				'field'   => 'lastname',
				'label'   => lang_line_ucwords('last_name'), 
				'rules'   => 'trim|max_length[64]'
			),
			array(
				'field'   => 'nickname',
				'label'   => lang_line_ucwords('nickname'), 
				'rules'   => 'trim|required|max_length[64]'
			),
			array(
				'field'   => 'display_name',
				'label'   => lang_line_ucwords('display_name'), 
				'rules'   => 'trim|required|max_length[100]'
			),
			array(
				'field'   => 'email',
				'label'   => lang_line_ucwords('email'), 
				'rules'   => 'trim|required|valid_email|max_length[64]'
			),
			array(
				'field'   => 'url',
				'label'   => lang_line_ucwords('biographical_info'), 
				'rules'   => 'trim|valid_url|max_length[128]'
			),
			array(
				'field'   => 'bio',
				'label'   => lang_line_ucwords('website'), 
				'rules'   => 'max_length[16384]'
			),
			array(
				'field'   => 'password',
				'label'   => lang_line_ucwords('password'), 
				'rules'   => 'min_length[4]'
			),
			array(
				'field'   => 'password_repeat',
				'label'   => lang_line_ucwords('new_password_repeat'), 
				'rules'   => 'matches[password]'
			)
		);
		
		$id = isset( $post['id'] ) ? intval( $post['id'] ) : 0;
		
		$this->form_validation->set_rules( $rules );
		
		$form_inputs = '.form-table input, .form-table textarea, .form-table select';
		$this->kajax->removeClass( $form_inputs, 'error' );
		$this->kajax->resetAnim( $form_inputs );
		
		if ( $this->form_validation->run() != FALSE )
		{
			if ( $this->Users_model->exists( $id )  )
			{
				$this->Users_model->update_from_post( $post, $allow_change_role );
				
				if ( $is_profile )
				{
					$this->kajax->fancy_log_success( lang_line_ucwords( 'profile_saved') );
				}
				else
				{
					$this->kajax->fancy_log_success( lang_line_ucwords('user') . " '" . $post['username'] . "' " . lang_line('saved') . '.' );
				}
				
				$this->kajax->reload_target();
			}
			else
			{
				$this->kajax->fancy_log_error( lang_line_ucwords('user') . " '" . $post['username'] . "' " . lang_line('doesnt_exists') . '.' );
			}
		}
		else
		{
			$this->kajax->fancy_log_error( validation_errors() );
		}
		
		$this->kajax_validate_inputs( $rules );
		
		$this->kajax->out();
	}
	
	public function profile_update()
	{
		$this->session_restrict( CIBLOG_SUSCRIBER_LEVEL );
		
		$post = $this->input->post();
		$id = isset( $post['id'] ) ? intval( $post['id'] ) : 0;
		
		if ( $this->user->user_id == $id )
		{
			$this->user_update_data( FALSE, TRUE );
		}
	}
	
	public function profile()
	{
		$this->session_restrict( CIBLOG_SUSCRIBER_LEVEL );
		
		$this->load->model('Users_model');
		
		$data = $this->Users_model->by_id( $this->user->user_id, ARRAY_A);
		$data['user'] = $data;
		$data['profile'] = TRUE;
		
		$this->add_frame_view('admin/user_form', $data );
	}
	
	public function login()
	{
		$post = $this->input->post();
		
		if ( !empty( $post ) && isset( $post['user'] ) )
		{
			$user = $this->Users_model->get_user( $post['user'], $post['pass'] );
			
			if( isset( $user ) )
			{
				$this->session_create( $post['user'], $post['pass'], isset( $post['remember_me'] ) );
				
				$this->Users_model->update_last_login( $user->user_id );
				
				if ( $user->user_level > CIBLOG_SUSCRIBER_LEVEL )
				{
					$this->kajax->redirect(base_url('/admin'));
				}
				else
				{
					$this->kajax->redirect(base_url('/user/profile'));
				}
			}
			else
			{
				$this->kajax->fadeIn('.form-error',500);
				$this->kajax->html( '.form-error', '<p>' . $this->lang->line('user_pass_incorrect') . '</p>' );
			}
			
			$this->kajax->out();
		}
		else
		{
			if ( $this->user_is_logged() )
			{
				if ( $this->user->user_level > CIBLOG_SUSCRIBER_LEVEL )
				{
					if ( $this->is_kajax_request() )
					{
						$this->kajax->redirect(base_url('/admin'));
						$this->kajax->out();
					}
					else
					{
						redirect('/admin');
					}
				}
				else
				{
					if ( $this->is_kajax_request() )
					{
						$this->kajax->redirect(base_url('/user/profile'));
						$this->kajax->out();
					}
					else
					{
						redirect(base_url('/user/profile'));
					}
				}
			}
			else
			{
				$this->add_frame_view('admin/login', NULL, FALSE);
			}
		}
	}
}
