<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * @property  employee
 */
class ItemsApi extends CI_Controller
{

    private $staff_token;
    private $employee;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee', 'Item', 'Inventory', 'Item_quantity', 'Item_taxes',  'Item_kit');

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
        } else{
            $response['status'] = '0';
            $response['response'] = 'Token not provided';
            echo json_encode($response, JSON_PRETTY_PRINT);
            exit;
        }

    }


    public function get_location_items(){

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            }else{
                $location_id = $this->input->post('location_id');

                if ($location_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $items = $this->Item->get_all($location_id);

                    if (count($items) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $items->result();
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


    public function get_item_detail(){

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            }else{
                $item_id = $this->input->post('item_id');

                if ($item_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $item = $this->Item->get_info($item_id);

                    if (count($item) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $item;
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


    public function get_item_tax_detail(){

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            }else{
                $item_id = $this->input->post('item_id');

                if ($item_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $item = $this->Item_taxes->get_info($item_id);

                    if (count($item) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $item;
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


    public function get_item_kit_detail(){

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            }else{
                $item_kit_id = $this->input->post('item_kit_id');

                if ($item_kit_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $item = $this->Item_kit->get_info($item_kit_id);

                    if (count($item) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $item ;
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


    public function get_item_kit_quantity(){

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            }else{
                $item_kit_id = $this->input->post('item_kit_id');
                $item_id = $this->input->post('item_id');

                if ($item_kit_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $item = $this->Item_kit->get_item_kit_quantity($item_id, $item_kit_id);

                    if (count($item) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $item ;
                    }
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT );
        echo $response;
    }


    public function get_item_quantity(){

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            }else{
                $item_id = $this->input->post('item_id');
                $location_id = $this->input->post('location_id');

                if ($item_id == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $item = $this->Item_quantity->get_item_quantity($item_id, $location_id);

                    if (count($item) < 1) {
                        $returnArr['response'] = 'No items found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $item ;
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


}
