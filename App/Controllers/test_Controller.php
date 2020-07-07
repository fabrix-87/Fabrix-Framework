<?php

namespace App\Controllers;

use App\Mappers\user_Mapper;
use App\Post;
use App\User;
use System\Core\Controller;
use System\Data\DataMapper;

/**
 *
 */
class test_Controller extends Controller
{

    public function index()
    {
        $usr = new User();
        $uMap = new DataMapper($this->registry->get('db'), $usr);

        $filter = [
            ['banned','=',0],
            ['firstname','like','f%']
        ];
        echo json_encode($uMap->where($filter)->findAll(10,0));
        die();
        
        /*
        $usr->fillAll([
            'title' => 'Titolo di prova - modifica',
            'description' => 'Descrizione di prova',
            'body' => 'Testo di provola',
            'author_id' => 1
        ]);
        
        /*
        var_dump($usr->validate());
        var_dump($usr->getErrors());
        */
        //$uMap->update();

        //var_dump($usr);
        
        die('ciccio prova');
        return true;
    }

    public function prova()
    {
        return ['ciccio prova' => 'asd'];
    }
}



 ?>
