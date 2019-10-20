<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\EmailResetType;
use App\Form\PasswordResetType;
use App\Helper\MailHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        /*if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        }*/

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->get('form.factory')
            ->createNamedBuilder(null)
            ->add('_username', null, ['label' => 'Email'])
            ->add('_password', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('ok', SubmitType::class, ['label' => 'Ok', 'attr' => ['class' => 'btn-success btn-block']])
            ->getForm();
        return $this->render('security/login.html.twig', [
            'title' => 'Connexion',
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/password/reset", name="password_reset")
     */
    public function resetPassword(Request $request, EntityManagerInterface $entityManager, MailHelper $mailHelper)
    {
        $form = $this->createForm(EmailResetType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository(User::class)->findOneByEmail($form->getData()['email']);
            if ($user !== null) {
                $token = uniqid();
                $user->setResetPassword($token);
                $entityManager->persist($user);
                $entityManager->flush();

                $message = $mailHelper->getMailForList('Réinitialisation mot de passe AMAP hommes de terre', [$user]);
                if (null !== $message) {
                    $message
                        ->setBody(
                            $this->renderView('emails/resetpassword.html.twig', [
                                'message' => $message,
                                'token' => $token,
                            ]),
                            'text/html'
                        )
                        ->addPart(
                            $this->renderView('emails/resetpassword.txt.twig', [
                                'message' => $message,
                                'token' => $token,
                            ]),
                            'text/plain'
                        );

                    return $mailHelper->sendMessages($request, [$message], $this->redirectToRoute('login'));
                }
            }
            return $this->redirectToRoute('login');
        }

        return $this->render('security/resetpassword.html.twig', array(
            'title' => 'Mot de passe oublié',
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/password/reset/confirm", name="password_reset_confirm")
     */
    public function resetPasswordToken(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $token = $request->query->get('token');
        $error = false;
        if ($token !== null) {
            $user = $entityManager->getRepository(User::class)->findOneByResetPassword($token);
            if ($user !== null) {
                $form = $this->createForm(PasswordResetType::class);
                $form->handleRequest($request);
                if ($form->isSubmitted()) {
                    if ($form->isValid()) {
                        $password = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());
                        $user->setPassword($password);
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->addFlash('success', 'Votre mot de passe a été mis à jour.');

                        return $this->redirectToRoute('login');
                    } else {
                        $error = true;
                    }
                }
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            $this->addFlash('danger', 'Echec lors de la mise à jour de votre mot de passe!');
        }

        return $this->render('security/resetpassword.html.twig', [
            'title' => 'Nouveau mot de passe',
            'form' => $form->createView(),
        ]);
    }
}
