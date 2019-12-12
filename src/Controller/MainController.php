<?php


namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\ImageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function IndexAction()
    {
        $user = $this->getUser();

        $rCat = $this->getDoctrine()->getRepository(Category::class);
        $categories = $rCat->findAll();

        $ra = $this->getDoctrine()->getRepository(Articles::class);
        $articles = $ra->findBy([], ['createdAt' => 'desc'], 5);
        $lastArticle = $ra->find(1);

        return $this->render('index.html.twig',
            [
                'categories' => $categories,
                'lastArticle' => $lastArticle,
                'articles' => $articles,
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="post")
     */
    public function PostAction(Request $request,$id)
    {
        //$id = 1; //id en dur le temps du travail
        $user = $this->getUser();

        $rCat = $this->getDoctrine()->getRepository(Category::class);
        $categories = $rCat->findAll();

        $rp = $this->getDoctrine()->getRepository(Articles::class);
        $article = $rp->find($id);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid())
        {
            $comment->setUser($user);
            $comment->setArticle($article);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('post');
        }

        $rCom = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $rCom->findBy(['article' => $article]);

        return $this->render('post.html.twig',
            [
                'article' => $article,
                'categories' => $categories,
                'comments' => $comments,
                'form' => $commentForm->createView(),
                'user' => $user
            ]
        );
    }

    /**
     * @Route("/categories/{id}", name ="category")
     */
    public function CategoryAction($id)
    {
        $user = $this->getUser();

        $rCat = $this->getDoctrine()->getRepository(Category::class);
        $categories = $rCat->findAll();

        $currentCategory = $rCat->find($id);

        $ra = $this->getDoctrine()->getRepository(Articles::class);
        $articles = $ra->findBy(['category' => $id], ['createdAt' => 'desc'], 5);

        return $this->render('category.html.twig',
            [
                'currentCategory' =>$currentCategory,
                'categories' => $categories,
                'articles' => $articles,
                'user' => $user
            ]
        );
    }
}