<?php


class Attendance
{
    private $staff_token;
    private $employee;
    private $item;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receiving','Employee','Customer');

        $this->load->helper(array('cookie', 'date', 'form', 'email'));
//        $this->load->library(array('encryption', 'form_validation'));

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

    public function checkin()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';
        try {
            if(!$this->input->post()){
                $returnArr['response'] = "Only Post Method allowed";
            }else{
                $attendance_data = array(
                    'customer_id' => $this->input->post('customer_id'),
                    'employee_id' => $this->input->post('employee_id'),
                    'checkin_time' => $this->input->post('checkin_time'),
                    'checkout_time' => $this->input->post('checkout_time'),
                    'latitude' => $this->input->post('latitude'),
                    'longitude' =>$this->input->post('longitude')
                );
                if(!isset($attendance_data)){
                    $returnArr['response'] = "Some parameter is missing";
                }else{
                    $attendacce = $this->Customer->save_attendance($attendance_data);
                    if(!$attendacce){
                        $returnArr['response'] = "No attendance!";
                    }else{
                        $returnArr['status'] = '1';
                        $returnArr['response'] = $attendacce;
                    }
                }
            }
        }catch (Exception $ex){
            $returnArr['response'] = "Error in connection";
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;

    }

    public function ads()
    {
        $returnArr['status'] = '0';
        $returnArr['response'] = '';

        try {
            if(!$this->input->post()){
                $returnArr['response'] = "Only Post Method is allowed";
            }else{
                $ads_data = array(
                    'pic_file' =>$this->input->post('pic_file'),
                    'latitude' =>$this->input->post('latitude'),
                    'longitude' =>$this->input->post('longitude')
                );
                $ad = $this->Customer->save_ads($ads_data);
                if(!$ad){
                    $returnArr['response'] = "no ads updated";
                }else{
                    $returnArr['status'] = '1';
                    $returnArr['response'] = $ad;
                }
            }
        }catch (Exception $ex){
            $returnArr['response'] = " Erro in connection";
            $returnArr['error'] = $ex->getMessage();
        }
        $response = json_encode($returnArr, JSON_PRETTY_PRINT);
        echo $response;
    }



}