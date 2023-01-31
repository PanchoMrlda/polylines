<section class="row col-10 col-offset-10 col-md-4 col-md-offset-4 mx-auto">
    <div class="alert alert-danger alert-dismissible d-none text-center" role="alert">
        <span class="">{{ $dynamoDbData['message'] ?? null }}</span>
        <button type="button" class="close" onclick="closeAlert(this)" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</section>