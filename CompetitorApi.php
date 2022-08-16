<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * @property  employee
 */
class CompetitorApi extends CI_Controller
{

    private $staff_token;
    private $employee;
    private $item;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee','Customer');

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

    public function get_competitor_qns()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if ($this->input->post()){
                $returnArr['response'] = "Only post methond is allowed";
            }else{
                $competitor = $this->Customer->competitor_details();
                if (count($competitor < 1)){
                    $returnArr['response'] = "No competitor question found!";
                }else{
                    $returnArr['status'] = 1;
                    $returnArr['response'] = $competitor;
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] =  $ex->getMessage();
        }
        $response =json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;
 
    }

    public function competitor_ans()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()){
                $returnArr['response'] = "Only post method is allowed";

            }else{
                $answer = array(
                    'time' => $this->input->post('time'),
                    'customer_id' => $this->input->post('customer_id'),
                    'employee_id' => $this->input->post('employee_id'),
                );

                if(!isset($answer)){
                    $returnArr['response'] = "Some parameter are missing!";

                }else{
                    $save_answer =  $this->Customer->save_answer($answer);

                    if(!$save_answer){
                        $returnArr['response'] = "Data are not saved";
                    }else{
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $save_answer;
                    }
                }
            }
        } catch (Exception $ex) {
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] =  $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;
    }
}
?>
