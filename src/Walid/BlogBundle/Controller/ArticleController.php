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

    // Ici je fixe le nombre d'annonces par page à 3
    // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
    $nbPerPage = 5;
    // On récupère notre objet Paginator
    $listArticles = $this->getDoctrine()
      ->getManager()
      ->getRepository('WalidBlogBundle:Article')
      ->getArticles($page, $nbPerPage, $categoryId)
    ;
    // On calcule le nombre total de pages grâce au count($listArticles) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listArticles)/$nbPerPage);
    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages && $page != 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }
    // On donne toutes les informations nécessaires à la vue
    return $this->render('WalidBlogBundle:Article:index.html.twig', array(
      'listArticles' => $listArticles,
      'nbPages'     => $nbPages,
      'page'        => $page
    ));
  }

  public function viewAction($id, Request $request){
    /*$tag = $request->query->get('tag');
    return new Response("Affichage de l'annonce d'id : ".$id.", avec le tag : ".$tag);*/

    // On récupère l'annonce avec l'id $id
    $article = $this->getDoctrine()
    ->getManager()
    ->find('WalidBlogBundle:Article',$id)
    ;

    // $Article est donc une instance de OC\PlatformBundle\Entity\Article
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $article) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // Le render ne change pas, on passait avant un tableau, maintenant un objet
    return $this->render('WalidBlogBundle:Article:view.html.twig', array(
      'article' => $article
      ));
  }

  public function addAction(Request $request) {
    $article = new Article();

    // J'ai raccourci cette partie, car c'est plus rapide à écrire !
    $form = $this->get('form.factory')->create(new ArticleType(), $article);

    // On fait le lien Requête <-> Formulaire
    // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur

    // On vérifie que les valeurs entrées sont correctes
    // (Nous verrons la validation des objets en détail dans le prochain chapitre)

    if ($form->handleRequest($request)->isValid()) {
      // On l'enregistre notre objet $article dans la base de données, par exemple
      //$article->getImage()->upload();

      $em = $this->getDoctrine()->getManager();

      //$article->getImage()->preUpload();
      $em->persist($article);

      //return new Response($article->getImage()->getFile()->getExtension()." + ".$article->getImage()->getUrl());

      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // On redirige vers la page de visualisation de l'annonce nouvellement créée
      return $this->redirect($this->generateUrl('walid_blog_view', array('id' => $article->getId())));
    }

    // À ce stade, le formulaire n'est pas valide car :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
    return $this->render('WalidBlogBundle:Article:add.html.twig', array(
      'form' => $form->createView(),
      ));

  }



  public function editAction($id, Request $request)
  {
    $article = $this->getDoctrine()
    ->getManager()
    ->find('WalidBlogBundle:Article',$id)
    ;;

    // J'ai raccourci cette partie, car c'est plus rapide à écrire !
    $form = $this->get('form.factory')->create(new ArticleType(), $article);

    // On fait le lien Requête <-> Formulaire
    // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur

    // On vérifie que les valeurs entrées sont correctes
    // (Nous verrons la validation des objets en détail dans le prochain chapitre)
    if ($form->handleRequest($request)->isValid()) {
      // On l'enregistre notre objet $article dans la base de données, par exemple
      $em = $this->getDoctrine()->getManager();
      $em->persist($article);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // On redirige vers la page de visualisation de l'annonce nouvellement créée
      return $this->redirect($this->generateUrl('walid_blog_view', array('id' => $article->getId())));
    }

    // À ce stade, le formulaire n'est pas valide car :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
    return $this->render('WalidBlogBundle:Article:add.html.twig', array(
      'form' => $form->createView(),
      ));

  }


  public function deleteAction($id, Request $request) {
    // Ici, on récupérera l'annonce correspondant à $id
    $article = $this->getDoctrine()
    ->getManager()
    ->find('WalidBlogBundle:Article',$id)
    ;

    // Ici, on gérera la suppression de l'annonce en question
    $em = $this->getDoctrine()->getManager();
    $em->remove($article);

    $em->flush();

    $request->getSession()->getFlashBag()->add('notice', 'Annonce supprimée.');

    $url = $this->get('router')->generate('walid_blog_home');
    return new RedirectResponse($url);
  }

}

