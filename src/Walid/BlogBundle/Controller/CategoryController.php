<?php

namespace Walid\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    // J'ai raccourci cette partie, car c'est plus rapide à écrire !
    $form = $this->get('form.factory')->create(new CategoryType(), $category);

    // On fait le lien Requête <-> Formulaire
    // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur

    // On vérifie que les valeurs entrées sont correctes
    // (Nous verrons la validation des objets en détail dans le prochain chapitre)
    if ($form->handleRequest($request)->isValid()) {
      // On l'enregistre notre objet $article dans la base de données, par exemple
      //$article->getImage()->upload();

      $em = $this->getDoctrine()->getManager();

      //$article->getImage()->preUpload();
      $em->persist($category);

      //return new Response($article->getImage()->getFile()->getExtension()." + ".$article->getImage()->getUrl());

      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // On redirige vers la page de visualisation de l'annonce nouvellement créée
      return $this->redirect($this->generateUrl('walid_blog_categories'));
    }

    // À ce stade, le formulaire n'est pas valide car :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
    return $this->render('WalidBlogBundle:Category:add.html.twig', array(
      'form' => $form->createView(),
      ));

  }

}
