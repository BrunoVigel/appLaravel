<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}} Following">
  @include('following-only')
</x-profile>