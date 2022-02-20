<?php
namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        $user = User::where('is_active', 1)
                ->where('is_deleted', 0)
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withPath(url('/api/v1/users'));
        if(count($user) > 0)
        {
            return response([
                'id' => Str::uuid(),
                'status' => 200,
                'data' => $user,
            ], 200);
        } else {
            return response([
                'id' => Str::uuid(),
                'status' => 412,
                'message' => 'No Data Available',
            ], 412);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);

        if ($validator->fails()) {
            return response([
                'id' => Str::uuid(),
                'status' => 400,
                'message' => $validator->errors()->first(),
            ], 400);
        } else {
            try {
                User::where('id', $id)->update([
                    'name' => $request->input('name'),
                ]);
                
                return response([
                    'id' => Str::uuid(),
                    'status' => 200,
                    'message' => 'Name successfully updated.',
                ], 200);
            } catch (\Exception $e) {
                return response([
                    'id' => Str::uuid(),
                    'status' => 400,
                    'message' => $e->getMessage(),
                ], 400);
            }
        }
    }

    public function delete($id)
    {
        try {
            User::where('id', $id)->update([
                'is_deleted' => 1,
            ]);
            
            return response([
                'id' => Str::uuid(),
                'status' => 200,
                'message' => 'User successfully deleted.',
            ], 200);
        } catch (\Exception $e) {
            return response([
                'id' => Str::uuid(),
                'status' => 400,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}