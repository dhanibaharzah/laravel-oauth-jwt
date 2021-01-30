<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Exception;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $path = $request->input('path') ? $request->input('path') : 'rand';

        $validator = Validator::make($request->all(), [
            'file.*' => 'image|mimes:jpeg,svg,png,jpg|max:10240',
        ]);


        if ($validator->fails()) {
            return response([
                'id' => Str::uuid(),
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        } else {
            try {
                $image_urls = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $image_url = 'https://'.config('filesystems.disks.s3.bucket').'.s3-ap-southeast-1.amazonaws.com/'.$path.'/'.$image_urls;

                $s3 = \Storage::disk('s3');
                $s3->put($path .'/'. $image_urls, file_get_contents($file), 'public'); 

                return response([
                    'id' => Str::uuid(),
                    'status' => 200,
                    'data' => [
                        'file' => $image_url,
                    ],
                ], 200);
            } catch (\Exception $e) {
                return response([
                    'id' => Str::uuid(),
                    'status' => 400,
                    'error' => [
                        'message' => $e->getMessage(),
                    ]
                ], 400);
            }
        }
    }
}
