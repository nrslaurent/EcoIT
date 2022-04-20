<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Section;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        $instructors = $this->getDoctrine()->getRepository(User::class)->findby(array('isValidated' => false));
        //return parent::index();
        return $this->render('admin/easyAdmin.html.twig', [
            'instructors' => $instructors
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EcoIT');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Les Utilisateurs', 'fas fa-list', User::class);
        //yield MenuItem::linkToCrud('Les Formations', 'fas fa-list', Course::class);
        //yield MenuItem::linkToCrud('Les Sections', 'fas fa-list', Section::class);
        //yield MenuItem::linkToCrud('Les Leçons', 'fas fa-list', Lesson::class);
    }
}
