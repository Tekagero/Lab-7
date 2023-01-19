<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\News;
use App\Services\PageFormer;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;


class MainController extends AbstractController
{
     /**
     * @Route("/archive/{page}", name="archive_page", methods={"GET", "POST"})
     */
    public function archive(int $page, ManagerRegistry $doctrine): Response
    {
        $session = new Session();
        $session->start();

        $name = $session->get('name') ?? null;

        $news = $doctrine->getRepository(News::class)->getPageNews($page);

        $pages = PageFormer::formPagination($page, $doctrine);

        return $this->render('main/archive.html.twig', [
            'controller_name' => 'MainController',
            'news' => $news,
            'user_name' => $name,
            'actual_page' => $page,
            'pages' => $pages
        ]);
    }
     /**
     * @Route("/", name="app_main", methods={"GET", "POST"})
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $session = new Session();
        $session->start();

        $name = $session->get('name') ?? null;

        $news = $doctrine->getRepository(News::class)->getLastNews();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'news' => $news,
            'user_name' => $name
        ]);
    }
     /**
     * @Route("/news/{id}", name="app_new", methods={"GET", "POST"})
     */
    #[Route('/news/{id}', name: 'app_news')]
    public function news(News $news, ManagerRegistry $doctrine): Response
    {
        $session = new Session();
        $session->start();

        $name = $session->get('name') ?? null;

        $news->setViews($news->getViews() + 1);

        $comments = $doctrine->getRepository(Comment::class)->getCommentsById($news->getId());

        $authorized = false;

        $entityManager = $doctrine->getManager();
        $entityManager->persist($news);
        $entityManager->flush();


        return $this->render('main/news.html.twig', [
            'controller_name' => 'MainController',
            'news' => $news,
            'comments' => $comments,
            'authorized' => $authorized,
            'user_name' => $name
        ]);
    }
}
