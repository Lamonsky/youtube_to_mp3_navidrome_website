<x-layout>
    <x-slot:title>
        Welcome
    </x-slot:title>

    <div class="flex flex-col items-center justify-center text-center">
        <div class="flex flex-col items-center justify-center text-center w-full max-w-2xl">            
            <form method="POST" action="/">
                @csrf
                <label class="label" for="search_query">
                    <span class="label-text">Search for a YouTube video:</span>
                </label>
                <input type="text" id="search_query" name="search_query" class="input input-bordered" placeholder="Enter video title or keywords">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>        
        </div>
        <div class="card bg-base-100 shadow mt-8 border-2 w-full max-w-2xl">
            <div class="card-body border-2"></div>
                @foreach ($response['items'] as $item)                
                    <form method="GET" action="/download">
                        <input type="hidden" name="video_id" value="{{ $item['id']['videoId'] }}">
                        <button type="submit" class="border p-4 mb-4 w-full text-left">                       
                            <p>{{ $item['snippet']['title'] }}</p>
                            <img src="{{ $item['snippet']['thumbnails']['default']['url'] }}" alt="Thumbnail">
                        </button>
                    </form>                
                @endforeach
            </div>
        </div>
    </div>
</x-layout>
