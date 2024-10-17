<?php

namespace App\Controller;

use App\DTO\ClientSearchDTO;
use App\Entity\Client;
use App\Entity\User;
use App\Form\ClientSearchType;
use App\Form\ClientType;
use App\Form\SelectDetteType;
use App\Form\UserType;
use App\Repository\ClientRepository;
use App\Repository\DetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    #[Route('/clients', name: 'clients.index', methods: ['GET', 'POST'])]
    public function index(ClientRepository $clientRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 4);
        $count = 0;
        $maxPage = 0;
        $clientSearchDto = new ClientSearchDTO();
        $form = $this->createForm(ClientSearchType::class, $clientSearchDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $clients = $clientRepository->searchClients($clientSearchDto, $page, $limit);
            $count = $clients->count();
        } else {
            $clients = $clientRepository->paginateClients($page, $limit);
            $count = $clients->count();
        }
        $maxPage = ceil($count / $limit);
        return $this->render('client/index.html.twig', [
            'formClientSearch' => $form->createView(),
            'clients' => $clients,
            'currentPage' => $page,
            'maxPages' => $maxPage,
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
        $toggleSwitch = $request->request->get('toggleSwitch');
        if ($form->isSubmitted()) {
            $errorsClient = $validator->validate($client);
            if ($toggleSwitch != null) {
                $errorsUser = $validator->validate($user);
                if ($errorsClient->count() === 0 && $errorsUser->count() === 0) {
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

    #[Route('/clients/dette', name: 'clients.dette', methods: ['GET', 'POST'])]
    public function dettesClient(Request $request, DetteRepository $detteRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 4);
        $maxPage = 0;
        $id = $request->query->get('id');
        $count = 0;
        $maxPage = 0;
        $form = $this->createForm(SelectDetteType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dettes = $detteRepository->getDetteFiltre($form->get('montant')->getData(), $id);
            $count = $dettes->count();
            $maxPage = ceil($count / $limit);
        } else {
            $dettes = $detteRepository->getDetteClient($id, $page, $limit);
            $count = $dettes->count();
            $maxPage = ceil($count / $limit);
        }
        return $this->render('client/detteClient.html.twig', [
            'dettes' => $dettes,
            'currentPage' => $page,
            'maxPages' => $maxPage,
            'formselectDette' => $form->createView(),
        ]);
    }

}
