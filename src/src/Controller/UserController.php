<?php

namespace App\Controller;

use App\Entity\User;
use http\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Storage\StorageInterface;
use App\Helpers\ObjectUtils;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
use Doctrine\ORM\Mapping\Entity;

class UserController extends AbstractController
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     *
     * @var TokenGeneratorInterface
     */
    private $fosToken;

    /**
     * @var TwigSwiftMailer
     */
    private $fosMailer;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        StorageInterface $storage,
        TokenGeneratorInterface $tokenGenerator,
        TwigSwiftMailer $fosMailer,
        LoggerInterface $logger
    )
    {
        $this->storage   = $storage;
        $this->fosToken  = $tokenGenerator;
        $this->fosMailer = $fosMailer;
        $this->logger    = $logger;
    }

    public function meAction(Request $request)
    {
        $params = json_decode($request->getContent(), true);

        $user = $this->getUser();
        if ($params && count($params))
        {
            $user = $this->initialize($user, $params, $this->getIgnoreInitFields());
            $em   = $this->getDoctrine()->getManager();

            //sets birthDate to Date object
            if (isset($params['birthDate']))
            {
                $date  = new \DateTime();
                $dt    = explode("-", $params['birthDate']);
                $year  = $dt[0];
                $month = $dt[1];
                $day   = explode("T", $dt[2])[0];
                $date->setDate($year, $month, $day);
                $user->setBirthDate($date);
            }

            $em->persist($user);
            $em->flush();
        }
        return $user;
    }

    protected function getIgnoreInitFields()
    {
        return ["birthDate", "password"];
    }

    public function registerAction(Request $request)
    {
        $params = json_decode($request->getContent(), true);
        if (!$params || !count($params) || !isset($params["username"]) || !isset($params["email"]))
        {
            throw new InvalidArgumentException("Needs params to register");
        }
        //check email and username
        $email = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($params["email"]);
        if ($email)
        {
            throw new InvalidArgumentException("Email in use");
        }

        //create  User
        $em   = $this->getDoctrine()->getManager();
        $user = $this->objUtils->initialize(new User(), $params, $this->getIgnoreInitFields());

        //check password
        $pasword = null;
        if (isset($params["password"]) || isset($params["plainPassword"]))
        {
            if (isset($params["password"]))
            {
                //check passwords match
                if ($params["password"] !== $params["password_conf"])
                {
                    throw new InvalidArgumentException("Passwords not match");
                }
                $pasword = $params["password"];
            }
            if (isset($params["plainPassword"]))
            {
                $pasword = $params["plainPassword"];
            }
        }
        else
        {
            $user->setPlainPassword($pasword);
        }


        if (!isset($params["enabled"]) || $params["enabled"] !== false || $params["enabled"] !== 'false')
        {
            $user->setEnabled(true);
        }
        //save
        $em->persist($user);
        $em->flush();
        return $user;
    }

    public function logoutAction(Request $request)
    {
        $token  = $this->get("security.token_storage")->getToken();
        //unregister device
        $device = $this->getDoctrine()->getRepository(Device::class)->findOneByToken($token);
        if ($device)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($device);
            $em->flush();
        }
        try
        {
            $request->getSession()->invalidate();
            $this->get("security.token_storage")->setToken(null);
            return new Response();
        }
        catch (\Exception $e)
        {
            return new Response($e, 400);
        }
    }

    public function uploadImageAction(Request $request, $id = null)
    {
        $uploadedFile = $request->files->get('file');
        //get user
        $user         = $id ? $this->getDoctrine()->getRepository(User::class)->find($id) : $this->getUser();
        $user->setImageFile($uploadedFile);
        //save data
        $em           = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response(getEnv("BASE_URL") . $this->storage->resolveUri($user, "imageFile"));
    }

    public function resetPasswordRequestAction(Request $request)
    {
        $userData = json_decode($request->getContent(), true);
        $user     = $this->getDoctrine()->getRepository(User::class)->findOneBy(["email" => $userData["email"]]);
        if (null === $user)
        {
            throw new NotFoundHttpException("User with mail : " . $userData["email"] . " not found");
        }
        if ($user->isPasswordRequestNonExpired($this->getParameter('fos_user.resetting.token_ttl')))
        {
//            throw new BadRequestHttpException('Password request alerady requested');
        }
        if (null === $user->getConfirmationToken())
        {
            $user->setConfirmationToken($this->fosToken->generateToken());
        }
        $this->fosMailer->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return new Response(null, Response::HTTP_OK);
    }

    /**
     *
     * @param Entity $obj
     * @param array $data
     * @param string[] $exclude
     * @return Entity
     */
    public function initialize($obj, $data, array $exclude = [])
    {
        foreach ($data as $key => $value)
        {
            if (!in_array($key, $exclude))
            {
                if (is_object($data))
                {
                    $gettername = 'get' . ucfirst($key);
                    if (!method_exists($data, $gettername))
                    {
                        continue;
                    }
                }
                $functionName = 'set' . ucfirst($key);
                if (method_exists($obj, $functionName))
                {
                    $obj->$functionName(is_object($data) ? $data->$gettername() : $value);
                }
            }
        }
        return $obj;
    }
}
