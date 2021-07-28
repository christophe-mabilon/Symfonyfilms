<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ImpressionsController extends AbstractController
{
    /**
     * @Route ("/impression/edit/{id}", name="edit_impression")
     */
    public function edit(Impression $impression, Request $req, EntityManagerInterface $manager)
    {   $user = $this->getUser();
        if($user){
        $formulaire = $this->createForm(ImpressionsType::class, $impression);
        $formulaire->handleRequest($req);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $impression->setUser($user);
            $impression->setCreatedAt(new \DateTime());
            $film = $impression->getFilm();
            $impression->setFilm($film);
            $manager->persist($impression);
            $manager->flush();
            // redirection vers le chemin show
            //return $this->redirectToRoute('show_film', ["id" => $impression->getId()]);
        }
        return $this->render('impressions/edit.html.twig', [
            'formulaireImpression' => $formulaire->createView(),
            "impression" => $impression,'film'=> $impression->getFilm()
        ]);
        }else{
            return die('Vous devez etre connectée pour faire ça !');
        }
    }

    /**
     *
     * @Route("/impression/delete/{id}", name="impression_delete")
     *
     */
    public function delete(Impression $impression,EntityManagerInterface $manager)
    {   $user = $this->getUser();
        if($user){
        $manager->remove($this->impression);
        $manager->flush();
        return $this->redirectToRoute('show_film');
        }else{
            return die('Vous devez etre connectée pour faire ça !');
        }

    }
}
