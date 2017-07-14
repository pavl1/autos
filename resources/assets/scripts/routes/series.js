export default {
    init() {
        $('.series-link').on('click', function(e) {
            $.ajax({
                type: 'POST',
                url: window.wp_data.ajax,
                data: {
                    action: 'models',
                    data: e.currentTarget.dataset,
                },
                success: (response) => {
                    console.log(response);
                },
            });
        });
    },
};
