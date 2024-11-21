<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}} Profile">
  @include('posts-only')
</x-profile>