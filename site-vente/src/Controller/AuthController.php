<?php
// src/Controller/AuthController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;



class AuthController extends AbstractController
{
    private $entityManager;
    private $logger;
    private $formFactory;


    public function __construct(EntityManagerInterface $entityManager, \Symfony\Component\Form\FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;

 
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request,SessionInterface $session): Response
    {
        $form = $this->formFactory->createBuilder()
        ->add('email', TextType::class)
        ->add('password', TextType::class)
        ->add('save', SubmitType::class, ['label' => 'Se connecter'])
        ->getForm();

    $form->handleRequest($request);
        $error = null;
          if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();
            $email = $data['email'];
            $password = $data['password'];
           
            
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
 
            // Vérifier si l'utilisateur existe et si le mot de passe est correct
            if ($user && $user->getPassword() === $password) {
                $session->set('user_id', $user->getId());
                // L'utilisateur est authentifié avec succès, vous pouvez effectuer des actions supplémentaires ici
                return $this->redirectToRoute('vehicule_show'); // Rediriger vers une autre page après la connexion réussie
            } else {
                // L'authentification a échoué, définir le message d'erreur
                $error = 'Adresse e-mail ou mot de passe incorrect.';
            }
        }

        return $this->render('login.html.twig', [
            'form' => $form->createView(),
            'error' => $error, // Passer le message d'erreur au rendu de la page
        ]);
    }
}
