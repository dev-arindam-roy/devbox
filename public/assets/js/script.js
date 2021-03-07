$(function () {
    // Open add category modal
    $('#addCategoryBtn').on('click', function () {
        $('.modal').modal('hide');
        $('#addCategoryModal').modal('show');
    });
});