<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * @property  employee
 */
class SalesApi extends CI_Controller
{

    private $staff_token;
    private $employee;
    private $item;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee','Sale');

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
            echo json_encode($response, JSON_PRETTY_PRINT);
            exit;
        }

    }


    public function get_receiving_details()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {
                $receiving_id = $this->input->post('receiving_id');

                if ($receiving_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $receiving = $this->Receiving->get_info($receiving_id);

                    if (count($receiving) < 1) {
                        $returnArr['response'] = 'No receiving found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $receiving->result();
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


    public function create_sales()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            if(!$this->input->post()){
                $returnArr['response'] = "Only Post method is allowed";
            }else{
                $sales_data = array(
                    'sale_time'			=> date('Y-m-d H:i:s'),
                    'customer_id'		=> $this->input->post('customer_id'), //$this->Customer->exists($this->input->post('customer_id')) ? $this->input->post('customer_id') : NULL,
                    'employee_id'		=> $this->input->post('employee_id'),
                    'comment'			=> $this->input->post('comment'),
                    'sale_status'		=> $this->input->post('sale_status'),
                    'invoice_number'	=> $this->input->post('invoice_number'),
                    'quote_number'		=> $this->input->post('quote_number'),
                    'work_order_number'	=> $this->input->post('work_order_number'),
                    'dinner_table_id'	=> $this->input->post('dinner_table'),
                    'sale_type'			=> $this->input->post('sale_type'),
                    'sale_id'			=> $this->input->post('sale_id'),
                );

                $payments = $this->input->post('payments');
                $sales_items = $this->input->post('sales_items');

                $sales = $this->Sale->save_sales($sales_data, $sales_items, $payments);
                if (!$sales){
                    $returnArr['response'] = 'Object Not saved';
                }else{
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $sales;
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = 'Error in connection';
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr,JSON_PRETTY_PRINT);
        echo $response;

    }

}
