<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class CreateUser extends Command
{
    protected static $defaultName = 'app:create-user';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private  $encoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;

        parent::__construct();

    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user.')

            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')

            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
        ;
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $result = $this->entityManager->getRepository('App:User')->findByEmail($email);

        if(!empty($result)) {
            $output->writeln([
                                 'Email is already used!',
                                 'Email: ' . $email,
                             ]);
            return 0;
        }

        $user = new User();
        $encoded = $this->encoder->encodePassword($user, $password);

        $user->setPassword($encoded);
        $user->setEmail($email);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln([
            'Successfully created User!',
            'Email: ' . $email,
            'Password: ' . $password
                         ]);
        return 0;
    }

}