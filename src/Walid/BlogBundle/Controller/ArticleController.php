<?php

namespace Walid\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Walid\BlogBundle\Entity\Article;
use Walid\BlogBundle\Entity\Image;
use Walid\BlogBundle\Form\ArticleType;

class ArticleController extends Controller
{
  public function indexAction($page, Request $request)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    $categoryId = $request->query->get('category');

    // 5 articles per page.
    $nbPerPage = 5;

    // list of articles
    $listArticles = $this->getDoctrine()
    ->getManager()
    ->getRepository('WalidBlogBundle:Article')
    ->getArticles($page, $nbPerPage, $categoryId)
    ;

    $nbPages = ceil(count($listArticles)/$nbPerPage);
    // if page doesn't exist, return 404 not found
    if ($page > $nbPages && $page != 1) {
      throw $this->createNotFoundException("Page ".$page." doesn't exist.");
    }

    // call the view
    return $this->render('WalidBlogBundle:Article:index.html.twig', array(
      'listArticles' => $listArticles,
      'nbPages'     => $nbPages,
      'page'        => $page
      ));
  }

  public function viewAction($id, Request $request){
    // Get article with id = $id
    $article = $this->getDoctrine()
    ->getManager()
    ->find('WalidBlogBundle:Article',$id)
    ;

    // if Article.find($id) doesn't exist, return exception
    if (null === $article) {
      throw new NotFoundHttpException("No article with id ".$id);
    }

    // call the view to show $article
    return $this->render('WalidBlogBundle:Article:view.html.twig', array(
      'article' => $article
      ));
  }

  public function addAction(Request $request) {
    $article = new Article();

    // Create form for $article
    $form = $this->get('form.factory')->create(new ArticleType(), $article);


    // Check that the value are correct
    if ($form->handleRequest($request)->isValid()) {

      $em = $this->getDoctrine()->getManager();

      // $article is ready to be save in DataBase
      $em->persist($article);

      // Save $article in DB
      $em->flush();

      // Add flash notification
      $request->getSession()->getFlashBag()->add('notice', 'Article has been created.');

      // Show the new article
      return $this->redirect($this->generateUrl('walid_blog_view', array('id' => $article->getId())));
    }

    // If the request if a Get request or form contains invalid values, we show the form
    return $this->render('WalidBlogBundle:Article:add.html.twig', array(
      'form' => $form->createView(),
      ));

  }



  public function editAction($id, Request $request)
  {
    // Get article with id $id
    $article = $this->getDoctrine()
    ->getManager()
    ->find('WalidBlogBundle:Article',$id)
    ;;

    // Create form with default value $article
    $form = $this->get('form.factory')->create(new ArticleType(), $article);

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($article);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Article has been saved.');

      return $this->redirect($this->generateUrl('walid_blog_view', array('id' => $article->getId())));
    }

    // We can use the same page for edit and create article
    return $this->render('WalidBlogBundle:Article:add.html.twig', array(
      'form' => $form->createView(),
      ));

  }


  public function deleteAction($id, Request $request) {
    // Get article with id $id
    $article = $this->getDoctrine()
    ->getManager()
    ->find('WalidBlogBundle:Article',$id)
    ;

    // Delete the article
    $em = $this->getDoctrine()->getManager();
    $em->remove($article);

    $em->flush();

    $request->getSession()->getFlashBag()->add('notice', 'Article deleted.');

    $url = $this->get('router')->generate('walid_blog_home');
    return new RedirectResponse($url);
  }

}

