<?php

namespace App\Console\Commands;

use App\Models\Torrent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TorrentSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'torrent:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload torrent yang status file-nya pending';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // cek dulu apakah jumlah torrent uploads sudah mencapai 100?
        $torrents_count = Torrent::all()->count();

        if ($torrents_count < 100) {
            $torrent = Torrent::where('download_status', 'new')->first();

            // Jika ada torrent yang masih pending
            if ($torrent) {

            // Offcloud Start
                $apiKey = 'LXu2qIs7iBKS8c5BktD3vewDi5AJhICG';
        
                // url: URL of downloaded resource
                // remoteOptionId: ID of the remote account where to download
                // folderId: Google Drive's ID of the folder to upload content to.

                $url = $torrent->url; // resource file torrent yang akan dikirim
                $remoteOptionId = "599c53d30ca9a33797b29899";
                $folderId = "0B6bZ0ymthTk2ME5vb2R1RFN6NXc";

                $response = Http::post("https://offcloud.com/api/remote?key=$apiKey", [
                'url' => $url,
                'remoteOptionId' => $remoteOptionId,
                'folderId' => $folderId,
            ]);

                // lalu update ke database
                $obj = $response->getBody();
                $json = json_decode($obj, true);

                $torrent->download_status = $json['status'];
                $torrent->request_id = $json['requestId'];

                $torrent->save();

                // Kirim ke file Log
                Log::channel('cronjob')->info('Start Remote Upload '.$torrent->name.' pada '.date('d M Y H:i:s'));
            }
        } else {
            // jika mencapai 100 maka jangan lakukan upload
            // Kirim ke file Log
            Log::channel('cronjob')->info('Torrent sudah mencapai 100 download');
        }
        

        $this->info('Start remote upload dieksekusi pada '.date('d M Y H:i:s'));
    }
}
