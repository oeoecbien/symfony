<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteController extends AbstractController
{
    #[Route('/notes', name: 'app_note_index', methods: ['GET'])]
    public function index(NoteRepository $notes): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $notes->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/notes/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $note = new Note();
        $form = $this->createFormBuilder($note)
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['maxlength' => 255],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('app_note_index');
        }

        return $this->render('note/new.html.twig', [
            'form' => $form,
        ]);
    }
}
