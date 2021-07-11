<?php

namespace Rabble\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request, array $contentDocument, ?string $template = null)
    {
        return $this->render($template, [
            'content' => $contentDocument,
        ]);
    }
}
