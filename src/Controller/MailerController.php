<?php
namespace App\Controller;

use App\Entity\Lead;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\Smtp\Auth\XOAuth2Authenticator;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchType;

class MailerController extends AbstractController
{
    #[Route('/contact', name: 'app_email')]
    public function sendEmail(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		
		$username = $this->getUser();

        $formSearch = $this->createForm(SearchType::class);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();
			return $this->redirectToRoute('app_search', [
				'dataSearch' => $data,
				'formSearch' => $formSearch,
			]);
        }
		
		$transport = new EsmtpTransport(
			host: 'oauth-smtp.domain.tld',
			authenticators: [new XOAuth2Authenticator()]
		);

        $lead = new Lead();
		
		$formEmail = $this->createForm(ContactType::class, $lead);

        $formEmail->handleRequest($request);

        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $lead->setEmail($username->getEmail());

            $entityManager->persist($lead);
            $entityManager->flush();

			$email = (new Email())
					->from($username->getEmail())
					->to('example@you.com')
					->subject($lead->getSubject())
					->text($this->renderView('email/contact.html.twig',array('lead'=>$lead)))
					->html($this->renderView('email/contact.html.twig',array('lead'=>$lead)));

            try {
                $mailer->send($email);
                // $type peut être : success, warning, danger, etc.
                // $message : Contient le contenu de la notification
                $this->addFlash('success', 'Content sent successfully');
                return $this->redirectToRoute('app_homepage');
            } catch (TransportExceptionInterface $e) {
                // $type peut être : success, warning, danger, etc.
                // $message : Contient le contenu de la notification
                $this->addFlash('danger', 'Content not sent due to error !');
                return $this->redirectToRoute('app_email');
            }
        }

        return $this->render('contact.html.twig', [
			'formEmail' => $formEmail,
			'formSearch' => $formSearch,
			'pfpName' => $username->getPfpName(),
		]);
    }
}