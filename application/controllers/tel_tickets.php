<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* CiRM
*
* @author		Aleksey Ksenzov aka KsenZ
* @copyright	        Copyright (c) 2013, Aleksey Ksenzov
* @license		GNU General Public License version 2(GPLv2)
*/

// ------------------------------------------------------------------------

class Tel_Tickets extends CI_Controller {

    public $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('crud_model');
        $this->load->model('tel_tickets_model');

        if(!$this->ion_auth->logged_in())
        {
            redirect('auth/login');
        }
    }

    public function display($sort_by='id', $sort_order='asc', $offset = 0)
    {
        $limit = 10;
        $this->data['fields'] = array(
            'id' => '№',
            'date' => 'Дата открытия',
            'phone' => 'Телефон',
            'contact' => 'Контактный телефон',
            'address' => 'Адрес',
            'description' => 'Неисправность',
            'open' => 'Открыл',
            'responsible' => 'Ответственный'
        );
        $this->data['sort_by'] = $sort_by;
        $this->data['sort_order'] = $sort_order;
        $this->data['tel_tickets_count'] = $this->tel_tickets_model->get_count();
        $this->load->library('pagination');
        $config = array(
            'base_url' => site_url("tel_tickets/display/$sort_by/$sort_order"),
            'total_rows' => $this->data['tel_tickets_count'],
            'per_page' => $limit,
            'uri_segment' => 5,
            'full_tag_open' => '<div class="pagination"><ul>',
            'full_tag_close' => '</ul></div>',
            'first_link' => '<li>Первая</li>',
            'last_link' => '<li>Последняя</li>',
            'cur_tag_open' => '<li class="active"><a href="#">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'next_link' => '&gt;',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'prev_link' => '&lt;',  
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>'
        );			
        $this->pagination->initialize($config);

        $this->data['pagination'] = $this->pagination->create_links();
        $this->data['tel_tickets'] = $this->tel_tickets_model->get_list($limit, $offset, $sort_by, $sort_order);

        $this->layout->render('tel_tickets/list', $this->data);
    }

    public function display_closed($sort_by='id', $sort_order='asc', $offset = 0)
    {
        $limit = 10;
        $this->data['fields'] = array(
            'id' => '№',
            'date' => 'Дата открытия',
            'cdate' => 'Дата закрытия',
            'phone' => 'Телефон',
            'contact' => 'Контактный телефон',
            'address' => 'Адрес',
            'description' => 'Неисправность',
            'comment' => 'Комментарий',
            'close' => 'Закрыл',
        );
        $this->data['sort_by'] = $sort_by;
        $this->data['sort_order'] = $sort_order;
        $this->data['ctickets_count'] = $this->tel_tickets_model->get_count_closed();
        $this->load->library('pagination');
        $config = array(
            'base_url' => site_url("tel_tickets/display_closed/$sort_by/$sort_order"),
            'total_rows' => $this->data['ctickets_count'],
            'per_page' => $limit,
            'uri_segment' => 5,
            'full_tag_open' => '<div class="pagination"><ul>',
            'full_tag_close' => '</ul></div>',
            'first_link' => '<li>Первая</li>',
            'last_link' => '<li>Последняя</li>',
            'cur_tag_open' => '<li class="active"><a href="#">',
            'cur_tag_close' => '</a></li>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'next_link' => '&gt;',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'prev_link' => '&lt;',
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>'
        );            
        $this->pagination->initialize($config);

        $this->data['pagination'] = $this->pagination->create_links();
        $this->data['tickets'] = $this->tel_tickets_model->get_list_closed($limit, $offset, $sort_by, $sort_order);

        $this->layout->render('tel_tickets/list_closed', $this->data);
    }

    public function edit()
    {
        $id = $this->uri->segment(3, 0);
        $id = (int)$id;

        $this->form_validation->set_rules($this->tel_tickets_model->update_rules);

        if ($this->form_validation->run() == TRUE)
        {
            if ($this->input->post('close_checkbox'))                
            {
                $current_user = $this->ion_auth->user()->row();
                $data = array(
                    'id' => $this->input->post('id'),
                    'date' => $this->input->post('date'),
                    'cdate' => $this->input->post('cdate'),
                    'phone' => $this->input->post('phone'),
                    'contact' => $this->input->post('contact'),
                    'address' => $this->input->post('address'),
                    'description' => $this->input->post('description'),
                    'comment' => $this->input->post('comment'),
                    'open' => $this->input->post('open'),
                    'responsible' => $this->input->post('responsible'),
                    'close' => "$current_user->first_name $current_user->last_name",
                );
                $this->tel_tickets_model->close_ticket($this->input->post('id'), $data);
                redirect('tel_tickets');    
            }
            else
            {
                $ticket = array(
                    'comment' => $this->input->post('comment'),
                );
                $this->tel_tickets_model->update($this->input->post('id'), $ticket);
                redirect('tel_tickets');
            }
        }

        $this->data['tel_tickets'] = $this->tel_tickets_model->get_by_id($id);

        if ($this->data['tel_tickets']  == null)
        {
            $this->data['msg'] = 'Ничего не найденно.';
            $this->layout->render('error', $this->data);
        }
        else
        {
            $this->data['form_action'] = 'tel_tickets/edit/' . $id;			
            $this->layout->render('tel_tickets/edit', $this->data);
        }
    }

    public function view_closed()
    {
        $id = $this->uri->segment(3, 0);
        $id = (int)$id;

        $this->data['tickets'] = $this->tel_tickets_model->get_by_id_closed($id);

        if ($this->data['tickets']  == null)
        {
            $this->data['msg'] = 'Ничего не найденно.';
            $this->layout->render('error', $this->data);
        }
        else
        {
            $this->data['form_action'] = 'tel_tickets/view_closed/' . $id;            
            $this->layout->render('tel_tickets/view_closed', $this->data);
        }
    }

    public function add()
    {
        $this->form_validation->set_rules($this->tel_tickets_model->add_rules);

        if ($this->form_validation->run() == TRUE)
        {
            $current_user = $this->ion_auth->user()->row();
            $tel_ticket = array(
                'date' => $this->input->post('date'),
                'phone' => $this->input->post('phone'),
                'contact' => $this->input->post('contact'),
                'address' => $this->input->post('address'),
                'open' => "$current_user->first_name $current_user->last_name",
                'responsible' => $this->input->post('responsible'),
                'description' => $this->input->post('description'),
            );
            $this->tel_tickets_model->add( $tel_ticket );
            redirect('tel_tickets');
        }
        $this->data['form_action'] = 'tel_tickets/add/';			
        $this->layout->render('tel_tickets/add', $this->data);
    }	
}