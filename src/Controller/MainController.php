<?php

namespace App\Controller;

use App\Entity\LstPokemon;
use App\Repository\LstPokemonRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

#[Route('/', name: 'main_')]
final class MainController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route('/pokemons', name: 'pokemons', methods: ['GET'])]
    public function pokemons(Request $request, LstPokemonRepository $lstPokemonRepository): Response
    {
        $sort = $request->query->get('sort'); // 'nom' ou 'capture'

        if ($sort === 'capture') {
            echo('test');
            $lstPokemons = $lstPokemonRepository->findBy([], ['estCapture' => 'DESC']);
        } else if ($sort === 'nom') {
            $lstPokemons = $lstPokemonRepository->findBy([], ['nom' => 'ASC']);
        } else {
            $lstPokemons = $lstPokemonRepository->findAll();
        }

        return $this->render('main/pokemons.html.twig', [
            'lstPokemons' => $lstPokemons,
            'currentSort' => $sort,
        ]);
    }

    #[Route('/pokemons/{id}', name: 'pokemon', methods: ['GET'])]
    public function detail(LstPokemon $pokemon, LstPokemonRepository $lstPokemonRepository): Response
    {
        return $this->render('main/pokemon.html.twig', [
            'pokemon' => $pokemon,
        ]);
    }

    #[Route('/pokemons/{id}/switchCapture', name: 'switchCapture', methods: ['GET'])]
    public function switchCapture(int $id, LstPokemonRepository $lstPokemonRepository, EntityManagerInterface $em): Response
    {
        $pokemon = $lstPokemonRepository->find($id);
        if (!$pokemon) {
            throw $this->createNotFoundException('Pokémon non trouvé');
        }

        $pokemon->setEstCapture(!$pokemon->isEstCapture());
        $em->persist($pokemon);
        $em->flush();
        return $this->redirectToRoute('main_pokemons');
    }
}
