<?php

namespace App\Console\Commands;

use App\Models\Torrent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TorrentDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'torrent:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus torrent yang status-nya sudah downloaded';

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
        $torrents = Torrent::where('download_status', 'downloaded')->get();

        foreach ($torrents as $torrent) {

            // move file to recycle bin
            Storage::disk('public')->move("torrent_upload/$torrent->name", "torrent_upload_deleted/$torrent->name");

            Torrent::destroy($torrent->id);

            // Kirim ke file Log
            Log::channel('cronjob')->info("Delete downloaded $torrent->name pada ".date('d M Y H:i:s'));

            //sleep for 3 seconds
            sleep(3);
        }

        $this->info('Delete Downloaded Torrent dieksekusi pada '.date('d M Y H:i:s'));
    }
}
