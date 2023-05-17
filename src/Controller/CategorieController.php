<?php

namespace App\Controller;

use App\Entity\Categories;
use Ob\HighchartsBundle\Highcharts\Highchart;
use App\Form\CategorieType;
use App\Manager\ManagerRegistry;
use App\Repository\CategoriesRepository;
use App\Repository\OffreRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 */
class CategorieController extends AbstractController
{
/**
 * @IsGranted("ROLE_ADMIN")
 */
    #[Route('/categorie', name: 'afficher_categorie')]
    public function index(CategoriesRepository $repository): Response
    {

        $Categorie = $repository->findAll();
        // $ob = new Highchart();
        // $ob->chart->renderTo('piechart');
        // $ob->title->text('stat categorie');
        // $ob->plotOptions->pie(array(
        //     'allowPointSelect' => true,
        //     'cursor' => 'pointer',
        //     'dataLabels' => array('enabled' => false),
        //     'showInLegend' => true
        // ));
        // $categories = $repository->findAll();
        // $data2 = [];
        // $categNom = [];
        // $categCount = [];
        // $somme = 0;
        // $compt[] = 0;
        // foreach ($categories as $categorie) {
        //     $categNom[] = $categorie->getNomCategorie();
        //     $categCount[] = $repository->getproduits();
        // }
        // $i = 0;
        // foreach ($categNom as $p) {
        //     array_push($data2, [$categNom[$i], $categCount[0][$i]['num']]);
        //     $i++;
        // }
        // $ob->series(array(array('type' => 'pie', 'name' => 'Nombre des offres', 'data' => $data2)));
      return $this->render('Back/categorie/index.html.twig', [
            'categorie' => $Categorie
        ]);
    }

/**
 * @IsGranted("ROLE_ADMIN")
 */
    #[Route('/ajout_categorie', name: 'ajout_categorie')]
    function Ajout(Request $request, SluggerInterface $slugger)
    {
        $Categorie = new Categories();
        $form = $this->createForm(CategorieType::class, $Categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('imageCategorie')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('imageCategorie'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $Categorie->setImageCategorie($newFilename);
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($Categorie);
            $em->flush();

            return $this->redirectToRoute('afficher_categorie');
        }
        return $this->render('Back/categorie/ajout_categorie.html.twig', [
            'f' => $form->createView(),

        ]);
    }


/**
 * @IsGranted("ROLE_ADMIN")
 */
    #[Route('/modif_categorie/{id}', name: 'modif_categorie')]

    function Modif(CategoriesRepository $repository, $id, Request $request)
    {
        $Categorie = $repository->find($id);
        $form = $this->createForm(CategorieType::class, $Categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("afficher_categorie");
        }
        return $this->render(
            'Back/categorie/modif_categorie.html.twig',
            [
                'f' => $form->createView(),
                "form_title" => "Modifier une  offre"
            ]
        );
    }






    #[Route('/Deletecategorie/{id}', name: 'supp_categorie')]
    function Supprimer($id, CategoriesRepository $rep, OffreRepository $rep2)
    {
        // $offre=$rep2->findBy($id)
        $Categorie = $rep->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($Categorie);
        $em->flush();
        return $this->redirectToRoute('afficher_categorie');
    }
}
