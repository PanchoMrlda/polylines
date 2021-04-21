<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HandwritingController
{
    /**
     * @param Request $request
     * @return string
     */
    public function image(Request $request)
    {
//        $jsonParams = file_get_contents('php://input');
//        $params = json_decode($jsonParams, true);
//        define('UPLOAD_DIR', 'img/');
//        $img = $params['imgBase64'];
//        $img = str_replace('data:image/png;base64,', '', $img);
//        $img = str_replace(' ', '+', $img);
//        $data = base64_decode($img);
//        $file = UPLOAD_DIR . 'canvas.png';
//        $success = file_put_contents($file, $data);
//        return $success ? $file : 'Unable to save the file.';

        $imgBase64 = $request->input('imgBase64');
        $img = str_replace('data:image/png;base64,', '', $imgBase64);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = 'canvas.png';
        $success = file_put_contents($file, $data);
        return $success ? $file : 'Unable to save the file.';
    }
}
