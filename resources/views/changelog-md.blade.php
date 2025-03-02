# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
@isset($changelog)
@foreach ($changelog as $release => $changelogItems)
## [{{ ucfirst($release) }}]@if ($changelogItems['date'])
    - {{ \Carbon\Carbon::parse($changelogItems['date'])->format('Y-m-d') }}
@endif

@foreach ($changelogItems as $changeType => $items)
@if (is_array($items))

### {{ ucfirst($changeType) }}

@foreach ($items as $item)
    - {{ $item['message'] }}
@endforeach
@endif
@endforeach

@endforeach
@endisset


