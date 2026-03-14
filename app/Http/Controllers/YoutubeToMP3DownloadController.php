<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class YoutubeToMP3DownloadController extends Controller
{
    private string $ApiKey;
    private string $ApiUrl = '';
    private string $searchQuery = '';
    private string $navidromeScanUrl = '';
    private string $songArtist = '';
    public function __construct()
    {
        $this->ApiKey = config('services.youtube.api_key');
        $this->navidromeScanUrl = config('services.navidrome.scan_url');
    }

    public function index()
    {
        return view('youtube', ['response' => ['items' => []]]);
    }

    public function search(Request $request)
    {
        $this->searchQuery = $request->input('search_query');
        $client = new Google_Client();
        $client->setDeveloperKey($this->ApiKey);

        $youtube = new Google_Service_YouTube($client);

        $response = $youtube->search->listSearch('snippet', [
            'q' => $this->searchQuery,
            'maxResults' => 3,
            'type' => 'video'
        ]);

        return view('youtube', ['response' => $response]);
    }

    public function download(Request $request)
    {
        $videoId = $request->input('video_id');

        $yt = new YoutubeDl();

        $url = 'https://www.youtube.com/watch?v=' . $videoId;

        $collection = $yt->download(
            Options::create()
                ->downloadPath(storage_path('app/public'))
                ->extractAudio(true)
                ->audioFormat('mp3')
                ->audioQuality(0)
                ->output('%(title)s.%(ext)s')
                ->url($url)
        );

        foreach ($collection->getVideos() as $video) {
            if ($video->getError() !== null) {
                echo "Error downloading video: {$video->getError()}.";
            } else {
                $video->getFile(); // audio file
                //Metadata
                $filePath = storage_path("app/public/". $video->getTitle(). ".mp3");
                echo($video->getChannel());
                $artist = $video->getChannel();
                $song_title = str_replace($artist, '', $video->getTitle());
                $song_title = str_replace('-', '', $song_title);
                $song_title = str_replace('()', '', $song_title);
                $song_title = str_replace('[]', '', $song_title);
                $song_title = str_replace('  ', ' ', $song_title);
                $song_title = trim($song_title);
                echo($video->getTitle());
                $this->set_metadata($filePath, $artist, $song_title);

                $artistDirectory = storage_path('app/public/' . $artist);
                if(!is_dir($artistDirectory)){
                    mkdir($artistDirectory, 0755, true);
                }
                rename($filePath, $artistDirectory . '/' . $video->getTitle() . '.mp3');
            }
        }
        $this->scan_navidrome();
        return redirect("/");
    }

    private function scan_navidrome()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($this->navidromeScanUrl);
        return redirect("/");
    }

    private function set_metadata($filePath, $artist, $song_title)
    {
        $getID3 = new \getID3;
        $tagwriter = new \getid3_writetags;
        $tagwriter->filename = $filePath;
        $tagwriter->tagformats = ['id3v2.3'];
        $tagwriter->overwrite_tags = true;
        $tagwriter->tag_encoding = 'UTF-8';
        $tagData = [
            'artist' => [$artist],
            'title' => [$song_title],
        ];
        $tagwriter->tag_data = $tagData;
        if ($tagwriter->WriteTags()) {
            echo "Tags written successfully.";
        } else {
            echo "Failed to write tags: " . implode(', ', $tagwriter->errors);
        }
    }

}
