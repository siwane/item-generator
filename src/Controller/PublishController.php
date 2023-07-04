<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\QtiItemPackageCreator;
use App\Service\TaoPublisher;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublishController
{
    private QtiItemPackageCreator $packageCreator;
    private TaoPublisher $publisher;
    private string $taoBaseUrl;
    private string $taoQueryPath = '/tao/Main/index?structure=items&ext=taoItems&section=manage_items&uri=';

    public function __construct(QtiItemPackageCreator $packageCreator, TaoPublisher $publisher, string $taoBaseUrl)
    {
        $this->packageCreator = $packageCreator;
        $this->publisher = $publisher;
        $this->taoBaseUrl = $taoBaseUrl;
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): Response
    {
        $data = $request->request->all();
        $id = sprintf('i%s', uniqid());
        $title = sprintf('%s-%s-%s', $data['topic'], $data['difficulty'], $id);

        $filePath = $this->packageCreator->createQtiItemPackage($id, $data['quiz'], $title);

        $uri = $this->publisher->publishPackage($filePath);

        return new Response(
            sprintf('%s%s%s', $this->taoBaseUrl, $this->taoQueryPath, urlencode($uri))
        );
    }
}
