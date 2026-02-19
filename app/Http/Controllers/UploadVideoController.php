<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;

class UploadVideoController extends Controller
{
    public function upload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            return response()->json(['error' => 'No file'], 400);
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {

            $file = $save->getFile();

            $name = uniqid() . '.mp4';
            $directory = storage_path('app/public/cursos/videos');

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            // 🔥 mueve el archivo reconstruido completo
            $file->move($directory, $name);

            return response()->json([
                'path' => "cursos/videos/$name",
                'url' => Storage::url("cursos/videos/$name")
            ]);
        }


        $handler = $save->handler();

        return response()->json([
            "percentage" => $handler->getPercentageDone()
        ]);
    }
}
