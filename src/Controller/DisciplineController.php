<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Form\DisciplineType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisciplineController extends AbstractController
{
    #[Route('/discipline', name: 'app_discipline')]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $discipline = new Discipline;
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($discipline);
            $om->flush();
            return $this->redirectToRoute('app_discipline');
        }
        return $this->renderForm('discipline/index.html.twig', [
            'disciplines' => $manager->getRepository(Discipline::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route('discipline/{id}/update', name:'update_discipline', methods:['GET', 'POST'], requirements:['id' => '\d+'])]
    public function update (int $id, ManagerRegistry $manager, Request $request): Response
    {
        $discipline = $manager->getRepository(Discipline::class)->find($id);

        if ($discipline) {
            $form = $this->createForm(DisciplineType::class, $discipline);
            $form->handleRequest($request);

            if( $form->isSubmitted() && $form->isValid()) {
                $om = $manager->getManager();
                $om->persist($discipline);
                $om->flush();
                $this->addFlash('success', "Discipline modifiée avec succés");
                return $this->redirectToRoute('app_discipline');
            }
            
            return $this->renderForm('discipline/update.html.twig',[
                'form' => $form
            ]);
        } else {
            $this->addFlash('danger', 'Discipline non trouvée');
            return $this->redirectToRoute('app_discipline');
        }
    }

    #[Route('discipline/{id}/delete', name:'delete_discipline', methods:'GET', requirements:['id' => '\d+'])]
    public function delete(int $id, ManagerRegistry $manager): Response
    {
        $discipline = $manager->getRepository(Discipline::class)->find($id);
        if ($discipline) {
            $om= $manager->getManager();
            $om->remove($discipline);
            $om->flush();
            $this->addFlash('success', "Discipline retirée de la liste");
        } else {
            $this->addFlash('danger', 'Discpline non trouvée');
        }
        return $this->redirectToRoute('app_discipline');
    }
}
