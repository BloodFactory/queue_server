<?php

namespace App\Command\User;

use App\Entity\User;
use App\Exception\User\CreateUserException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, string $name = 'app:user:create')
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct($name);
    }

    public function configure()
    {
        $this->setDescription('Создать нового пользователя')
             ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Имя пользователя', '')
             ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Пароль', '')
             ->addOption('roles', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Роли', []);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $data = $input->getOptions();

        try {
            $this->validateInputs($data);
        } catch (CreateUserException $e) {
            $io->error($e->getMessage());
            return 1;
        }

        $user = new User();

        $user->setUsername($data['username'])
             ->setPassword($this->passwordEncoder->encodePassword($user, $data['password']))
             ->setRoles(array_unique($data['roles']));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Пользователь успешно создан');

        return 0;
    }

    /**
     * @param array $data
     * @throws CreateUserException
     */
    private function validateInputs(array $data): void
    {
        if (empty($data['username'])) throw new CreateUserException('Необходимо указать имя пользователя');
        if (empty($data['password'])) throw new CreateUserException('Необходимо указать пароль');
        if (empty($data['roles'])) throw new CreateUserException('Необходимо указать по крайней мере одну роль');
    }
}
