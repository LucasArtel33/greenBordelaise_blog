<?php


namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Image;
use App\Form\ArticlesType;
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

        if($articleForm->isSubmitted() && $articleForm->isValid())
        {
            dd($article);die;
        }

        return $this->render('editor.html.twig',
            [
                'categories' => $categories,
                'form' => $articleForm->createView(),
            ]
        );
    }
}