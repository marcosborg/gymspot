<section id="faqs" class="mb-5 pb-5 bg-gray pt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-title">
                    <div class="divider mb-3"></div>
                    <h2>FAQS</h2>
                    <p>Dúvidas que eventualmente tenha.</p>
                </div>
            </div>
        </div>
        @foreach ($faq_categories as $faq_category)
        <div class="accordion" id="accordion_{{ $faq_category->id }}">
            @foreach ($faq_category->faq_questions as $faq_question)
            <div class="card">
                <div class="card-header" id="heading_{{ $faq_question->id }}">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left" type="button" data-toggle="collapse"
                            data-target="#collapse_{{ $faq_question->id }}" aria-expanded="true"
                            aria-controls="collapse_{{ $faq_question->id }}">
                            {{ $faq_question->question }}
                        </button>
                    </h2>
                </div>

                <div id="collapse_{{ $faq_question->id }}" class="collapse"
                    aria-labelledby="heading_{{ $faq_question->id }}" data-parent="#accordion_{{ $faq_category->id }}">
                    <div class="card-body">
                        {{ $faq_question->answer }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</section>