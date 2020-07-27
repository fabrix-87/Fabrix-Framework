<?php

namespace System;

use System\Core\Registry;
use System\Services\AuthService;
use App\User;
use App\Mappers\user_Mapper;

/**
 *
 * Class SystemUser
 * @version 2.0a
 * @autor Menza Fabrizio
 *
 */
class SystemUser
{
    private $logged = false;
    private $userMapper;
    private $registry;
    private $userData;
    private $auth;

    public function __construct(Registry $registry){
        $this->registry = $registry;
        $this->userData = new User();
        
        $this->userMapper = new user_Mapper($registry->get('db'), $this->userData);

        $this->auth = new AuthService($this->userData, $this->userMapper);

        $this->logged = $this->auth->init();
    }

    public function logout()
    {
        $this->auth->logout();
    }

    public function login($username, $password)
    {
        return $this->auth->login($username, $password);
    }

    public function __get($key)
    {
        return $this->userData->$key ?? null;
    }

    //------------------------------		GET DATA		------------------------------//
    public function getUserLevel()
    {
        return $this->getValue('level');
    }

    public function user_id()
    {
        return $this->getValue('user_id');
    }

    public function getValue($key)
    {
        return $this->userData->$key;
    }

    /**
     * @name getUserData
     * Ritorna i dati dell'utente
     * @return array|false
     * */
    public function getUserData() {
        if (!$this->logged)
            return false;
        else
            return $this->userData->getAll();
    }

    // public function getDataFormat() {
    //     if (!empty($this->user_settings['format']))
    //         return $this->user_settings['format'];
    //     else
    //         return 'd/m/Y';
    // }
    //
    // public function getJsDataFormat() {
    //     if (!empty($this->user_settings['js_format']))
    //         return $this->user_settings['js_format'];
    //     else
    //         return 'dd/mm/yy';
    // }



    // public function getSettings() {
    //     return $this->user_settings;
    // }




    //------------------------------		EDIT DATA		------------------------------//

    /**
     * @name editUserData
     * Modifica i dati dell'utente
     * @param array()
     * */
    public function editUserData($user_id, $data = array()) {
        $this->db->updateRecords('users', $data, 'user_id=' . $user_id);
    }

    public function editUserLanguages($user_id, $user_languages_id, $data = array()) {
        $this->db->updateRecords('user_languages', $data, 'user_id=' . $user_id . ' and user_languages_id=' . $user_languages_id);
    }

    public function editSettings($user_id, $data = array()) {
        $this->db->updateRecords('user_settings', $data, 'user_id=' . $user_id);
    }



    //------------------------------	  DELETE DATA		------------------------------//

    public function deleteUser($user_id) {
        //todo
        //$this->db->deleteRecords('user_languages','user_id='.$this->user_id);
    }




    //------------------------------		UPDATE DATA		------------------------------//
    public function user_login($mail = null, $username = null, $password) {
        if ($mail != null) {
            $mail = filter_var($mail, FILTER_VALIDATE_EMAIL);
            if (!$mail)
                return false;
            $sql = "SELECT user_id, admin FROM users WHERE email='" . $mail . "' and password='" . $password . "'";
        }
        elseif ($username != null) {
            $username = filter_var($username, FILTER_SANITIZE_STRING);
            $sql = "SELECT user_id, admin FROM users WHERE username='" . $username . "' and password='" . $password . "'";
        }
        else
            return false;

        $this->db->executeQuery($sql);
        if ($this->db->numRows() != 1)
            return false;

        $this->user_data = $this->db->getRows();

        if($this->user_data['admin'] != 1)
            $this->is_admin = false;
        else{
            $this->is_admin = true;
        }

        $_SESSION['user_id'] = $this->user_data['user_id'];
        $_SESSION['password'] = $password;

        return true;
    }



    //------------------------------	  CHECK DATA		------------------------------//

    /**
     * Controlla se un utente si è già iscritto con una email
     */
    public function checkMail($mail) {
        $query = "SELECT user_id FROM users WHERE email='" . $mail . "'";
        $this->db->executeQuery($query);

        if ($this->db->numRows() == 1) {   // è già presente
            //$_SESSION['message'] = array("type" => "error", "mex" => "email_already_taken");
            return true;
        }

        return false;
    }

    public function checkUsername($username) {
        $query = "SELECT user_id FROM users WHERE username='" . $username . "'";
        $this->db->executeQuery($query);

        if ($this->db->numRows() == 1) {
            //$_SESSION['message'] = array("type" => "error", "mex" => "username_already_taken");
            return true;
        }
        return false;
    }

    public function isLogged() {
        return $this->logged;
    }

    public function is_admin(){
        return $this->is_admin;
    }

}
