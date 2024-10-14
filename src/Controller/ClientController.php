<?php

namespace App\Controller;

use App\DTO\ClientSearchDTO;
use App\Entity\Client;
use App\Entity\User;
use App\Form\ClientSearchType;
use App\Form\ClientType;
use App\Form\UserType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    #[Route('/clients', name: 'clients.index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 3);
        $clients = $clientRepository->showPaginated($page, $limit);
        $totalClients = $clientRepository->countClients();
        $totalPages = ceil($totalClients / $limit);
        $clientSeach = new ClientSearchDTO();
        $form = $this->createForm(ClientSearchType::class, $clientSeach);
        return $this->render('client/index.html.twig', [
            'formClientSearch' => $form->createView(),
            'clients' => $clients,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/clients/show/{id?}', name: 'clients.show', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/clients/search/telephone', name: 'clients.searchclientByTelephone', methods: ['GET'])]
    public function searchclientByTelephone(Request $request): Response
    {
        $telephone = $request->query->get('tel');
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/clients/remove', name: 'clients.remove', methods: ['GET'])]
    public function remove(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/clients/store', name: 'clients.store', methods: ['GET', 'POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $client = new Client();
        $user = new User();
        $form = $this->createForm(ClientType::class, $client);
        $form1 = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $form1->handleRequest($request);
        if ($form->isSubmitted()) {
            $errorsClient = $validator->validate($client);
            if ($user->getNom() != null || $user->getPrenom() != null || $user->getLogin() != null || $user->getPassword() != null) {
                $errorsUser = $validator->validate($user);
                if ($errorsClient->count() === 0 && $errorsUser->count() === 0) {
                    $user->setCreateAt(new \DateTimeImmutable());
                    $user->setUpdateAt(new \DateTimeImmutable());
                    $user->setBlocked(false);
                    $entityManager->persist($user);
                    $client->setUsers($user);
                } else {
                    return $this->render('client/form.html.twig', [
                        'formUser' => $form1->createView(),
                        'formClient' => $form->createView(),
                        'errorsClient' => $errorsClient,
                        'errorsUser' => $errorsUser,
                    ]);
                }
            }
            if (count($errorsClient) === 0) {
                $client->setCreateAt(new \DateTimeImmutable());
                $client->setUpdateAt(new \DateTimeImmutable());
                $entityManager->persist($client);
                $entityManager->flush();
                return $this->redirectToRoute('clients.index');
            } else {
                return $this->render('client/form.html.twig', [
                    'formClient' => $form->createView(),
                    'formUser' => $form1->createView(),
                    'errorsClient' => $errorsClient,
                ]);
            }
            
        }
        return $this->render('client/form.html.twig', [
            'formClient' => $form->createView(),
            'formUser' => $form1->createView(),
        ]);
    }

    #[Route('/clients', name: 'clients.search')]
    public function search(Request $request, ClientRepository $clientRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 3);
        $clients = $clientRepository->showPaginated($page, $limit);
        $clientSearchDto = new ClientSearchDTO();
        $form = $this->createForm(ClientSearchType::class, $clientSearchDto);
        $form->handleRequest($request);
        $totalClients = $clientRepository->countClients($clientSearchDto->getTelephone(), $clientSearchDto->getUsername());
        $totalPages = ceil($totalClients / $limit);
        if ($form->isSubmitted()) {
            $clients = $clientRepository->searchClients($clientSearchDto);
        }
        return $this->render('client/index.html.twig', [
            'formClientSearch' => $form->createView(),
            'clients' => $clients,
            'currentPage' => $page,
            'totalPages' => $totalPages,

        ]);
    }
}
