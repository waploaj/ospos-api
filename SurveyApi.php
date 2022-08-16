<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');

class SurveyApi extends CI_Controller
{

    private $staff_token;
    private $employee;
    private $item;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Customer', 'Item', 'Employee');

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

    public function create_survey()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {

                $survey_data = array(
                    'customer_id' => $this->input->post('customer_id'),
                    'like' => $this->input->post('like'),
                    'rate' => $this->input->post('rate'),
                    'sugested' => $this->input->post('sugested'),
                    'use_again' => $this->input->post('use_again'),
                    'item_liked' => $this->input->post('item_liked'),
                    'reason_not' => $this->input->post('reason_not'),
                    'customer_comment' => $this->input->post('customer_comment'),
                    'date' => $this->input->post('date')

                );

                if (!isset($survey_data) ) {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {

                    $customer = $this->Customer->save_survey($survey_data);

                    if (count($customer) < 1) {
                        $returnArr['response'] = 'No survery Answers';
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

}
