@extends('layout.layoutAdmin')
@section("title")
Admin Create
@endsection

@section('content')
<div class="container">
    <h1>Create Travel Package</h1>
    <form action="{{ route('admin.travel-package.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">Package Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <select name="country" class="form-control" required>
                <option value="Indonesia">Indonesia</option>
                <option value="Thailand">Thailand</option>
                <option value="Vietnam">Vietnam</option>
                <option value="South Korea">South Korea</option>
            </select>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <!-- Multi-line fields for Itinerary -->
        <div class="form-group">
            <label for="itinerary">Itinerary</label>
            <div id="itinerary-container">
                <textarea name="itinerary[]" class="form-control mb-2" rows="2" placeholder="Day 1: Activities"></textarea>
            </div>
            <button type="button" id="add-itinerary" class="btn btn-secondary btn-sm">Add Itinerary Day</button>
        </div>
        <div class="form-group">
            <label for="itinerary_pdfs">Upload PDFs for Itinerary</label>
            <input type="file" name="itinerary_pdfs[]" multiple class="form-control-file">
        </div>

        <!-- Multi-line fields for Include -->
        <div class="form-group">
            <label for="include">Include</label>
            <div id="include-container">
                <textarea name="include[]" class="form-control mb-2" rows="2" placeholder="Include item"></textarea>
            </div>
            <button type="button" id="add-include" class="btn btn-secondary btn-sm">Add Include</button>
        </div>
        <div class="form-group">
            <label for="include_pdfs">Upload PDFs for Include</label>
            <input type="file" name="include_pdfs[]" multiple class="form-control-file">
        </div>

        <!-- Multi-line fields for Exclude -->
        <div class="form-group">
            <label for="exclude">Exclude</label>
            <div id="exclude-container">
                <textarea name="exclude[]" class="form-control mb-2" rows="2" placeholder="Exclude item"></textarea>
            </div>
            <button type="button" id="add-exclude" class="btn btn-secondary btn-sm">Add Exclude</button>
        </div>
        <div class="form-group">
            <label for="exclude_pdfs">Upload PDFs for Exclude</label>
            <input type="file" name="exclude_pdfs[]" multiple class="form-control-file">
        </div>

        <!-- Available Dates -->
        <div class="form-group">
            <label for="available_dates">Available Dates</label>
            <div id="available-dates-container">
                <input type="date" name="available_dates[]" class="form-control mb-2">
            </div>
            <button type="button" id="add-available-date" class="btn btn-secondary btn-sm">Add Available Date</button>
        </div>

        <div class="form-group">
            <label for="image">Main Image</label>
            <input type="file" name="image" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

@endsection

@section('scripts')
    @include('partials.dynamic-fields-js')
@endsection
