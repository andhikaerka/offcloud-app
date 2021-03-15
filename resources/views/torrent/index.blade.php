<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-wrap space-y-4 mb-5 sm:space-y-0 sm:space-x-4 text-center">
                <form action="{{ route('torrent.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="files[]" required multiple="multiple">
                    <button type="submit" class="ml-1 text-white font-bold p-2 rounded outline-none focus:outline-none mr-1 mb-1 bg-gray-800 active:bg-gray-700 text-sm shadow hover:shadow-lg">
                        Submit
                    </button>
                </form>
            </div>
    
            
            <!-- This example requires Tailwind CSS v2.0+ -->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Url
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Download Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Request Id
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($torrents as $torrent)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $torrent->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $torrent->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ $torrent->url }}" target="_blank">
                                        Download
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $torrent->download_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $torrent->request_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <form action="{{ route('torrent.destroy', ['torrent' => $torrent->id]) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-1 text-white font-bold p-2 rounded outline-none focus:outline-none mr-1 mb-1 bg-red-800 active:bg-red-700 text-sm shadow hover:shadow-lg">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>

                <div class="mt-2">
                    {{ $torrents->links() }}
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>
