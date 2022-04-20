<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Form\PaysType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PaysController extends AbstractController
{
    #[Route('/pays', name: 'app_pays', methods: ['GET', 'POST'])]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $pays = new Pays;
        $form = $this->createForm(PaysType::class, $pays);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $drapeau = $form->get('drapeau')->getData();
            // $drapeau->getClientOriginalName() permet de récupérer le nom originel du fichier chargé
            $pays->setDrapeau($drapeau->getClientOriginalName());
            $om = $manager->getManager();
            $om->persist($pays);
            $om->flush();
            $this->addFlash('success', "Nouveau pays ajouté à la liste des pays participants");
            return $this->redirectToRoute('app_pays');
        }

        return $this->renderForm('pays/index.html.twig', [
            'paysList' => $manager->getRepository(Pays::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route('/pays/{id}/update', name:'update_pays', methods:['GET', 'POST'], requirements:['id' => '\d+'])]
    public function update(int $id, ManagerRegistry $manager, request $request): Response
    {
        $pays = $manager->getRepository(Pays::class)->find($id);
        if ($pays) {
            $drapeauName = $pays->getDrapeau();
            $pays->setDrapeau(
                new File($this->getParameter('pays_dir').'/'. $pays->getDrapeau())
            );
            $form = $this->createForm(PaysType::class, $pays);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $drapeau = $form->get('drapeau')->getData();
                if ($drapeau) {
                    $pays->setDrapeau($drapeau->getClientOriginalName());
                } else {
                    $pays->setDrapeau($drapeauName);
                }
                $om = $manager->getManager();
                $om->persist($pays);
                $om->flush();
                $this->addFlash('success', "Les informations du pays ont été mises à jour");
                return $this->redirectToRoute('app_pays');
            }
            return $this->renderForm('pays/update.html.twig', [
                'form' => $form
            ]);
        } else {
            $this->addFlash('danger', "Ce pays n'a pas été trouvé");
            return $this->redirectToRoute('app_pays');
        }
    }

    #[Route('pays/{id}/delete', name:'delete_pays', methods:['GET'], requirements:['id' => '\d+'])]
    public function delete (int $id, ManagerRegistry $manager): Response
    {
        $pays = $manager->getRepository(Pays::class)->find($id);
        if ($pays) {
            $om = $manager->getManager();
            $om->remove($pays);
            $om->flush();
            $this->addFlash('success', 'Pays enlevé de la liste des pays participants');
        } else {
            $this->addFlash('danger', "Le pays n'a pas été trouvé et n'a pas pu être retiré de la liste");
        }
        return $this->redirectToRoute('app_pays');
    }
}
