<?php

namespace App\Controllers\Admin;

use System\Core\Controller;
use System\Routing\Routes;

class users_Controller extends Controller{

    protected $modelName = 'User';

    public function index(){
        $this->view->show('admin/users');
    }

    public function getAll()
    {
        return ['user_list' => $this->mapper->findAll()];
    }

    public function show(int $id){
        var_dump($id);
        die();
    }

    public function deleteUser(){
        echo ($this->mapper->delete($this->request->post('user_id'))) ? 'true' : 'false';

        return true;
    }

    public function checkMail(){
        echo ($this->user->checkMail($this->request->get('email'))) ? http_response_code(418) : http_response_code(200); //200 non valida (già presente)

        return true;
    }

    public function checkUsername(){
        echo ($this->user->checkUsername($this->request->get('username'))) ? http_response_code(418) : http_response_code(200); //200 non valida (già presente)

        return true;
    }

    public function addUser(){
        echo ($this->model->addUser()) ? 'true' : 'false';

        return true;
    }


    public function userDetails(){

        $data['user_details'] = $this->model->getUserInfo($this->request->get('user_id'));

        $this->view->show('user_details', $data);

        return true;
    }


    public  function sendActivationMail(){
        
        return true;
    }
}
