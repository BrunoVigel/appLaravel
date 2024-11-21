<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}} Followers">
  @include('followers-only')    
</x-profile>