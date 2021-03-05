<?php

namespace App\Http\Controllers;

use App\Models\Torrent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TorrentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $torrents = Torrent::all();

        return view('torrent.index', compact('torrents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Torrent $torrent)
    {
        $files = $request->file('file');

        if ($request->hasFile('file')) {
            foreach ($files as $file) {
                // upload file-nya dulu
                $fileName = $file->getClientOriginalName();
                $dir = $file->storeAs('torrent_upload', $fileName, 'public');
                $nameDir = url('/').'/storage/'.$dir;

                //store to database
                $torrent->name = $fileName;
                $torrent->url = $nameDir;
                $torrent->download_status = "pending";
                
                $torrent->save();
            }
        }

        return redirect()->route('dashboard');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
