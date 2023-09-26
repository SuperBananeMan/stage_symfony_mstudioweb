<?php

namespace App\Controller;

use App\Entity\Adminforum;
use App\Entity\Adminforumpage;
use App\Form\SearchType;
use App\Repository\AdminforumRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function new(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, UserRepository $repository, AdminforumRepository $adminforumrepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $username = $this->getUser();

        $formSearch = $this->createForm(SearchType::class);

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

        $queryBuilderadminforum = $adminforumrepository->adminforumTakerAll();
        $adapteradminforum = new QueryAdapter($queryBuilderadminforum);
        $pagerfantaadminforum = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapteradminforum,
            $request->query->get('page', 1),
            99
        );

        return $this->render('admin/admin.html.twig', [
            'pageradminforum' => $pagerfantaadminforum,
            'formSearch' => $formSearch,
            'pfpName' => $username->getPfpName(),
        ]);
    }

    #[Route('/admin/page', name: 'app_admin_page_simple')]
    public function forumPagesimple(Request $request, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, UserRepository $repository, AdminforumRepository $adminforumrepository): Response
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

        return $this->render('admin/admin_page.html.twig', [
            'slug' => '',
            'formSearch' => $formSearch,
            'pfpName' => $username->getPfpName(),
        ]);
    }

    #[Route('/admin/page/{slug}', name: 'app_admin_page')]
    public function forumPage(Request $request, Adminforum $adminforum, Adminforumpage $adminforumpage, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, UserRepository $repository, AdminforumRepository $adminforumrepository, string $slug = null): Response
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

        $queryBuilderadminforum = $adminforumrepository->adminforumTakerPage();
        $adapteradminforum = new QueryAdapter($queryBuilderadminforum);
        $pagerfantaadminforum = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapteradminforum,
            $request->query->get('page', 1),
            99
        );

        return $this->render('admin/admin_page.html.twig', [
            'slug' => $slug,
            'pageradminforum' => $pagerfantaadminforum,
            'formSearch' => $formSearch,
            'pfpName' => $username->getPfpName(),
        ]);
    }
}