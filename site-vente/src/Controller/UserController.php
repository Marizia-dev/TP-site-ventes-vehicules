<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $entityManager;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, \Symfony\Component\Form\FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

   
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
{
    $form = $this->formFactory->createBuilder()
        ->add('email', TextType::class)
        ->add('pseudo', TextType::class)
        ->add('password', TextType::class)
        ->add('save', SubmitType::class, ['label' => 'Create User'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $userData = $form->getData();

        // Création d'une nouvelle instance de l'entité User avec les données du formulaire
        $user = new User();
        $user->setPassword($userData['password']);
        $user->setEmail($userData['email']);
        $user->setPseudo($userData['pseudo']);
        $user->setDateCreation(new \DateTime()); // Définir la date de création sur la date et l'heure actuelles
        // Vous pouvez également définir d'autres propriétés de l'utilisateur ici

        // Persistez l'utilisateur dans la base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();
 

        // Rediriger l'utilisateur vers une autre page ou afficher un message de succès
        // Ici, nous redirigeons l'utilisateur vers la liste des utilisateurs
        return $this->redirectToRoute('login');
    }

    return $this->render('add_user.html.twig', [
        'form' => $form->createView(),
    ]);
}
}