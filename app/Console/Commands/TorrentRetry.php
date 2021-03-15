<?php

namespace App\Console\Commands;

use App\Models\Torrent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TorrentRetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'torrent:retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry torrent remote download yang statusnya bukan 
    uploaded, 
    created, 
    downloading, 
    queued';

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
        $torrents = Torrent::where('download_status', '<>', 'downloaded')
        ->whereNotNull('request_id')
        ->where('download_status', '<>', 'created')
        ->where('download_status', '<>', 'queued')
        ->where('download_status', '<>', 'downloading')
        ->where('download_status', '<>', 'uploading')
        ->get();

        foreach ($torrents as $torrent) {
            // Offcloud Start
            $apiKey = 'LXu2qIs7iBKS8c5BktD3vewDi5AJhICG';

            $response = Http::get("https://offcloud.com/api/remote/retry/$torrent->request_id?key=$apiKey");

            // lalu update ke database
            $obj = $response->getBody();
            $json = json_decode($obj, true);

            if ($json['status']['status']) {
                $torrent->download_status = $json['status']['status'];
                $torrent->save();
            }

            // Kirim ke file Log
            Log::channel('cronjob')->info("Retry Remote download $torrent->name pada ".date('d M Y H:i:s'));

            //sleep for 3 seconds
            sleep(3);
        }

        $this->info('Remote download dieksekusi pada '.date('d M Y H:i:s'));
    }
}
