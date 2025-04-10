@extends('layout.layoutAdmin')
@section("title")
Admin Edit
@endsection

@section('content')
<div class="container">
    <h1>Edit Travel Package</h1>
    <form action="{{ route('admin.travel-package.update', $package->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Package Name</label>
            <input type="text" name="name" class="form-control" value="{{ $package->name }}" required>
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <select name="country" class="form-control" required>
                <option value="Indonesia" {{ $package->country == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                <option value="Thailand" {{ $package->country == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                <option value="Vietnam" {{ $package->country == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                <option value="South Korea" {{ $package->country == 'South Korea' ? 'selected' : '' }}>South Korea</option>
            </select>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" step="0.01" class="form-control" value="{{ $package->price }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" rows="4" required>{{ $package->description }}</textarea>
        </div>

        <!-- Itinerary -->
        <div class="form-group">
            <label for="itinerary">Itinerary</label>
            <div id="itinerary-container">
                @php
                    $itineraryData = is_string($package->itinerary) ? json_decode($package->itinerary, true) : $package->itinerary;
                @endphp
                @foreach ($itineraryData as $itinerary)
                    <textarea name="itinerary[]" class="form-control mb-2" rows="2">{{ $itinerary }}</textarea>
                @endforeach
            </div>
            <button type="button" id="add-itinerary" class="btn btn-secondary btn-sm">Add Itinerary Day</button>
        </div>

        <!-- PDF Upload for Itinerary -->
        <div class="form-group">
            <label for="itinerary_pdfs">Upload PDFs for Itinerary</label>
            <input type="file" name="itinerary_pdfs[]" multiple class="form-control-file">
            @if (!empty($package->itinerary_pdfs))
                <p>Current Itinerary PDFs:</p>
                <ul>
                    @php
                        $itineraryPdfs = is_string($package->itinerary_pdfs) ? json_decode($package->itinerary_pdfs, true) : $package->itinerary_pdfs;
                    @endphp
                    @foreach ($itineraryPdfs as $pdf)
                        <li>
                            <a href="{{ asset('storage/' . $pdf) }}" target="_blank">{{ basename($pdf) }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Include -->
        <div class="form-group">
            <label for="include">Include</label>
            <div id="include-container">
                @php
                    $includeData = is_string($package->include) ? json_decode($package->include, true) : $package->include;
                @endphp
                @foreach ($includeData as $include)
                    <textarea name="include[]" class="form-control mb-2" rows="2">{{ $include }}</textarea>
                @endforeach
            </div>
            <button type="button" id="add-include" class="btn btn-secondary btn-sm">Add Include</button>
        </div>

        <!-- PDF Upload for Include -->
        <div class="form-group">
            <label for="include_pdfs">Upload PDFs for Include</label>
            <input type="file" name="include_pdfs[]" multiple class="form-control-file">
            @if (!empty($package->include_pdfs))
                <p>Current Include PDFs:</p>
                <ul>
                    @php
                        $includePdfs = is_string($package->include_pdfs) ? json_decode($package->include_pdfs, true) : $package->include_pdfs;
                    @endphp
                    @foreach ($includePdfs as $pdf)
                        <li>
                            <a href="{{ asset('storage/' . $pdf) }}" target="_blank">{{ basename($pdf) }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Exclude -->
        <div class="form-group">
            <label for="exclude">Exclude</label>
            <div id="exclude-container">
                @php
                    $excludeData = is_string($package->exclude) ? json_decode($package->exclude, true) : $package->exclude;
                @endphp
                @foreach ($excludeData as $exclude)
                    <textarea name="exclude[]" class="form-control mb-2" rows="2">{{ $exclude }}</textarea>
                @endforeach
            </div>
            <button type="button" id="add-exclude" class="btn btn-secondary btn-sm">Add Exclude</button>
        </div>

        <!-- PDF Upload for Exclude -->
        <div class="form-group">
            <label for="exclude_pdfs">Upload PDFs for Exclude</label>
            <input type="file" name="exclude_pdfs[]" multiple class="form-control-file">
            @if (!empty($package->exclude_pdfs))
                <p>Current Exclude PDFs:</p>
                <ul>
                    @php
                        $excludePdfs = is_string($package->exclude_pdfs) ? json_decode($package->exclude_pdfs, true) : $package->exclude_pdfs;
                    @endphp
                    @foreach ($excludePdfs as $pdf)
                        <li>
                            <a href="{{ asset('storage/' . $pdf) }}" target="_blank">{{ basename($pdf) }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Available Dates -->
        <div class="form-group">
            <label for="available_dates">Available Dates</label>
            <div id="available-dates-container">
                @php
                    $availableDates = is_string($package->available_dates) ? json_decode($package->available_dates, true) : $package->available_dates;
                @endphp
                @foreach ($availableDates as $date)
                    <input type="date" name="available_dates[]" class="form-control mb-2" value="{{ $date }}">
                @endforeach
            </div>
            <button type="button" id="add-available-date" class="btn btn-secondary btn-sm">Add Available Date</button>
        </div>

        <!-- Image -->
        <div class="form-group">
            <label for="image">Main Image</label>
            <input type="file" name="image" class="form-control-file">
            <p>Current Image: <img src="{{ asset('storage/' . $package->image) }}" width="100"></p>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

@section('scripts')
@include('partials.dynamic-fields-js')
@endsection
