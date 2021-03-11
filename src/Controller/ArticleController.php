<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    
    /**
     * @var ArticleRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    public function __construct(ArticleRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }
    /**
     * @Route("/articles", name="articles")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
    	//creation d'un Repository
    	//$repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $paginator->paginate(
            $this->repository->findAll(),
            $request->query->getInt('page', 1),
             4);  

        return $this->render('article/index.html.twig', [
            'articles' => $articles 
        ]);
    }

    /**
     * @Route("/article/new", name="article.create")
     * @Route("/article/{id}/edit", name="article.edit")
     */

    public function form(Article $article = null , Request $request , EntityManagerInterface $manager)
    {
    	if(!$article){

    		$article = new Article();
    	}

    	$form = $this ->createForm(ArticleType::class , $article );

    	$form->handleRequest($request);

    	 if ($form->isSubmitted() && $form->isValid())
    	 {
            $file = $form->get('image')->getData();
            $this->uploadFile($file, $article);
    	 	if (!$article->getId()){

                 $article->setCreatedAt(new \DateTime());
                 $manager->persist($article);
             }
             
    	 	$manager->flush();

    	 	return $this->redirectToRoute('article.show',['id'=> $article->getId()]);
    	 } 			   

    	return $this->render('article/create.html.twig',[

    		'formArticle' => $form->createView(),
    		'editMode' => $article->getId() !==null

    	]);
    }

    /**
     * @Route("/article/{id}", name="article.show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager)
    {
    	// $repo = $this->getDoctrine()->getRepository(Article::class);

    	// //$article = $repo->find($id);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('article.show',['id'=> $article->getId()]);
        }

    	return $this->render('article/show.html.twig', [
    		'article'=> $article,
            'commentForm'=> $form->createView()
    	]);
    }

    /**
     * @param File $file
     * @param object $object
     * 
     * @return object
     */
    public function uploadFile(File $file, object $object){
         $filename = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
         $file->move($this->getParameter('uploads'), $filename);
         $object->setImage($filename);

        return $object;
     }
}
