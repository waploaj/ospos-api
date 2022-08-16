<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * @property  employee
 */
class CustomerApi extends CI_Controller
{

    private $staff_token;
    private $employee;
    private $item;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Customer', 'Item', 'Inventory', 'Item_quantity', 'Item_taxes', 'Item_kit');

        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encryption', 'form_validation'));

        /* Authentication Begin **/
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');

        if (array_key_exists("X-Token", $headers)) {
            $this->staff_token = $headers['X-Token'];
            try {
                if (isset($this->driver_token)) {
                    $employee = $this->Employee->get_logged_in_employee_info();
                    if ($employee == false && count($employee) <= 0) {
                        echo json_encode(array("is_logged_out" => "Yes"));
                        die;
                    } else {
                        $this->employee = $employee->row();
                    }
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
                die;
            }
        } else {
            $response['status'] = '0';
            $response['response'] = 'Token not provided';
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT);
            exit;
        }

    }


    public function get_call_card_customers()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {
                $call_card_id = $this->input->post('card_id');

                if ($call_card_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $customers = $this->Customer->get_customers_by_call_card($call_card_id);

                    if (count($customers) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $customers->result();
                    }
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;
    }


    public function get_call_cards()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {
                $employee_id = $this->input->post('employee_id');

                if ($employee_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $cards = $this->Customer->get_call_cards($employee_id);

                    if (count($cards) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $cards->result();
                    }
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;
    }


    public function create_new_customer()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {

                $person_data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'address_1' => $this->input->post('address_1'),
                    'address_2' => $this->input->post('address_2'),
                    'city' => $this->input->post('city'),
                    'country' => $this->input->post('country'),
                    'phone_number' => $this->input->post('phone_number')
                );

                $customer_data = array(
                    //'ward_id' => $this->input->post('ward_id'),
                    'visit_id' => $this->input->post('visit_id'),
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude'),
                    'channel_id' => $this->input->post('channel_id'),
                    'retail_id' => $this->input->post('retail_id'),
                    'location_id' => $this->input->post('location_id'),
                    'store_bussiness_name' => $this->input->post('store_bussiness_name')
                );

                if (!isset($person_data) && !isset($customer_data)) {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {

                    $customer = $this->Customer->save_customer($person_data, $customer_data);

                    if (!$customer) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] =  $customer;
                    }
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;

    }

    public function get_details()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()){
                $returnArr['response'] = 'Only post method is required';
            }else{
                $customer_id = $this->input->post('customer_id');

                if($customer_id == ''){
                    $returnArr['response'] = 'Some paramater are missing';
                }else{
                    $customer = $this->Customer->get_stats($customer_id);

                    if (count($customer<1)){
                        $returnArr['response'] = 'No Stats about this customer';
                    }else{
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $customer->result();
                    }
                }
            }

        } catch (Exception $ex) {
            //throw $th;
            $returnArr['response'] = 'Error in connection';
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;
        
    }

}
