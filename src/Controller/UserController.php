<?php

namespace App\Controller;

use App\Form\NewPasswordType;
use App\Form\UserType;
use App\Entity\User;
use App\EntityManager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route(
     *     "/{role}/user/index/{type}",
     *     name="user_index",
     *     requirements={
     *      "role"="admin|referent",
     *      "type"="admin|referent|producer|member"
     *     }
     * )
     */
    public function userListingAction(EntityManagerInterface $entityManager, string $type, string $role)
    {
        if ('member' != $type) {
            $users = $entityManager->getRepository(User::class)->findByRole('ROLE_'.strtoupper($type), $this->getUser());
        } else {
            $users = $entityManager->getRepository(User::class)->findByRoleOrNoRole('ROLE_'.strtoupper($type), $this->getUser());
        }
        $title = '';
        if ('referent' == $type) {
            $title = 'Référents/es';
        } elseif ('producer' == $type) {
            $title = 'Producteurs/trices';
        } elseif ('member' == $type) {
            $title = 'Consom\'acteurs/trices';
        }

        return $this->render('user/index.html.twig', [
            'role' => $role,
            'type' => $type,
            'users' => $users,
            'title' => $title,
        ]);
    }

    /**
     * @Route(
     *     "/{role}/user/view",
     *     name="user_view",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *     }
     * )
     */
    public function userViewAction(Request $request, EntityManagerInterface $entityManager, string $role, Pdf $knpSnappy, ParameterBagInterface $parameterBag)
    {
        $members = $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_MEMBER');
        $referents = $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_REFERENT');
        $producers = $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_PRODUCER');
        $admins = $entityManager->getRepository(User::class)->findByRoleAndActive('ROLE_ADMIN');

        $isPdf = $request->query->get('pdf');
        $html = $this->renderView('user/view.html.twig', [
            'role' => $role,
            'members' => $members,
            'referents' => $referents,
            'producers' => $producers,
            'admins' => $admins,
            'isPdf' => $isPdf,
        ]);
        if ($isPdf) {
            return new PdfResponse(
                $knpSnappy->getOutputFromHtml($html, ['user-style-sheet' => $parameterBag->get('kernel.project_dir').'/public/assets/css/pdf-color-page-break.css']),
                'membres.pdf'
            );
        }

        return new Response($html);
    }

    /**
     * @Route(
     *     "/{role}/user/form/{type}/{id}",
     *     name="user_form",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *      "type"="admin|referent|producer|member",
     *     "id"="\d+"
     *     },
     *     defaults={"id"=0}
     * )
     * @Route(
     *     "/{role}/user/profil",
     *     name="user_profil",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *     },
     * )
     */
    public function userEditAction(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, string $role, string $type = null, User $user = null, RouterInterface $router)
    {
        $isAccount = null == $type;
        $user = $isAccount ? $this->getUser() : $user;
        $isNew = $user ? false : true;
        $user = $user ?? new User();
        if (!$isNew) {
            $user->setPlainPassword($user->getPassword());
        } else {
            $user->addRole('ROLE_'.strtoupper($type));
        }
        $form = $this->createForm(UserType::class, $user, [
            'role' => $role,
            'type' => $type,
            'isAccount' => $isAccount,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($isNew) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->setActive(false);
                if (null === $user->getParent()) {
                    $user->setParent($this->getUser());
                }
                if ('producer' == $type) {
                    $user->setOrder(($entityManager->getRepository(User::class)->findMaxOrder() ?? 0) + 1);
                }
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', $isNew ? 'Le compte a été créé.' : 'Le compte a été mis à jour.');
            if ($isAccount) {
                return $this->forward('App\Controller\DocumentController::documentViewAction', [
                    'role' => $role,
                    'name' => 'homepage',
                ]);
            }

            return $this->forward('App\Controller\UserController::userListingAction', [
                'role' => $role,
                'type' => $type,
            ]);
        }

        $title = $isNew ? 'Inscription ' : 'Mise à jour ';
        if ('referent' == $type) {
            $title .= 'référent/e';
        } elseif ('producer' == $type) {
            $title .= 'producteur/trice';
        } elseif ('member' == $type) {
            $title .= 'consom\'acteur/trice';
        }
        if ($isAccount) {
            $title = 'Mon compte';
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
            'title' => $title,
            'role' => $role,
            'user' => $user,
            'isAccount' => $isAccount,
        ]);
    }

    /**
     * @Route(
     *     "/{role}/user/password/{id}",
     *     name="user_password",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *     "id"="\d+"
     *     },
     * )
     * @Route(
     *     "/{role}/user/profil/password",
     *     name="user_profil_password",
     *     requirements={
     *      "role"="admin|referent|producer|member",
     *     },
     * )
     */
    public function userPasswordAction(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, string $role, User $user = null)
    {
        $isAccount = null == $user;
        $user = $isAccount ? $this->getUser() : $user;
        $form = $this->createForm(NewPasswordType::class, $user, [
            'isAccount' => $isAccount,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setNew(false);
            $oldPassword = $form->has('oldPassword') ? $form->get('oldPassword')->getData() : '';
            if (!$form->has('oldPassword') || $passwordEncoder->isPasswordValid($user, $oldPassword)) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Le mot de passe a été mis à jour.');

            $options = ['role' => $role];
            if (!$isAccount) {
                $roles = $user->getRoles();
                $options['id'] = $user->getId();
                $options['type'] = in_array('ROLE_ADMIN', $roles) ? 'admin' : (
                    in_array('ROLE_REFERENT', $roles) ? 'referent' : (
                        in_array('ROLE_PRODUCER', $roles) ? 'producer' : 'member'
                ));
            }

            return $this->forward('App\Controller\UserController::userEditAction', $options);
        }

        $title = 'Mise à jour du mot de passe';

        return $this->render('user/password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'title' => $title,
        ]);
    }

    /**
     * @Route(
     *     "/{role}/user/active/{type}/{id}",
     *     name="user_active",
     *     requirements={
     *      "role"="admin|referent",
     *      "type"="admin|referent|producer|member"
     *     },
     * )
     */
    public function activeAction(EntityManagerInterface $entityManager, UserManager $userManager, string $role, string $type, User $user)
    {
        $active = !$user->isActive();
        $user->setActive($active);
        $entityManager->persist($user);
        $userManager->changeUserActivity($user);
        $entityManager->flush();
        $this->addFlash('success', $active ? 'Le compte a été activé.' : 'Le compte a été désactivé.');

        return $this->forward('App\Controller\UserController::userListingAction', [
            'role' => $role,
            'type' => $type,
        ]);
    }

    /**
     * @Route(
     *     "/admin/user/move/producer",
     *     name="user_move",
     * )
     */
    public function moveAction(Request $request, EntityManagerInterface $entityManager)
    {
        $moved = false;
        if ($request->request->has('moves')) {
            foreach ($request->request->get('moves') as $move) {
                $user = $entityManager->getRepository(User::class)->findOneByOrder($move[0]);
                $user->setOrder($move[1]);
                $moved = true;
            }
        }
        if ($moved) {
            $entityManager->flush();
            $this->addFlash('success', 'L\'ordre du producteur a été modifié.');
        } else {
            $this->addFlash('info', 'Ce changement d\'ordre n\'est pas valide.');
        }

        return $this->forward('App\Controller\UserController::userListingAction', [
            'role' => 'admin',
            'type' => 'producer',
        ]);
    }

    /**
     * @Route(
     *     "/admin/user/delete/{id}/{type}",
     *     name="user_delete",
     * )
     */
    public function deleteAction(EntityManagerInterface $entityManager, UserManager $userManager, User $user, string $type)
    {
        $userManager->setDeleted($user);
        $entityManager->flush();
        $this->addFlash('success', 'L\'utilisateur a été supprimé.');

        return $this->forward('App\Controller\UserController::userListingAction', ['role' => 'admin', 'type' => $type]);
    }

    /**
     * @Route(
     *     "/unsubscribe",
     *     name="unsubscribe",
     * )
     */
    public function unsubscribeAction()
    {
        return new Response();
    }
}
