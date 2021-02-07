<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UpdateUserPassword extends Command
{
    protected static $defaultName = 'app:update-pass';

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
            ->setDescription('Update User password.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to change a user\'s password.')

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

        if(empty($result)) {
            $output->writeln([
                                 'Email does not exist.',
                                 'Email: ' . $email,
                             ]);
            return 0;
        }

        foreach($result as $user) {
            $encoded = $this->encoder->encodePassword($user, $password);
            $user->setPassword($encoded);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $output->writeln([
                             'Successfully change user\'s password!',
                             'Email: ' . $email,
                             'Password: ' . $password
                         ]);
        return 0;
    }

}