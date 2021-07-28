<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Entity\User;
use App\Form\FilmType;
use App\Form\ImpressionsType;
use App\Repository\FilmRepository;
use Container5I8CFFC\getFilmTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class FilmController extends AbstractController
{
    /**
     * @Route("/film", name="film")
     */
    public function index(FilmRepository $repo): Response
    {
        $films = $repo->findAll();
        return $this->render('film/index.html.twig', [
            'controller_name' => 'FilmController',
            'films' => $films
        ]);
    }

    /**
     * @Route("/film/{id}", name="show_film")
     *
     */
    public function show(Film $film,Request $req,EntityManagerInterface $manager): Response
    {   $impression= new Impression();
        $formulaire = $this->createForm(ImpressionsType::class,$impression);
        $formulaire->handleRequest($req);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $impression->setUser($this->getUser()) ;
            $impression->setCreatedAt(new \DateTime());
            $impression->setFilm($film);
            $manager->persist($impression);
            $manager->flush();
            return $this->redirectToRoute('show_film', ["id" => $film->getId()]);
        }

        return $this->render('film/show.html.twig', [
            'controller_name' => 'FilmController',
            'film' => $film,
            'formulaireImpression' => $formulaire->createView()
        ]);
    }

    /**
     *
     * @Route("/film/new", name="film_new", priority=2)
     * @Route("/film/edit/{id}", name="film_edit", priority=2)
     */
    public function new(Request $req,EntityManagerInterface $manager, Film $film = null): Response
    {

        $user = $this->getUser();
        if($user){
        $modeCreation = false;

        if (!$film) {
            $film = new Film();
            $modeCreation = true;
            $film->setUser($user);
        }
        $formulaire = $this->createForm(FilmType::class,$film);
        $formulaire->handleRequest($req);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            $manager->persist($film);
            $manager->flush();

            // redirection vers le chemin show
            return $this->redirectToRoute('show_film', ["id" => $film->getId()]);
        }
        return $this->render('film/create.html.twig', [
            'formulaireFilm' => $formulaire->createView(),
            "creation" => $modeCreation,
            "film" => $film
        ]);
        }else{
            return die('Vous devez etre connectée pour faire ça !');
        }
    }

    /**
     *
     * @Route("/film/delete/{id}", name="film_delete",priority=1)
     *
     */
    public function delete(Film $film,EntityManagerInterface $manager)
    {   $user = $this->getUser();
        if($user){
        $manager->remove($film);
        $manager->flush();
        return $this->redirectToRoute('film');
    }else{
            return die('Vous devez etre connectée pour faire ça !');
        }
    }
}
