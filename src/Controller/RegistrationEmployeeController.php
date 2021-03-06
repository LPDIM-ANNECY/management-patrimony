<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/manage-employee', name: 'manageEmployee_')]
class RegistrationEmployeeController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $encoder
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('manageEmployee/index.html.twig', [
            'employees' => $this->entityManager->getRepository(User::class)->findAllEmployee()
        ]);
    }

    #[Route('/read/{id}', name: 'read')]
    public function read(User $user, Request $request): Response
    {
        return $this->render('manageEmployee/read.html.twig', [
            'employee' => $user
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user, ['validation_groups' => 'registration'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre',
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Mot de pass de confirmation'],
            ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $user->setChief($this->getUser());
            $user->setRoles(['ROLE_EMPLOYEE']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur à bien été ajouté");
            return $this->redirectToRoute('manageEmployee_index');
        }

        return $this->render('manageEmployee/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(User $user, Request $request): Response
    {
        $oldData = clone $user;
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            if(strlen($user->getPassword()) > 0)
                $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            else
                $user->setPassword($oldData->getPassword());


            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur à bien été mise à jour");
            return $this->redirectToRoute('manageEmployee_index');
        }

        return $this->render('manageEmployee/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->addFlash('success', "L'utilisateur $user à bien été supprimé");
        return $this->redirectToRoute('manageEmployee_index');
    }


}
