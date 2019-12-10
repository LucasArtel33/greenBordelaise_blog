<?php


namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
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

        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAll();


        return $this->render('index.html.twig',
            [
                'categories' => $categories
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
        }

        return $this->render('editor.html.twig',
            [
                'categories' => $categories,
                'form' => $articleForm->createView(),
            ]
        );
    }

    /**
     * @Route("/post", name="post")
     */
    public function PostAction(Request $request)
    {
        $id = 1; //id en dur le temps du travail
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
}