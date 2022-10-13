<?php

namespace App\Controller;


use App\Entity\User;
use App\Model\UserModel;
use Nienfba\Framework\Controller;

class FrontController extends Controller {

    /** 
     * @route /
     */
    public function home() {
        $userModel = new UserModel();
        
    }

    /** Je retourn du JSON 
     * @route /front/test
     */
    public function test()
    {

        $user = new User();
        $user->setFirstname('Jean')->setLastname('PeuPlus')->setEmail('jeanpeuplus@email.fr');

        $this->renderJson(["user"  => $user]);
    }

}
