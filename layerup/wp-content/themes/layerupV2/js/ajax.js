jQuery(document).ready(function($) {
    $('.category-link').on('click', function(e) {
        e.preventDefault();
        
        var category_id = $(this).data('id');
        
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_posts_by_category',
                category_id: category_id
            },
            success: function(response) {
                $('#post-list').html(response);
            },
            error: function() {
                alert('Erro ao carregar os posts.');
            }
        });
    });
});
