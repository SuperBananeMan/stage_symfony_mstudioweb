<?php
namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\Smtp\Auth\XOAuth2Authenticator;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/contact', name: 'app_email')]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$username = $this->getUser();
		
		$defaultData = ['message' => 'Type your message here'];
        $formSearch = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class)
            
            ->getForm();

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formSearch->getData();
			return $this->redirectToRoute('app_search', [
				'dataSearch' => $data,
				'formSearch' => $formSearch,
				'pfpName' => $username->getPfpName(),
			]);
        }
		
		$transport = new EsmtpTransport(
			host: 'oauth-smtp.domain.tld',
			authenticators: [new XOAuth2Authenticator()]
		);
		
        
		
		$formEmail = $this->createForm(ContactType::class);

        $formEmail->handleRequest($request);

        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $formEmail->getData();
			$email = (new Email())
					->from($username->getEmail())
					->to('example@you.com')
					->subject($data['subject'])
					->text($this->renderView('email/contact.html.twig',$data))
					->html($this->renderView('email/contact.html.twig',$data));
			$mailer->send($email);
        }

        return $this->render('contact.html.twig', [
			'formEmail' => $formEmail,
			'formSearch' => $formSearch,
			'pfpName' => $username->getPfpName(),
		]);
    }
}