<?php


namespace App\Controller;


use App\Entity\Articles;
use App\Entity\Category;
use App\Form\ArticlesType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/editor", name="editor")
     */
    public function EditorAction(Request $request)
    {
        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAll();

        $article = new Articles();
        $articleForm = $this->createForm(ArticlesType::class, $article);

        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {

            $cover = $article->getCover();
            $coverName = md5(uniqid()).'.'.$cover->guessExtension();

            try {
                $cover->move(
                    $this->getParameter('upload_cover'),
                    $coverName
                );
            } catch (FileException $e) {
                throw new \Exception($e->getMessage());
            }
            $article->setCover($coverName);
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
                'user' => $user
            ]
        );
    }
}
