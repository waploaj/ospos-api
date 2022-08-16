<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * @property  employee
 */
class SupplierApi extends CI_Controller
{

    private $staff_token;
    private $employee;
    private $item;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Supplier','Employee');

        $this->load->helper(array('cookie', 'date', 'form', 'email'));
       // $this->load->library(array('encrypt', 'form_validation'));

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


    public function get_supplier_details()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {
                $supplier_id = $this->input->post('supplier_id');

                if ($supplier_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $supplier = $this->Supplier->get_info($supplier_id);

                    if (count($supplier) < 1) {
                        $returnArr['response'] = 'No supplier found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $supplier;
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


    public function get_suppliers()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {
                $supplier = $this->Supplier->get_all();

                if (count($supplier) < 1) {
                    $returnArr['response'] = 'No supplier found';
                } else {
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $supplier->result();
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;
    }

}
