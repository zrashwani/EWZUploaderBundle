<?php

namespace EWZ\Bundle\UploaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * Uploader Controller.
 */
class UploaderController extends Controller
{
    /**
     * Uploads a file.
     *
     * @param UploadedFile $file      Item uploaded via the HTTP POST method
     * @param string       $folder    The target folder
     * @param string       $maxSize   File max size
     * @param string|array $mimeTypes Mime types of the file
     *
     * @return Response A Response instance
     */
    public function uploadAction(Request $request)
    {
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile || !$file->isValid()) {
            return new Response(json_encode(array(
                'event' => 'uploader:error',
                'data' => array(
                    'message' => 'Missing file.',
                ),
            )));
        }

        // validate file size and mimetype
        if (!$maxSize = $request->get('maxSize')) {
            $maxSize = $this->container->getParameter('ewz_uploader.media.max_size');
        }
        if (!$mimeTypes = $request->get('mimeTypes')) {
            $mimeTypes = $this->container->getParameter('ewz_uploader.media.mime_types');
        }
        $mimeTypes = is_array($mimeTypes) ? $mimeTypes : json_decode($mimeTypes, true);

        $fileConst = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize' => $maxSize,
            'mimeTypes' => $mimeTypes,
        ));

        $errors = $this->get('validator')->validateValue($file, $fileConst);
        if (count($errors) > 0) {
            return new Response(json_encode(array(
                'event' => 'uploader:error',
                'data' => array(
                    'message' => 'Invalid file.',
                ),
            )));
        }

        // check if exists
        if (!is_file($file->__toString())) {
            return new Response(json_encode(array(
                'event' => 'uploader:error',
                'data' => array(
                    'message' => 'File was not uploaded.',
                ),
            )));
        }

        // set drop directory
        if (!$folder = $request->get('folder')) {
            $folder = $this->container->getParameter('ewz_uploader.media.folder');
        }
        $directory = sprintf('%s/%s', $this->container->getParameter('ewz_uploader.media.dir'), $folder);

        // create directory if doesn't exists
        $filesystem = new Filesystem();
        if ($filesystem->exists($directory)) {
            $filesystem->mkdir($directory);
        }

        $file->move($directory, $filename = sprintf('%s.%s', uniqid(), $file->guessExtension()));

        return new Response(json_encode(array(
            'event' => 'uploader:success',
            'data' => array(
                'orgname' => $file->getClientOriginalName(),
                'filename' => $filename,
            ),
        )));
    }

    /**
     * Removes a file.
     *
     * @param string $filename The file name
     * @param string $folder   The target folder
     *
     * @return Response A Response instance
     */
    public function removeAction(Request $request)
    {
        $response = new Response(null, 200, array(
            'Content-Type' => 'application/json',
        ));

        if (!$filename = $request->get('filename')) {
            $response->setStatusCode(500);
            $response->setContent(json_encode(array(
                'event' => 'uploader:error',
                'data' => array(
                    'message' => 'Invalid file.',
                ),
            )));

            return $response;
        }

        if (!$folder = $request->get('folder')) {
            $folder = $this->container->getParameter('ewz_uploader.media.folder');
        }
        $filepath = sprintf('%s/%s/%s', $this->container->getParameter('ewz_uploader.media.dir'), $folder, $filename);

        // check if exists
        if (!is_file($filepath)) {
            $response->setStatusCode(500);
            $response->setContent(json_encode(array(
                'event' => 'uploader:error',
                'data' => array(
                    'message' => 'File was not uploaded.',
                ),
            )));

            return $response;
        }

        // remove file
        $filesystem = new Filesystem();
        $filesystem->remove($filepath);

        $response->setContent(json_encode(array(
            'event' => 'uploader:fileremoved',
            'data' => array(),
        )));

        return $response;
    }

    /**
     * Downloads a file.
     *
     * @param string $filename The file name
     * @param string $folder   The target folder
     *
     * @return Response A Response instance
     *
     * @throws FileException         If the file invalid
     * @throws FileNotFoundException If the file does not exist
     */
    public function downloadAction(Request $request)
    {
        if (!$filename = $request->get('filename')) {
            throw new FileException('Invalid file.');
        }

        if (!$folder = $request->get('folder')) {
            $folder = $this->container->getParameter('ewz_uploader.media.folder');
        }

        $filepath = sprintf('%s/%s/%s', $this->container->getParameter('ewz_uploader.media.dir'), $folder, $filename);

        // load file
        $file = new File($filepath);

        // read file
        $content = file_get_contents($filepath);

        return new Response($content, 200, array(
            'Content-Type' => $file->getMimeType(),
            'Content-Disposition' => sprintf('attachment;filename=%s', $file->getFilename()),
        ));
    }
}
