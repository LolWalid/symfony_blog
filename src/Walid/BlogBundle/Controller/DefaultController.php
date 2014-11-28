<?php

namespace Walid\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WalidBlogBundle:Default:index.html.twig');
    }
}
