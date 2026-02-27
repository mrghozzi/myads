<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Directory;
use App\Models\ForumTopic;
use App\Models\Option;
use App\Models\Short;
use App\Models\Status;

class StatusController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'fimg' => 'required|image|max:10000',
        ]);

        if ($request->hasFile('fimg')) {
            $file = $request->file('fimg');
            $extension = $file->getClientOriginalExtension();
            // Generate name like legacy: time_random.ext
            $filename = time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $extension;
            
            $destinationPath = base_path('upload');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $file->move($destinationPath, $filename);

            $relativePath = 'upload/' . $filename;
            $url = asset($relativePath);

            return '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%"> Uploaded </div> </div>' .
                   '<img src="' . $url . '" style="width: 100%; height: auto; border-radius: 12px; margin-top: 24px;"><br>' .
                   '<input type="text" name="img" style="visibility:hidden" value="' . $relativePath . '">';
        }

        return '<p style="color: #ff0000f7;border: 1px dashed #f00;">Invalid file</p>';
    }

    public function create(Request $request)
    {
        $request->validate([
            's_type' => 'required|integer',
        ]);

        $user = Auth::user();
        $uid = $user->id;
        $type = $request->input('s_type');
        $txt = $request->input('txt');
        $time = time();
        $statu = 1;

        DB::beginTransaction();
        try {
            // Type 1: Directory Link
            if ($type == 1) {
                $request->validate([
                    'name' => 'required|string',
                    'url' => 'required|url',
                    'categ' => 'required|integer',
                ]);

                $dir = Directory::create([
                    'uid' => $uid,
                    'name' => $request->name,
                    'url' => $request->url,
                    'txt' => $txt ?? '',
                    'metakeywords' => $request->tag ?? '',
                    'cat' => $request->categ,
                    'vu' => 0,
                    'statu' => $statu,
                ]);

                $status = Status::create([
                    'uid' => $uid,
                    'date' => $time,
                    's_type' => $type,
                    'tp_id' => $dir->id,
                ]);

                // Create Short Link
                $hash = hash('crc32', $request->url . $dir->id);
                Short::create([
                    'uid' => $uid,
                    'sho' => $hash,
                    'url' => $request->url,
                    'clik' => 0,
                    'sh_type' => $type,
                    'tp_id' => $dir->id,
                ]);

                DB::commit();
                return redirect()->route('directory.show.short', $dir->id);

            } 
            // Type 4: Image Post
            elseif ($type == 4) {
                $path = null;
                if ($request->has('img') && is_string($request->input('img'))) {
                    $path = $request->input('img');
                } elseif ($request->hasFile('img')) {
                    $file = $request->file('img');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $extension;
                    
                    $destinationPath = base_path('upload');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }
                    $file->move($destinationPath, $filename);
                    
                    $path = 'upload/' . $filename;
                }

                if (!$path) {
                    throw new \Exception('Image is required');
                }

                $topic = ForumTopic::create([
                    'uid' => $uid,
                    'name' => 'image',
                    'txt' => $txt ?? '',
                    'cat' => 0,
                    'statu' => $statu,
                ]);

                $status = Status::create([
                    'uid' => $uid,
                    'date' => $time,
                    's_type' => $type,
                    'tp_id' => $topic->id,
                ]);

                Option::create([
                    'name' => $time, // Legacy uses timestamp as name
                    'o_valuer' => $path,
                    'o_type' => 'image_post',
                    'o_parent' => $topic->id,
                    'o_order' => $uid,
                    'o_mode' => 'file',
                ]);

                DB::commit();
                // Legacy redirects to /t{id}
                return redirect()->route('forum.topic', $topic->id);
            } 
            // Type 100: Text Post
            elseif ($type == 100) {
                $request->validate([
                    'txt' => 'required|string',
                ]);

                $topic = ForumTopic::create([
                    'uid' => $uid,
                    'name' => 'post',
                    'txt' => $txt,
                    'cat' => 0,
                    'statu' => $statu,
                ]);

                Status::create([
                    'uid' => $uid,
                    'date' => $time,
                    's_type' => $type,
                    'tp_id' => $topic->id,
                ]);

                DB::commit();
                return redirect()->route('forum.topic', $topic->id);
            }

            DB::rollBack();
            return back()->with('error', 'Invalid post type');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
