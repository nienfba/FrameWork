<?php

namespace App\Controller;


use App\Entity\User;
use Nienfba\Framework\Controller;

class FrontController extends Controller {

    /** 
     * @route /
     */
    public function home() {
        $content = 'Je suis la première page';

        $this->render('front/home.phtml', ['content' => $content]);
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
