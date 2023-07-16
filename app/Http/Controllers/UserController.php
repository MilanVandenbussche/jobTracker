<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function getCount(Request $request)
    {
        $users = User::all()->count();
        return response()->json(["usersCount" => $users]);
    }

    public function user(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            $user = User::where('remember_token', $token)->first();
            $data = [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'profile_picture' => $user->profilePicture(),
            ];
            return response()->json(["user_data" => $data], 200);
        }

        return response()->json(["data" => null], 200);
    }

    public function getUsers(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            try {
                $authUser = User::where('remember_token', $token)->first();
                if ($authUser) {
                    if ($authUser->admin) {
                        $users = User::withTrashed()->get();
                    } else {
                        $users = User::where('id', '!=', $authUser->id)->get();
                        $users->prepend($authUser);
                    }
                    $data = [];
                    foreach ($users as $user) {
                        $data[] = [
                            "id" => $user->id,
                            "language" => $user->language ? $user->language->language_code : "-",
                            "tags" => $user->tags,
                            "name" => $user->first_name . " " . $user->last_name,
                            "email" => $user->email,
                            "created_at" => $user->created_at->diffForHumans(),
                            "deleted_at" => $user->deleted_at ? $user->deleted_at->diffForHumans() : null,
                            "profile_picture" => $user->profilePicture(),
                            "profile_picture_lg" => $user->profilePicture('lg'),
                            "admin" => $user->admin,
                        ];
                    }
                    $favorites = $authUser->favorites->pluck('id');
                    return response()->json(["users" => $data, "favorites" => $favorites], 200);
                } else {
                    return response('Unauthorized', 401);
                }
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage()], 500);
            }
        }
    }

    private function Authenticate($token)
    {
        if ($token) {
            try {
                $user = User::where('remember_token', $token)->first();
                return $user;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    public function makeAdmin(Request $request)
    {
        $user = $this->Authenticate($request->bearerToken());
        if ($user && $user->admin) {
            $userToAdmin = User::find($request->id);
            if ($userToAdmin && $userToAdmin->admin) {
                $userToAdmin->admin = false;
                if ($userToAdmin->save()) {
                    return response()->json(["message" => "$userToAdmin->first_name $userToAdmin->last_name is not an administrator anymore "], 200);
                }
            } elseif ($userToAdmin) {
                $userToAdmin->admin = true;
                if ($userToAdmin->save()) {
                    return response()->json(["message" => "$userToAdmin->first_name $userToAdmin->last_name is now an administrator"], 200);
                }
            } else {
                return response()->json(["message" => "User not found..."], 404);
            }
        } elseif ($user) {
            return response()->json(["message" => "Unauthorized"], 401);
        }
        return response()->json(["message" => "Uh-Oh", "token" => $request->headers], 400);
    }

    public function uploadImage(Request $request)
    {
        $user = $this->Authenticate($request->bearerToken());
        if ($user) {
            $prevMediaId = null;
            if($user->media_id){
                $prevMediaId = $user->media_id;
            }
            if($request->hasFile('file')){
                $uploadedImage = $request->file('file');
                if(array_key_exists($uploadedImage->getClientOriginalExtension(), Media::extensions()) ){
                    $filename = time();
                    $fileext = $uploadedImage->getClientOriginalExtension();

                    $storagePath = storage_path("app/public/profile_pictures/");

                    // Create the storage directory if it doesn't exist
                    if (!File::exists($storagePath)) {
                        File::makeDirectory($storagePath, 0755, true, true);
                    }

                    foreach (Media::resolutions() as $name => $size) {
                        $image = Image::make($uploadedImage->getRealPath());

                        if ($image->width() === $image->height()) { // Image is already square
                            $image->resize($size, $size, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        } else {
                            if (($name === 'xs' || $name === 'sm' || $name === 'md') && ($image->width() > $size && $image->height() > $size)) {
                                // Resize the image to the desired width while maintaining aspect ratio
                                $image->resize(($image->width() <= $image->height()) ? $size : null, ($image->width() >= $image->height()) ? $size : null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });

                                // Crop the height to match the width
                                $image->crop($size, $size);
                            } else {
                                $image->resize(($image->width() > $image->height()) ? $size : null, ($image->width() < $image->height()) ? $size : null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                });
                            }
                        }

                        $image->save($storagePath . $name . '_' . $filename . '.' . $fileext);
                    }

                    $image = new Media();
                    $image->media_name = $filename;
                    $image->media_ext = $fileext;
                    $image->folder = 'profile_pictures/';
                    if($image->save()){
                        $user->media_id = $image->id;
                        if($user->save()){
                            if($prevMediaId){
                                $oldMedia = Media::where('id', $prevMediaId)->first();
                                $oldFileName =  $oldMedia->media_name;
                                $oldFileExt =  $oldMedia->media_ext;
                                foreach(Media::resolutions() as  $name){
                                    if(Storage::exists('app/public/profile_pictures/' .$name . '_' . $oldFileName . '.' . $oldFileExt)){
                                        File::delete($storagePath . $name . '_' . $oldFileName . '.' . $oldFileExt);
                                    }
                                }
                                $oldMedia->delete();
                            }
                            return response()->json(["message" => "Image saved successfully"],  200);
                        }else{
                            $image->delete();
                            foreach(Media::resolutions() as  $name){
                                if(Storage::exists('app/public/profile_pictures/' .$name . '_' . $filename . '.' . $fileext)){
                                    Storage::delete('app/public/profile_pictures/' .$name . '_' . $filename . '.' . $fileext);
                                }
                            }
                            return response()->json(["message" => "Something went wrong"], 500);
                        }
                    }else{
                        return response()->json(["message" => "Something went wrong while saving the image"], 500);
                    }
                }else{
                    return response()->json(["message" => "The uploaded filetype is not supported. Uploaded filetype: " . $uploadedImage->getClientOriginalExtension() , 415]);
                }
            }else{
                return response()->json(["message" => 'No file was uploaded'], 400);
            }
        }
        return response()->json(["message" => "Uh-Oh", "token" => $request->headers], 400);
    }

    public function favorite(Request $request)
    {
        $user = $this->Authenticate($request->bearerToken());
        if ($user) {
            $userToFavorite = User::find($request->id);
            if ($userToFavorite && $user->favorites()->wherePivot('child_id', $request->id)->exists()) {
                $user->favorites()->detach($userToFavorite);
                return response()->json(["message" => "$userToFavorite->first_name $userToFavorite->last_name unfavorited"], 200);
            } elseif ($userToFavorite) {
                $user->favorites()->attach($userToFavorite);
                return response()->json(["message" => "$userToFavorite->first_name $userToFavorite->last_name favorited"], 200);
            } else {
                return response()->json(["message" => "User not found"], 404);
            }
        }
        return response()->json(["message" => "Uh-Oh", "token" => $request->headers], 400);
    }

    public function delete(Request $request)
    {
        $user = $this->Authenticate($request->bearerToken());
        if ($user && $user->admin) {
            $userToDelete = User::find($request->id);
            if ($userToDelete) {
                $userToDelete->delete();
                return response()->json(["message" => "$userToDelete->first_name $userToDelete->last_name deleted"], 200);
            } else {
                return response()->json(["message" => "User not found"], 404);
            }
        } elseif ($user) {
            return response()->json(["message" => "Unauthorized"], 401);
        }
        return response()->json(["message" => "Uh-Oh", "token" => $request->headers], 400);
    }

    public function restore(Request $request)
    {
        $user = $this->Authenticate($request->bearerToken());
        if ($user && $user->admin) {
            $userToRestore = User::withTrashed()->find($request->id);
            if ($userToRestore) {
                $userToRestore->restore();
                return response()->json(["message" => "$userToRestore->first_name $userToRestore->last_name restored"], 200);
            } else {
                return response()->json(["message" => "User not found"], 404);
            }
        } elseif ($user) {
            return response()->json(["message" => "Unauthorized"], 401);
        }
        return response()->json(["message" => "Uh-Oh", "token" => $request->headers], 400);
    }
}
