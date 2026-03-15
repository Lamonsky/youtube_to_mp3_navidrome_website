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
                $artist = $this->sanitizeFileName($video->getChannel());
                $song_title = $this->sanitizeFileName(str_replace($artist, '', $video->getTitle() .''));
                $album = $this->getAlbumInfoFromLastFM($artist, $song_title);
                echo("Artist: $artist, Song Title: $song_title, Album: $album");
                $this->set_metadata($filePath, $artist, $song_title, $album);

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

    private function set_metadata($filePath, $artist, $song_title, $album)
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
            'album' => [$album],
        ];
        $tagwriter->tag_data = $tagData;
        if ($tagwriter->WriteTags()) {
            echo "Tags written successfully.";
        } else {
            echo "Failed to write tags: " . implode(', ', $tagwriter->errors);
        }
    }

    private function sanitizeFileName(string $fileName): string
    {
        // Remove any characters that are not allowed in file names
        $fileName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $fileName);
        $fileName = str_replace([' ', '_'], ' ', $fileName); // Replace underscores with spaces
        $fileName = preg_replace('/\s+/', ' ', $fileName); // Replace multiple spaces with a single space
        $fileName = str_replace('(OFFICIAL VIDEO)', '', $fileName); // Remove "(OFFICIAL VIDEO)"
        $fileName = str_replace('(OFFICIAL AUDIO)', '', $fileName); // Remove "(OFFICIAL AUDIO)"
        $fileName = str_replace('(LYRICS)', '', $fileName); // Remove "(LYRICS)"
        $fileName = str_replace('(AUDIO)', '', $fileName); // Remove "(AUDIO)"
        $fileName = str_replace('(OFFICIAL)', '', $fileName); // Remove "(OFFICIAL)"
        $fileName = str_replace('(HD)', '', $fileName); // Remove "(HD)"
        $fileName = str_replace('(HQ)', '', $fileName); // Remove "(HQ)"
        $fileName = str_replace('(EXPLICIT)', '', $fileName); // Remove "(EXPLICIT)"
        $fileName = str_replace('(EXPLICIT LYRICS)', '', $fileName); // Remove "(EXPLICIT LYRICS)"
        $fileName = str_replace('(EXPLICIT AUDIO)', '', $fileName); // Remove "(EXPLICIT AUDIO)"
        $fileName = str_replace('(EXPLICIT OFFICIAL)', '', $fileName); // Remove "(EXPLICIT OFFICIAL)"
        $fileName = str_replace('(EXPLICIT OFFICIAL VIDEO)', '', $fileName); // Remove "(EXPLICIT OFFICIAL VIDEO)"
        $fileName = str_replace('(EXPLICIT OFFICIAL AUDIO)', '', $fileName); // Remove "(EXPLICIT OFFICIAL AUDIO)"
        $fileName = str_replace('(EXPLICIT HD)', '', $fileName); // Remove "(EXPLICIT HD)"
        $fileName = str_replace('(EXPLICIT HQ)', '', $fileName); // Remove "(EXPLICIT HQ)"
        $fileName = str_replace('(EXPLICIT LYRICS HD)', '', $fileName); // Remove "(EXPLICIT LYRICS HD)"
        $fileName = preg_replace('/^[\s\-]+/', '', $fileName); // usuwa spacje i myślniki z początku
        $fileName = preg_replace('/[\s\-]+$/', '', $fileName); // usuwa spacje i myślniki z końca
        $fileName = str_replace('Music', '', $fileName); // Remove "Music"
        $fileName = str_replace('Video', '', $fileName); // Remove "Video"
        $fileName = str_replace('Audio', '', $fileName); // Remove "Audio"
        $fileName = str_replace('Lyrics', '', $fileName); // Remove "Lyrics"
        $fileName = str_replace('HD', '', $fileName); // Remove "HD"
        $fileName = str_replace('HQ', '', $fileName); // Remove "HQ
        return $fileName;
    }

    private function getAlbumInfoFromLastFM(string $artist, string $songTitle): string
    {
        $apiKey = config('services.lastfm.api_key');

        $client = new \GuzzleHttp\Client();

        $api_url = 'http://ws.audioscrobbler.com/2.0/?method=track.getInfo&api_key=' . $apiKey . '&artist=' . urlencode($artist) . '&track=' . urlencode($songTitle) . '&format=json';

        $response = $client->get($api_url);

        $data = json_decode($response->getBody(), true);

        return $data['track']['album']['title'] ?? 'YT-DLP';
    }

}
