<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('pseudo', 'Pseudo'),
            EmailField::new('email', 'Email'),
            ChoiceField::new('roles', 'Roles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->allowMultipleChoices()
                ->renderExpanded(),
        ];

        if ($pageName === Crud::PAGE_NEW) {
            $fields[] = TextField::new('plainPassword', 'Mot de passe')
                ->setFormTypeOption('mapped', false)
                ->setRequired(true);
        }

        return $fields;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $plainPassword = $this->getContext()->getRequest()->request->all('User')['plainPassword'] ?? null;
            if ($plainPassword) {
                $entityInstance->setPassword(
                    $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
                );
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
