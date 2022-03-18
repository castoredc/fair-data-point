<?php
declare(strict_types=1);

namespace App\Controller\UserInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToolsController extends AbstractController
{
    /** @Route("/tools/metadata-xml-parse", name="tools_metadata_xml_parse") */
    public function metadataXmlParse(Request $request): Response
    {
        return $this->render(
            'react.html.twig',
            ['title' => 'Convert Metadata XML to CSV']
        );
    }
}
