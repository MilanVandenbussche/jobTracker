<?php

namespace App\Http\Controllers;

use App\Models\Languages;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function getLanguages()
    {
        $languages = Languages::all();
        return response()->json(["languages" => $languages], 200);
    }
}
