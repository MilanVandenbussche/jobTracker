<?php

namespace App\Http\Controllers;

use App\Models\Jobs;
use App\Models\JobsLang;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class JobController extends Controller
{
    public function getCount(Request $request)
    {
        $jobs = Jobs::where('active', 1)->count();
        return response()->json(["jobsCount" => $jobs]);
    }

    public function getJobs(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            try {
                $authUser = User::where('remember_token', $token)->first();
                if ($authUser) {
                    if ($authUser->admin) {
                        $jobs = Jobs::withTrashed()->with(['jobLang' => function ($query) use ($authUser) {
                            $query->where('language_id', $authUser->language_id);
                        },  "media", 'tags'])
                            ->whereHas('jobLang', function ($query) use ($authUser) {
                                $query->where('language_id', $authUser->language_id);
                            })->get();
                    } else {
                        $jobs = Jobs::with(['jobLang' => function ($query) use ($authUser) {
                            $query->where('language_id', $authUser->language_id);
                        }, "media", "tags"])
                            ->whereHas('jobLang', function ($query) use ($authUser) {
                                $query->where('language_id', $authUser->language_id);
                            })->where('active', 1)->get();
                    }
                    /*$data = [];
                    foreach ($jobs as $job) {
                        $data[] = [

                        ];
                    }*/
                    $favorites = $authUser->favorites->pluck('id');
                    return response()->json(["status" => "success", "jobs" => $jobs, "favorites" => $favorites], 200);
                } else {
                    return response()->json(["status" => "error", "message" => "Unauthorized"], 401);
                }
            } catch (Exception $e) {
                return response()->json(["status" => "failed", "message" => $e->getMessage()], 500);
            }
        }
    }

    public function getTags(Request $request)
    {
            $tags = Tag::where('for', 'jobs')->get();
            if($tags){
                return response()->json(["status" => "success", "tags" => $tags], 200);
            }else{
                return response()->json(["status" => "failed", "message" => "No tags were found"], 404);
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

    public function createJob(Request $request)
    {
        $user = $this->Authenticate($request->bearerToken());
        if ($user && $user->admin) {
            $job = new Jobs();
            $job->active = $request->is_active === true;
            $job->publish_date = $request->publish_date;
            if ($job->save()) {
                $data = json_decode($request->data);
                foreach ($data as $languageId => $content) {
                    $jobsLang = new JobsLang();
                    $jobsLang->job_id = $job->id;
                    $jobsLang->language_id = $languageId;
                    foreach ($content as $column => $value) {
                        $jobsLang->$column = $value;
                    }
                    if ($jobsLang->save()) {
                        continue;
                    } else {
                        $job->forceDelete();
                        Jobs::truncate();
                        return response()->json(["status" => "failed", "message" => "Something went wrong while saving the job details"], 500);
                    }
                }
                if ($request->hasFile('images')) {
                    $imageIds = [];
                    foreach ($request->file('images') as $image) {
                        $uploadedImage = $image;
                        if (array_key_exists($uploadedImage->getClientOriginalExtension(), Media::extensions())) {
                            $filename = time();
                            $fileext = $uploadedImage->getClientOriginalExtension();

                            $storagePath = storage_path("app/public/jobs/");

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
                            $image->folder = 'jobs/';
                            if ($image->save()) {
                                $imageIds[] = $image->id;
                            } else {
                                $job->forceDelete();
                                Jobs::truncate();
                                $images = Media::whereIn('id', $imageIds)->get()->each(function ($image) {
                                    $image->forceDelete();
                                });
                                Media::truncate();
                                return response()->json(["status" => "failed", "message" => "Something went wrong while saving the image"], 500);
                            }
                        } else {
                            return response()->json(["status" => "warning", "message" => "The uploaded filetype is not supported. Uploaded filetype: " . $uploadedImage->getClientOriginalExtension(), 415]);
                        }
                    }
                    if(!empty($imageIds)){
                        $job->media()->attach($imageIds);
                        $tagIds = explode(',', $request->selectedTagIds);
                        foreach($tagIds as $tagId){
                            $job->tags()->attach($tagId);
                        }
                        return response()->json(["status" => "success", "message" => "Job successfully created"], 200);
                    }
                } else {
                    return response()->json(["status" => "success", "message" => "Job created successfully"], 200);
                }
            } else {
                return response()->json(["status" => "success", "message" => "Job created successfully"], 200);
            }
        } else {
            return response()->json(["status" => "failed", 'message' => 'Something went wrong while saving the job', 500]);
        }
    }

    public function activateJob(Request $request){
        $user = $this->Authenticate($request->bearerToken());
        if($user && $user->admin){
            $job = Jobs::where('id', $request->id)->first();
            if($job && $job->active){
                $job->active = false;
                if($job->save()){
                    return response()->json(["status" => "success", "message" => "Job deactivated"], 200);
                }else{
                    return response()->json(["status" => "failed", "message" => "Something went wrong"], 500);
                }
            }elseif($job && !$job->active){
                $job->active = true;
                if($job->save()){
                    return response()->json(["status" => "success", "message" => "Job activated"], 200);
                }else{
                    return response()->json(["status" => "failed", "message" => "Something went wrong"], 500);
                }
            }else{
                return response()->json(["status" => "failed", "message" => "Job not found"], 404);
            }
        }
        return response()->json(["status" => "failed", "message" => "Unauthorized"], 401);
    }

    public function deleteJob(Request $request){
        $user = $this->Authenticate($request->bearerToken());
        if($user && $user->admin){
            $job = Jobs::where('id', $request->id)->first();
            if($job){
                if($job->delete()){
                    return response()->json(["status" => "success", "message" => "Job deleted"], 200);
                }else{
                    return response()->json(["status" => "failed", "message" => "Something went wrong"], 500);
                }
            }else{
                return response()->json(["status" => "failed", "message" => "Job not found"], 404);
            }
        }
        return response()->json(["status" => "failed", "message" => "Unauthorized"], 401);
    }

    public function restoreJob(Request $request){
        $user = $this->Authenticate($request->bearerToken());
        if($user && $user->admin){
            $job = Jobs::onlyTrashed()->where('id', $request->id)->first();
            if($job){
                if($job->restore()){
                    return response()->json(["status" => "success", "message" => "Job restored"], 200);
                }else{
                    return response()->json(["status" => "failed", "message" => "Something went wrong"], 500);
                }
            }else{
                return response()->json(["status" => "failed", "message" => "Job not found"], 404);
            }
        }
        return response()->json(["status" => "failed", "message" => "Unauthorized"], 401);
    }
}
