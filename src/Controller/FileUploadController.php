<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AlbumService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/file-upload', name: 'app_file_upload_')]
class FileUploadController extends AbstractController
{
//    #[Route(path: '/album/{album_id}', name: 'album', methods: ['POST'])]
//    public function uploadAlbumPhoto(Request $request, int $album_id, AlbumService $albumService): Response
//    {
//        $photo = $request->files->get('album_image');
//        $albumService->changePhoto($photo, $album_id);
//        return new Response(null, Response::HTTP_OK);
//    }
}
