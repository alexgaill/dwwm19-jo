<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Form\AthleteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AthleteController extends AbstractController
{
    #[Route('/athlete', name: 'app_athlete', methods:['GET', 'POST'])]
    public function index(ManagerRegistry $manager, Request $request): Response
    {
        $athlete = new Athlete;
        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $photoName = md5(uniqid()). '.' . $photo->guessExtension();
                $photo->move($this->getParameter('upload_profil_dir'), $photoName);
                $athlete->setPhoto($photoName);
            }
            $om = $manager->getManager();
            $om->persist($athlete);
            $om->flush();
            $this->addFlash('success', "Athlète inscrit");
            return $this->redirectToRoute("app_athlete");
        }

        return $this->renderForm('athlete/index.html.twig', [
            'athletes' => $manager->getRepository(Athlete::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route('athlete/{id}/update', name:'update_athlete', methods:['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function update (int $id, ManagerRegistry $manager, Request $request): Response 
    {
        $athlete = $manager->getRepository(Athlete::class)->find($id);

        if ($athlete) {
            $oldPhotoName = $athlete->getPhoto();
            if (
                file_exists($this->getParameter('upload_profil_dir').'/'. $athlete->getPhoto()) && 
                ! is_dir($this->getParameter('upload_profil_dir').'/'. $athlete->getPhoto())
                ) {
                $athlete->setPhoto(
                    new File($this->getParameter('upload_profil_dir').'/'.$oldPhotoName)
                );
            }
            $form = $this->createForm(AthleteType::class, $athlete);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $photo = $form->get('photo')->getData();
                if ($photo) {
                    $photoName = md5(uniqid()).'.'. $photo->guessExtension();
                    if (
                        file_exists($this->getParameter('upload_profil_dir').'/'. $athlete->getPhoto()) && 
                        ! is_dir($this->getParameter('upload_profil_dir').'/'. $athlete->getPhoto())
                    ) {
                        unlink($this->getParameter('upload_profil_dir').'/'. $oldPhotoName);
                    }
                    $photo->move($this->getParameter('upload_profil_dir'), $photoName);
                    $athlete->setPhoto($photoName);
                } else {
                    $athlete->setPhoto($oldPhotoName);
                }

                $om = $manager->getManager();
                $om->persist($athlete);
                $om->flush();
                $this->addFlash('success', "Infos de l'athlete mises à jour");
                return $this->redirectToRoute('app_athlete');
            }

            return $this->renderForm('athlete/update.html.twig', [
                'form' => $form
            ]);

        } else {
            $this->addFlash('danger', "Athlete inconnu");
            return $this->redirectToRoute('app_athlete');
        }
    }

    #[Route('athlete/{id}/delete', name:'delete_athlete', methods:'GET', requirements:['id' => '\d+'])]
    public function delete (int $id, ManagerRegistry $manager): Response {
        $athlete = $manager->getRepository(Athlete::class)->find($id);

        if ($athlete) {
            $om = $manager->getManager();
            $om->remove($athlete);
            $om->flush();
            $this->addFlash('success', 'Participant désinscrit');
        } else {
            $this->addFlash('danger', 'Participant non trouvé');
        }
        return $this->redirectToRoute('app_athlete');
    }
}
