<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

defined('BASEPATH') OR exit('No direct script access allowed');

//require(APPPATH.'../libraries/REST_Controller.php');

/**
 * @property  employee
 */
class EmployeeApi extends CI_Controller
{

    private $staff_token;
    private $employee;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Employee');

        $this->load->helper(array('cookie', 'date', 'form', 'email'));
//        $this->load->library(array('encryption', 'form_validation'));

        /* Authentication Begin **/
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        $current_method = $this->router->fetch_method();

        $public_functions = array('login',);
        if (array_key_exists("X-Token", $headers)) {
            $this->staff_token = $headers['X-Token'];
            try {
                if (isset($this->staff_token)) {
                    $employee = $this->Employee->get_logged_in_employee_info();
                    if ($employee == false && $employee->num_rows() <= 0) {
                        echo json_encode(array("is_logged_out" => "Yes"));
                        die;
                    } else {
                        $this->employee = $employee;
                    }
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
                die;
            }
        } else if (!in_array($current_method, $public_functions)) {
            show_404();
        }

    }
    
      public function create_attendence()
    {

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if (!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            } else {

                $attendence_data = array(
                    'customer_id' => $this->input->post('customer_id'),
                    'employee_id' => $this->input->post('employee_id'),
                    'checkin_time' => $this->input->post('checkin_time'),
                    'checkout_time' => $this->input->post('checkout_time')

                );

                if (!isset($attendence_data) ) {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {

                    $employee = $this->Employee->save_attendence($attendence_data);

                    if (!$employee) {
                        $returnArr['response'] = 'No data found';
                    } else {
                        $returnArr['status'] = '1';
                        $returnArr['response'] =  $employee;
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



    public function login(){

        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()) {
                $returnArr['response'] = "Only POST method is allowed";
            }else{
                $username = $this->input->post('username');
                $password = $this->input->post('password');

                if ($username == '' || $password == '') {
                    $returnArr['response'] = "Some Parameters are missing";
                } else {
                    $login = $this->Employee->login($username, $password);
                    $employee = $this->Employee->get_logged_in_employee_info();

                    if ($login == false || count($employee) < 1) {
                        $returnArr['response'] = 'Please check the email and password and try again';
                    } else {

                        $token = base64_encode(now());
                        $this->Employee->update_login_token(
                            array('person_id' => (string) $employee->person_id),
                            array('token'=> $token, 'last_updated'=> date_format(date_create(), 'Y-m-d H:i:s'))
                        );

                        $returnArr['status'] = '1';
                        $returnArr['response'] = 'You are Logged In successfully';
                        $returnArr['employee'] = $this->Employee->get_logged_in_employee_info();
                        $returnArr['permissions'] = $this->Employee->get_employee_grants((string) $employee->person_id);
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
