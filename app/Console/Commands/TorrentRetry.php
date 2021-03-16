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
        $torrents = Torrent::where('download_status', 'error')
        ->whereNotNull('request_id')
        ->get();

        foreach ($torrents as $torrent) {
            // Offcloud Start
            $apiKey = 'LXu2qIs7iBKS8c5BktD3vewDi5AJhICG';

            $response = Http::get("https://offcloud.com/api/remote/retry/$torrent->request_id?key=$apiKey");

            // lalu update ke database
            $obj = $response->getBody();
            $json = json_decode($obj, true);

            if (!empty($json)) {
                if (array_key_exists('status', $json)) {
                    $torrent->download_status = $json['status']['status'];
                    $torrent->save();

                    // Kirim ke file Log
                    Log::channel('cronjob')->info("Retry Remote download $torrent->name");
                }
            }

            //sleep for 3 seconds
            sleep(2);
        }

        $this->info('Remote download dieksekusi pada '.date('d M Y H:i:s'));
    }
}
