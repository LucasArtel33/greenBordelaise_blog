<?php


namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\ArticlesType;
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

        $rCat = $this->getDoctrine()->getRepository(Category::class);
        $categories = $rCat->findAll();

        $ra = $this->getDoctrine()->getRepository(Articles::class);
        $articles = $ra->findBy([], ['createdAt' => 'desc'], 5);

        return $this->render('index.html.twig',
            [
                'categories' => $categories,
                'articles' => $articles
            ]
        );
    }

    /**
     * @Route("/editor", name="editor")
     */
    public function EditorAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAll();

        $article = new Articles();
        $articleForm = $this->createForm(ArticlesType::class, $article);

        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {

            $user = $this->getUser();

            $article->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('editor');
        }

        return $this->render('editor.html.twig',
            [
                'categories' => $categories,
                'form' => $articleForm->createView(),
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
        $rCat = $this->getDoctrine()->getRepository(Category::class);
        $categories = $rCat->findAll();

        $ra = $this->getDoctrine()->getRepository(Articles::class);
        $articles = $ra->findBy(['category' => $id], ['createdAt' => 'desc'], 5);

        return $this->render('category.html.twig',
            [
                'categories' => $categories,
                'articles' => $articles
            ]
        );
    }
}