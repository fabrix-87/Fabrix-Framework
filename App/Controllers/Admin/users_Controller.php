<?php

namespace App\Controllers\Admin;

use System\Core\Controller;

class users_Controller extends Controller{

    protected $requireLogin = true;

    public function index(){
        $this->view->show('admin/users');
    }

    public function show(int $id){
        var_dump($id);
        die();
    }

    public function deleteUser(){
        $this->requireLogin();
        if(!$this->ajax)
        return false;

        echo ($this->model->deleteUser($this->registry->postData('user_id'))) ? 'true' : 'false';

        return true;
    }

    public function checkMail(){
        $this->requireLogin();
        if(!$this->ajax)
        return false;

        echo ($this->user->checkMail($this->registry->getData('email'))) ? http_response_code(418) : http_response_code(200); //200 non valida (già presente)

        return true;
    }

    public function userList(){
        if(!$this->ajax)
        return false;

        echo json_encode($this->model->getUsersList());
        return true;
    }

    public function checkUsername(){
        $this->require_login();
        if(!$this->ajax)
        return false;

        echo ($this->user->checkUsername($this->registry->getData('username'))) ? http_response_code(418) : http_response_code(200); //200 non valida (già presente)

        return true;
    }

    public function addUser(){
        $this->require_login();
        if(!$this->ajax)
        return false;


        echo ($this->model->addUser()) ? 'true' : 'false';

        return true;
    }


    public function userDetails(){
        $this->require_login();

        if(!$this->ajax)
        return false;

        $data['user_details'] = $this->model->getUserInfo($this->registry->getData('user_id'));

        $this->view->show('user_details', $data);

        return true;
    }


    public  function sendActivationMail(){
        $this->require_login();

        $user_id = $this->registry->getData('id');
        if($this->model->sendActivationMail($user_id))
        $_SESSION['message'] = array('type' => 'info', 'mex' => 'e-Mail inviata');
        else
        $_SESSION['message'] = array('type' => 'error', 'mex' => 'Utente non trovato o già attivo');
        header('location:'.$this->view->generateUrl('users','details',array('id' => $user_id)));
        return true;
    }
}
