<?php

namespace App\Controller;

use App\Entity\Annee;
use App\Form\AnneeType;
use App\Repository\AnneeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AnneeController extends AbstractController
{
    #[Route('/annees', name: 'annee_index', methods: ['GET'])]
    public function index(AnneeRepository $anneeRepository): Response
    {
        $annees = $anneeRepository->findAll();
        return $this->json($annees, Response::HTTP_OK);
    }

    #[Route('/annees', name: 'annee_new', methods: ['POST'])]
    public function new(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();
        $annee = $serializer->deserialize($json, Annee::class, 'json');
        
        $errors = $validator->validate($annee);
        if (count($errors) === 0) {
            $entityManager->persist($annee);
            $entityManager->flush();
            return $this->json($annee, Response::HTTP_CREATED);
        }

        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }



    #[Route('/annees/{id}', name: 'annee_show', methods: ['GET'])]
    public function show(Annee $annee): Response
    {
        return $this->json($annee, Response::HTTP_OK);
    }

    /* #[Route('/annees/{id}', name: 'annee_edit', methods: ['PUT'])]
    public function edit(Request $request, Annee $annee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnneeType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->json($annee, Response::HTTP_OK);
        }

        return $this->json($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    } */

    #[Route('/annees/{id}', name: 'annee_edit', methods: ['PUT'])]
    public function edit(Request $request, Annee $annee, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();
        $updatedAnnee = $serializer->deserialize($json, Annee::class, 'json');
        
        $form = $this->createForm(AnneeType::class, $annee, ['csrf_protection' => false]);
        $form->submit($updatedAnnee->toArray());

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->json($annee, Response::HTTP_OK);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        
    }
    
    #[Route('/annees/{id}', name: 'annee_delete', methods: ['DELETE'])]
    public function delete(Request $request, Annee $annee, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($annee);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}


/*
namespace App\Controller;

use App\Entity\Annee;
use App\Form\AnneeType;
use App\Repository\AnneeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/annee')]
class AnneeController extends AbstractController
{
    #[Route('/', name: 'app_annee_index', methods: ['GET'])]
    public function index(AnneeRepository $anneeRepository): Response
    {
         return $this->render('annee/index.html.twig', [
            'annees' => $anneeRepository->findAll(),
        ]); 
        $annees = $anneeRepository->findAll();
        return $this->handleView($this->view($annees, Response::HTTP_OK));
    }

    #[Route('/new', name: 'app_annee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annee = new Annee();
        $form = $this->createForm(AnneeType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annee);
            $entityManager->flush();

            return $this->redirectToRoute('app_annee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annee/new.html.twig', [
            'annee' => $annee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annee_show', methods: ['GET'])]
    public function show(Annee $annee): Response
    {
        return $this->render('annee/show.html.twig', [
            'annee' => $annee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annee $annee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnneeType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annee/edit.html.twig', [
            'annee' => $annee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annee_delete', methods: ['POST'])]
    public function delete(Request $request, Annee $annee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annee->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annee_index', [], Response::HTTP_SEE_OTHER);
    }
}
*/