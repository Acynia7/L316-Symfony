<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function createEntity(string $entityFqcn): Comment
    {
        $comment = new Comment();
        $comment->setCreatedAt(new \DateTimeImmutable());

        return $comment;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextEditorField::new('content', 'Contenu'),
            AssociationField::new('author', 'Auteur')
                ->formatValue(fn ($value, $entity) => $entity->getAuthor()?->getPseudo()),
            AssociationField::new('post', 'Article')
                ->formatValue(fn ($value, $entity) => $entity->getPost()?->getTitle()),
            BooleanField::new('isReported', 'Signale'),
            BooleanField::new('isValidated', 'Valide'),
            DateTimeField::new('createdAt', 'Date de creation')->hideOnForm(),
        ];
    }
}
