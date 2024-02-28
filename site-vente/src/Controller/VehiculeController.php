<?php
// src/Controller/VehiculeController.php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;


class VehiculeController extends AbstractController
{
    private $formFactory;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, \Symfony\Component\Form\FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;

 
    }

 /**
     * @Route("/vehicules", name="vehicules_list")
     */
    public function show(EntityManagerInterface $entityManager): Response
    {
        $vehicules = $entityManager->getRepository(Vehicule::class)->findAll();

        return $this->render('/show_vehicules.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }

        /**
     * @Route("/vehicule/{id}/edit", name="edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Vehicule $vehicule): Response
{
    $form = $this->createForm(VehiculeType::class, $vehicule)
    ->add('save', SubmitType::class, ['label' => 'Modifier le véhicule']);

     
    $form->handleRequest($request);
 
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        // Rediriger vers une autre page après la modification réussie
        return $this->redirectToRoute('vehicule_show');
    }

    return $this->render('/edit_vehicule.html.twig', [
        'form' => $form->createView(),
    ]);
}

    /**
     * @Route("/vehicule/new", name="vehicule_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
         $form = $this->formFactory->createBuilder()
             ->add('en_vente', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true, 
                'multiple' => false, 
                'label' => 'En vente', 
                'required' => true, 
            ])
            ->add('nom', TextType::class)
            ->add('modele', TextType::class)
            ->add('description', TextType::class)
             ->add('image', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Ajouter une véhicule'])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
             $vehicule = new Vehicule();
            $vehicule->setImage($userData['image']);
            $vehicule->setEnVente($userData['en_vente']);
            $vehicule->setModele($userData['modele']);
            $vehicule->setNom($userData['nom']);
            $vehicule->setDescription($userData['description']);
            $vehicule->setDateCreation(new \DateTime()); // Définir la date de création sur la date et l'heure actuelles

            $entityManager->persist($vehicule);
            $entityManager->flush();

            // Rediriger vers une autre page après la création réussie
            return $this->redirectToRoute('vehicule_show');
        }

        return $this->render('/add_vehicule.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
