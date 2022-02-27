<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    protected Environment $twig;
    protected CommentRepository $commentRepository;

    public function __construct(Environment $twig, CommentRepository $commentRepository)
    {
        $this->twig = $twig;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return new Response($this->twig->render('conference/index.html.twig'));
     }

     #[Route('/conference/{id}', name: 'conference')]
    public function show(Request $request, Conference $conference): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $this->commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]));
    }
}
