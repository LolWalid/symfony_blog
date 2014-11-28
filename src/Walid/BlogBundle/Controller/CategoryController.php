<?php

namespace Walid\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Walid\BlogBundle\Entity\Category;
use Walid\BlogBundle\Form\CategoryType;


class CategoryController extends Controller
{
  public function indexAction()
  {
    $categories = $this->getDoctrine()
    ->getManager()
    ->getRepository('WalidBlogBundle:Category')
    ->getCategories()
    ;
    return $this->render('WalidBlogBundle:Category:index.html.twig', array(
      'categories' => $categories
      ));
  }

  public function addAction(Request $request) {
    $category = new Category();

    $form = $this->get('form.factory')->create(new CategoryType(), $category);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();

      $em->persist($category);

      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Category created.');

      return $this->redirect($this->generateUrl('walid_blog_categories'));
    }

    return $this->render('WalidBlogBundle:Category:add.html.twig', array(
      'form' => $form->createView(),
      ));
  }

   public function deleteAction($id, Request $request) {
    $category = $this->getDoctrine()
    ->getManager()
    ->find('WalidBlogBundle:Category',$id)
    ;

    // Delete the article
    $em = $this->getDoctrine()->getManager();
    $em->remove($category);

    $em->flush();


    $request->getSession()->getFlashBag()->add('notice', 'Category deleted.');

    $url = $this->get('router')->generate('walid_blog_categories');
    return new RedirectResponse($url);
  }
}
