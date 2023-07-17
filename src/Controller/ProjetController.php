<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Annee;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/projet')]
class ProjetController extends AbstractController
{
    #[Route('/', name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository): Response
    {
        $projets = $projetRepository->findAll();
        return $this->json($projets, Response::HTTP_OK);
    }

    #[Route('/new', name: 'app_projet_new', methods: ['POST'])]
    public function new(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        $json = $request->getContent();
        $projet = $serializer->deserialize($json, Projet::class, 'json');

        $errors = $validator->validate($projet);
        if (count($errors) === 0) {
            $anneeId = $request->request->get('anneeId');
            $annee = $entityManager->getRepository(Annee::class)->find($anneeId);
            $projet->setAnnee($annee);

            $entityManager->persist($projet);
            $entityManager->flush();

            return new JsonResponse($projet, Response::HTTP_CREATED);
        }

        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }



    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        return $this->json($projet, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_projet_edit', methods: ['PUT'])]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();
        $updatedProjet = $this->get('serializer')->deserialize($json, Projet::class, 'json');

        $form = $this->createForm(ProjetType::class, $projet);
        $form->submit(json_decode($json, true));

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->json($projet, Response::HTTP_OK);
        }

        return $this->json(['errors' => $form->getErrors(true)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_projet_delete', methods: ['DELETE'])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($projet);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

/* 
namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/projet')]
class ProjetController extends AbstractController
{
    #[Route('/', name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository): Response
    {
        return $this->render('projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projet/new.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projet_delete', methods: ['POST'])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }
}
 */