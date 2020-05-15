<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), false);
        $email = $content->_username;
        $password = $content->_password;
        $firstName = $content->firstName;
        $lastName = $content->lastName;

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRegKey('renew');

        $em->persist($user);
        try {
            $em->flush();
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], 409);
        }

        return $this->json(sprintf('User created'));
    }

}