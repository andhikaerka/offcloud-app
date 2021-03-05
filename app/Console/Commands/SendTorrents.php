<?php

namespace App\Console\Commands;

use App\Models\Torrent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTorrents extends Command
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
        // cek dulu apakah jumlah torrent uploads sudah mencapai 10?
        $torrent = Torrent::where('download_status', 'pending')
        ->first();

        // Jika ada torrent yang masih pending
        if ($torrent) {

            // Offcloud Start
            $apiKey = 'LXu2qIs7iBKS8c5BktD3vewDi5AJhICG';
        
            // Otentikasi
            // $response = Http::post("https://offcloud.com/api/remote/accounts?key=$apiKey");

            // $obj = $response->getBody();
            // $json = json_decode($obj, true);

            // Remote Download
            // $response = Http::post('https://offcloud.com/api/remote?key=LXu2qIs7iBKS8c5BktD3vewDi5AJhICG');

            // $json['data'][0]['type']

            // url: URL of downloaded resource
            // remoteOptionId: ID of the remote account where to download
            // folderId: Google Drive's ID of the folder to upload content to.

            $url = $torrent->url;
            $remoteOptionId = "599c53d30ca9a33797b29899";
            $folderId = "0B6bZ0ymthTk2ME5vb2R1RFN6NXc";

            $response = Http::post("https://offcloud.com/api/remote?key=$apiKey", [
                'url' => $url,
                'remoteOptionId' => $remoteOptionId,
                'folderId' => $folderId,
            ]);

            // get response remote download dari offcloud
            /**
             * requestId
             * fileName: the name of the requested file
             * site: website name
             * status: status of the requested file downloading. Can be ‘created’, ‘downloaded’, ‘error’.
             * originalLink: original link to the file
             * createdOn: date and time when request was processed
            **/

            // $status =$obj["data"][0]['status'];

            // atau simpan saja reponse ke log

            // lalu update ke database

            // Cek Status Remote Download
        
            // Kirim ke file Log
            Log::channel('cronjob')->info('Cek Status Remote Upload pada '.date('d M Y H:i:s'));
        
            $torrent->download_status = 'uploading';

            $torrent->save();
        }

        $this->info('Cek remote upload dieksekusi pada '.date('d M Y H:i:s'));
    }
}
