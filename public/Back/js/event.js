$(document).ready(function() {
    $('#eventModal').on('show.bs.modal', function(e) {
        var eventId = $(e.relatedTarget).data('event-id');
        var modal = $(this);
        
        $.ajax({
            url: '/event/details/' + eventId,
            method: 'GET',
            success: function(data) {
                modal.find('.modal-body').html('<p>' + data.description + '</p>');
                // add other fields as needed
            }
        });
    });
});
