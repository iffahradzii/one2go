@extends('layout.layout')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Hero Section with Background -->
            <div class="text-center mb-5 p-4 rounded bg-light shadow-sm">
                <h1 class="display-4 fw-bold text-primary mb-3">Frequently Asked Questions</h1>
                <p class="lead">Find answers to the most common questions about our travel packages and services</p>
                <div class="d-flex justify-content-center mt-4">
                    <div class="input-group mb-0" style="max-width: 500px;">
                        <input type="text" id="faqSearch" class="form-control" placeholder="Search FAQs..." aria-label="Search FAQs">
                        <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Categories with Improved Styling -->
            <div class="d-flex justify-content-center mb-4">
                <div class="btn-group shadow-sm" role="group" aria-label="FAQ Categories">
                    <button type="button" class="btn btn-outline-primary active px-4" data-category="all">
                        <i class="fas fa-list-ul me-2"></i>All
                    </button>
                    <button type="button" class="btn btn-outline-primary px-4" data-category="booking">
                        <i class="fas fa-calendar-check me-2"></i>Booking
                    </button>
                    <button type="button" class="btn btn-outline-primary px-4" data-category="payment">
                        <i class="fas fa-credit-card me-2"></i>Payment
                    </button>
                    <button type="button" class="btn btn-outline-primary px-4" data-category="travel">
                        <i class="fas fa-plane me-2"></i>Travel
                    </button>
                </div>
            </div>
            
            <!-- FAQs Accordion with Enhanced Styling -->
            <div class="accordion shadow rounded" id="faqAccordion">
                @forelse($faqs as $index => $faq)
                    <div class="accordion-item faq-item" data-category="{{ $faq->type }}" data-index="{{ $index }}">
                        <h2 class="accordion-header" id="heading{{ $faq->id }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $faq->id }}">
                                <div class="d-flex align-items-center w-100">
                                    <div class="category-indicator me-3" style="background-color: {{ $faq->type == 'all' ? '#212529' : ($faq->type == 'booking' ? '#0d6efd' : ($faq->type == 'payment' ? '#198754' : '#dc3545')) }}"></div>
                                    <span class="faq-question">{{ $faq->question }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <div class="faq-answer">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                    <span class="badge rounded-pill text-white py-2 px-3" style="background-color: {{ $faq->type == 'all' ? '#212529' : ($faq->type == 'booking' ? '#0d6efd' : ($faq->type == 'payment' ? '#198754' : '#dc3545')) }}">
                                        <i class="fas fa-tag me-1"></i> {{ ucfirst($faq->type) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <p class="lead">No FAQs available at the moment. Please check back later.</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Contact Section with Enhanced Design -->
            <div class="mt-5 text-center p-5 bg-light rounded shadow">
                <div class="row align-items-center">
                    <div class="col-md-8 mx-auto">
                        <h3 class="h4 mb-3">Still have questions?</h3>
                        <p class="mb-4">Our customer support team is here to help you with any questions you might have.</p>
                        <a href="{{ route('about.us') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-envelope me-2"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enhanced Styling */
    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.05);
        color: #0d6efd;
        font-weight: 600;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(13, 110, 253, 0.25);
    }
    
    .faq-item {
        transition: all 0.3s ease;
        border-left: none;
        border-right: none;
    }
    
    .faq-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    
    #faqSearch:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .category-indicator {
        width: 8px;
        height: 24px;
        border-radius: 4px;
    }
    
    .accordion-button {
        padding: 1rem 1.25rem;
    }
    
    .accordion-body {
        background-color: #f8f9fa;
        padding: 1.5rem;
    }
    
    .btn-group .btn {
        transition: all 0.3s ease;
    }
    
    .btn-group .btn.active {
        font-weight: 600;
    }
    
    /* Hide FAQs by default for filtering */
    .faq-item.filtered {
        display: none;
    }
</style>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Store original order of FAQ items
        var $faqItems = $('.faq-item');
        var originalOrder = [];
        
        $faqItems.each(function() {
            originalOrder.push($(this));
        });
        
        // Search functionality
        $("#faqSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".faq-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
            
            // Show message if no results
            if ($(".faq-item:visible").length === 0) {
                if ($("#noResults").length === 0) {
                    $("#faqAccordion").append('<div id="noResults" class="text-center py-4"><i class="fas fa-search fa-2x text-muted mb-3"></i><p>No FAQs match your search. Try different keywords.</p></div>');
                }
            } else {
                $("#noResults").remove();
            }
        });
        
        // Category filter with improved implementation
        $('[data-category]').click(function() {
            $('[data-category]').removeClass('active');
            $(this).addClass('active');
            
            var category = $(this).data('category');
            
            // First, hide all items with CSS (no animation)
            $('.faq-item').addClass('filtered');
            
            // Then show matching items with animation
            if (category === 'all') {
                $('.faq-item').removeClass('filtered').fadeIn(300);
            } else {
                $('.faq-item[data-category="' + category + '"]').removeClass('filtered').fadeIn(300);
            }
            
            // Show message if no results in category
            if ($(".faq-item:not(.filtered)").length === 0) {
                if ($("#noResults").length === 0) {
                    $("#faqAccordion").append('<div id="noResults" class="text-center py-4"><i class="fas fa-filter fa-2x text-muted mb-3"></i><p>No FAQs in this category. Try another category.</p></div>');
                }
            } else {
                $("#noResults").remove();
            }
        });
    });
</script>
@endsection