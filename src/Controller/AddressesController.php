<?php

namespace App\Controller;

use App\Entity\Addresses;
use App\Form\AddressesType;
use App\Repository\AddressesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/addresses')]
class AddressesController extends AbstractController
{
    #[Route('/', name: 'app_addresses_index', methods: ['GET'])]
    public function index(AddressesRepository $addressesRepository): Response
    {
        return $this->render('addresses/index.html.twig', [
            'addresses' => $addressesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_addresses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AddressesRepository $addressesRepository): Response
    {
        $address = new Addresses();
        $form = $this->createForm(AddressesType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $address->setUser($user);
            $addressesRepository->save($address, true);

            $this->addFlash('address_message', 'Your address has been saved successfully');
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('addresses/new.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    // #[Route('/{id}', name: 'app_addresses_show', methods: ['GET'])]
    // public function show(Addresses $address): Response
    // {
    //     return $this->render('addresses/show.html.twig', [
    //         'address' => $address,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_addresses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Addresses $address, AddressesRepository $addressesRepository): Response
    {
        $form = $this->createForm(AddressesType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressesRepository->save($address, true);

            $this->addFlash('address_message', 'Your address has been edited successfully');
            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('addresses/edit.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_addresses_delete', methods: ['POST'])]
    public function delete(Request $request, Addresses $address, AddressesRepository $addressesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $addressesRepository->remove($address, true);

            $this->addFlash('address_message', 'Your address has been deleted successfully');
        }

        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }
}
