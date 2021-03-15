<?php

namespace App\Console\Commands;

use App\Models\Torrent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TorrentCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'torrent:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek status remote download';

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
        ->get();

        foreach ($torrents as $torrent) {
            // Offcloud Start
            $apiKey = 'LXu2qIs7iBKS8c5BktD3vewDi5AJhICG';

            $response = Http::post("https://offcloud.com/api/remote/status?key=$apiKey", [
                'requestId' => $torrent->request_id,
            ]);

            // lalu update ke database
            $obj = $response->getBody();
            $json = json_decode($obj, true);

            if ($json['status']['status']) {
                $torrent->download_status = $json['status']['status'];
                $torrent->save();
            }

            // Kirim ke file Log
            Log::channel('cronjob')->info("Cek Status Remote Download $torrent->name pada ".date('d M Y H:i:s'));

            //sleep for 3 seconds
            sleep(3);
        }

        $this->info('Remote Download dieksekusi pada '.date('d M Y H:i:s'));
    }
}
