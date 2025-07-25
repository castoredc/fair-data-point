<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToolsController extends AbstractController
{
    #[Route(path: '/tools/metadata-xml-parse', name: 'tools_metadata_xml_parse')]
    public function metadataXmlParse(): Response
    {
        return $this->render(
            'react.html.twig',
            ['title' => 'Convert Metadata XML to CSV']
        );
    }
}
