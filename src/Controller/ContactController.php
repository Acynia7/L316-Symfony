<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
    ): Response {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Date de creation automatique
            $contact->setCreatedAt(new \DateTimeImmutable());

            // Sauvegarde en base de donnees
            $entityManager->persist($contact);
            $entityManager->flush();

            // Envoi d'email (utilise null://null si mailer non configure)
            try {
                $email = (new Email())
                    ->from($contact->getEmail())
                    ->to('contact@agence-digitale.fr')
                    ->subject('Contact : ' . $contact->getSubject())
                    ->text(
                        sprintf(
                            "De : %s %s (%s)\n\n%s",
                            $contact->getFistname(),
                            $contact->getLastname(),
                            $contact->getEmail(),
                            $contact->getMessage()
                        )
                    );

                $mailer->send($email);
            } catch (\Exception) {
                // Mailer non configure (null://null) : on ignore silencieusement
                // Le message est deja sauvegarde en BDD
            }

            $this->addFlash('success', 'Votre message a bien ete envoye. Nous vous recontacterons rapidement.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form,
        ]);
    }
}
