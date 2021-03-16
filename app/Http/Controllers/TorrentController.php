<?php

namespace App\Http\Controllers;

use App\Models\Torrent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $torrents = Torrent::when(request('category'), function ($query) {
            return $query->where('download_status', request('category'));
        })->orderBy('id', 'desc')
        ->paginate(10);

        $torrent_categories = Torrent::select(
            'download_status',
            DB::raw("COUNT(download_status) AS total")
        )
        ->groupBy('download_status')
        ->get();

        return view('torrent.index', compact('torrents', 'torrent_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $files = $request->file('files');

        if ($request->hasFile('files')) {
            foreach ($files as $file) {
                // upload file-nya dulu
                $fileName = $file->getClientOriginalName();
                $dir = $file->storeAs('torrent_upload', $fileName, 'public');
                $nameDir = url('/').'/public/storage/'.$dir;

                //store to database
                $torrent = new Torrent;
                $torrent->name = $fileName;
                $torrent->url = $nameDir;
                $torrent->download_status = "new";
                
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
    public function destroy(Torrent $torrent)
    {
        // move file to recycle bin
        Storage::disk('public')
        ->move("torrent_upload/$torrent->name", "torrent_upload_deleted/$torrent->name");

        Torrent::destroy($torrent->id);
        
        return redirect()->route('dashboard');
    }
}
