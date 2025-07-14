<?php

namespace App\Http\Controllers\content_delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class getContent extends Controller
{
    public function handleRequest($type, $path = null)
    {
        switch ($type) {
            case 'images':
                return $this->serveImage($path);
            case 'Excel':
                return $this->serveExcel($path);
            default:
                return response()->json(['error' => 'Invalid content type'], 404);
        }
    }

    private function serveImage($path)
    {
        if (!$path) {
            return response()->json(['error' => 'Path not provided'], 400);
        }
        $filePath = storage_path('app/public/' . $path);
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return Response::file($filePath);
    }

    private function serveExcel($path)
    {
        if (!$path) {
            return response()->json(['error' => 'Path not provided'], 400);
        }
        $filePath = storage_path('app/hra/question/excel/' . $path);
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return Response::file($filePath);
    }
}
