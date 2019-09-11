<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 27/08/2019
 * Time: 14:51
 */

namespace App\Controller;


use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\FAIRData\Organization;
use App\Entity\FAIRData\Person;
use App\Entity\Iri;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends Controller
{
    /**
     * @Route("/demo", name="demo")
     */
    public function demo(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $languageRepository = $doctrine->getRepository(Language::class);
        $languageRepository = $doctrine->getRepository(Language::class);
        $manager = $doctrine->getManager();

        $english = $languageRepository->find("en");

        $castor = new Organization("castoredc", "Castor EDC", new Iri('https://www.castoredc.com/'));
        $radboud = new Organization("radboudumc", "Radboudumc", new Iri('https://www.radboudumc.nl/'));
        $leo = new Person("Leo Schultze Kool", new Iri('https://orcid.org/0000-0001-9217-278X'));

        $now = new DateTime();

        $fdp = new FAIRDataPoint(
            new Iri("https://fdp.castoredc.com/"),
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Castor EDC FAIR Data Point", $english)
            ])),
            "2.0",
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Castor EDC FAIR Data Point", $english)
            ])),
            new ArrayCollection([$castor]),
            $english,
            null
        );

        $catalog = new Catalog(
            "vasca",
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Registry of Vascular Anomalies", $english)
            ])),
            "1.0",
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Databases of the ERN vascular anomalies", $english)
            ])),
            new ArrayCollection([$radboud, $leo]),
            $english,
            null,
            $now,
            $now,
            null
        );

        $dataset = new Dataset(
            "radboudumc",
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Registry of Vascular Anomalies - Radboudumc", $english)
            ])),
            "1.0",
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Databases of the ERN vascular anomalies, database of the Radboudumc", $english)
            ])),
            new ArrayCollection([$radboud, $leo]),
            $english,
            null,
            $now,
            $now,
            new ArrayCollection([$leo]),
            null,
            null
        );

        $distribution = new Distribution(
            "common-data-rdf",
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Common data elements (RDF)", $english)
            ])),
            "1.0",
            new LocalizedText(new ArrayCollection([
                new LocalizedTextItem("Databases of the ERN vascular anomalies, database of the Radboudumc", $english)
            ])),
            new ArrayCollection([$radboud, $leo]),
            $english,
            null,
            $now,
            $now
        );

        $dataset->addDistribution($distribution);
        $catalog->addDataset($dataset);
        $catalog->setFairDataPoint($fdp);
        $fdp->addCatalog($catalog);

        $manager->persist($fdp);
        $manager->flush();

        return $this->render(
            'base.html.twig'
        );
    }
}
