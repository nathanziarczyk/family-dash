<?php


namespace App\Controller;


use App\Entity\User;
use App\Helper\MailerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    use MailerTrait;
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), false);
        $email = $content->_username;
        $password = $content->_password;
        $firstName = $content->first_name;
        $lastName = $content->last_name;

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRegKey('renew');
        $link = 'https://wdev.be/wdev_nathan/eindwerk/confirm-email?regkey='.$user->getRegKey();

        $em->persist($user);
        try {
            $em->flush();
        } catch (\Exception $exception) {
            if($exception->getPrevious()->getCode() === '23000') return $this->json(['error' => 'Duplicate entry for email'], 409);
            return $this->json(['error' => $exception->getMessage()], 500);
        }
        $this->sendMail($email, $link );
        return $this->json(sprintf('User created'),'201', ['access-control-allow-origin'=>'*']);
    }

    /**
     * @Route("/confirm-email", name="confirm-email")
     */
    public function confirmEmail()
    {
        $regkey = $_GET['regkey'];
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $em = $this->getDoctrine()->getManager();
        $user = $userRepo->findOneBy(['regKey' => $regkey]);
        if (!$user) return $this->render('mailConfirm/mailConfirm.html.twig', [
            'title' => 'Something went wrong',
            'subtitle' => 'You may already have confirmed your email, otherwise contact us.',
            'linkTag' => 'Go to FamilyDash'
        ]);

        $user->setIsEnabled(true);
        $user->setRegKey('renew');

        $em->persist($user);
        $em->flush();

        return $this->render('mailConfirm/mailConfirm.html.twig', [
            'title' => 'Email confirmed',
            'subtitle' => 'Sign in to start using FamilyDash!',
            'linkTag' => 'Sign in'
        ]);
    }
}