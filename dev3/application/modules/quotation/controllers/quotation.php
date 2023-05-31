<?php
	class Quotation extends Controller
	{
		public $canSeeMargins;
		public $canSeeOMR;
		public $isAdmin;

		public function __construct()
		{
			parent::__construct();
			$this->load->model('quotation_model');
			$this->load->model('site/site_model');
			$this->load->library('session');
            $twoSee = canSeeMarginsAndOMR();
            $this->canSeeMargins = $twoSee['seemargins'];
            $this->canSeeOMR = $twoSee['seeomr'];

			$loggedInUserDetails = $this->users_model->getUserDetails($this->session->userdata('userid'));
			$this->isAdmin = $loggedInUserDetails['administrator'];

			if ($this->site_model->is_logged_in() == false)
				redirect('/');

			setcookie($this->config->item('site_name').'_'.$this->session->userdata('userid').'_last_visited', current_url(), time() + (86400 * 365), "/");
		}

		public function index()
		{
			//template name
			$data['main_content'] = 'quotations';
			$this->load->view('quotation/front_template', $data);
		}

		public function getAllQuotations()
		{
			$repWhere = "";

			$selectedUser = $this->session->userdata('selectedUser');

			if (count($selectedUser) > 0)
			{
				$userDetail = $this->site_model->getUserDetails($selectedUser['userid']);
				$repWhere = $userDetail['repwhere'];
			}

			$this->quotation_model->getAllQuotations($repWhere);
		}

		public function exportQuotations()
		{
			$repWhere = "";

			$selectedUser = $this->session->userdata('selectedUser');

			if (count($selectedUser) > 0)
			{
				$userDetail = $this->site_model->getUserDetails($selectedUser['userid']);
				$repWhere = $userDetail['repwhere'];
			}

			$this->quotation_model->getAllQuotationsForExport($repWhere);
		}

		public function detail($orderNumber)
		{
			$data['products'] = array();

			if (empty($orderNumber) || !is_numeric($orderNumber))
			{
				$data['sales_order_number'] = null;
			}
			else
			{
				$orderNumber = intval($orderNumber);

				$data['sales_order_number'] = $orderNumber;
				$products = $this->quotation_model->quotationDetailQuery(array('orderNumber' => $orderNumber));

				foreach ($products as $product)
				{
					$product['description'] = "<a href='".base_url()."products/details/".base64_encode($product['prodcode'])."'>".$product['fulldesc']."</a>";
					$data['products'][] = $product;
				}
			}

			//template name
			$data['main_content'] = 'detail';
			$this->load->view('quotation/front_template', $data);
		}

		public function exportQuotationDetails()
		{
			$this->quotation_model->getQuotationDetailsForExport();
		}
	}
