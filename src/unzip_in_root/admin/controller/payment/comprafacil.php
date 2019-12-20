<?php 
class ControllerPaymentComprafacil extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('payment/comprafacil');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('comprafacil', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
				
		$this->data['entry_cf_username'] = $this->language->get('entry_cf_username');
		$this->data['entry_cf_password'] = $this->language->get('entry_cf_password');
		$this->data['entry_cf_mode'] = $this->language->get('entry_cf_mode');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/comprafacil', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('payment/comprafacil', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');	

		if (isset($this->request->post['cf_username'])) {
			$this->data['cf_username'] = $this->request->post['cf_username'];
		} else {
			$this->data['cf_username'] = $this->config->get('cf_username');
		}
		
		if (isset($this->request->post['cf_password'])) {
			$this->data['cf_password'] = $this->request->post['cf_password'];
		} else {
			$this->data['cf_password'] = $this->config->get('cf_password');
		}
		
		if (isset($this->request->post['cf_mode'])) {
			$this->data['cf_mode'] = $this->request->post['cf_mode'];
		} else {
			$this->data['cf_mode'] = $this->config->get('cf_mode');
		}
		
		if (isset($this->request->post['comprafacil_status'])) {
			$this->data['comprafacil_status'] = $this->request->post['comprafacil_status'];
		} else {
			$this->data['comprafacil_status'] = $this->config->get('comprafacil_status');
		}
		
		if (isset($this->request->post['comprafacil_sort_order'])) {
			$this->data['comprafacil_sort_order'] = $this->request->post['comprafacil_sort_order'];
		} else {
			$this->data['comprafacil_sort_order'] = $this->config->get('comprafacil_sort_order');
		}

		$this->template = 'payment/comprafacil.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/comprafacil')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>