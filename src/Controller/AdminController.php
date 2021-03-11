<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $repository;
    public function __construct(ArticleRepository $repository, EntityManagerInterface $em)
    {
        $this->repository=$repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.article.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator,Request $request)
    {
        $articles = $paginator->paginate(
            $this->repository->findAll(),
            $request->query->getInt('page', 1),
             10); 
        return $this->render('admin/article/index.html.twig',compact('articles'));
    }

    /**
     * @Route("/admin/product/create", name="admin.article.new")
     * $param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form->get('image')->getData();
            $this->uploadFile($file, $article);

            $this->em->persist($article);
            $this->em->flush();
            $this->addFlash('success', "article cree avec succes");
            return $this->redirectToRoute('admin.article.index');
        }
        return $this->render('admin/article/new.html.twig',[
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article/{id}", name="admin.article.edit", methods="post|get")
     * @param Article $article
     * $param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Article $article, Request $request)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'article modifie avec succes');
            return $this->redirectToRoute('admin.article.index');
        }
        return $this->render('admin/article/edit.html.twig',[
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article/{id}", name="admin.article.delete", methods="DELETE")
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Article $article, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(),$request->get('_token')))
        {
            $this->em->remove($article);
            $this->em->flush();
            $this->addFlash('success', 'article supprime avec succes');
        }
        
        return $this->redirectToRoute('admin.article.index');
    }


    /**
     * @param File $file
     * @param object $object
     * 
     * @return object
     */
    public function uploadFile(File $file, object $object): object
     {
        $filename = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
       $file->move($this->getParameter('uploads'), $filename);
       $object->setImage($filename);

       return $object;
    }
    
}
